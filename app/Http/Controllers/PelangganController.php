<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Kategori;
use App\Models\Layanan;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\StatusLog;
use App\Services\DistanceService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PelangganController extends Controller
{
    protected DistanceService $distanceService;
    protected MidtransService $midtransService;

    public function __construct(DistanceService $distanceService, MidtransService $midtransService)
    {
        $this->distanceService = $distanceService;
        $this->midtransService = $midtransService;
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Get latest active order
        $activeOrder = Order::with(['items.layanan', 'pickupAssignment.driver.user', 'deliveryAssignment.driver.user', 'invoice'])
            ->where('id_user', $user->id_user)
            ->where('status', '!=', Order::STATUS_SELESAI)
            ->latest('id_order')
            ->first();

        // Services list for showcase
        $layananList = Layanan::with('kategori')->where('is_active', true)->get();

        // Recent completed orders
        $recentCompleted = Order::where('id_user', $user->id_user)
            ->where('status', Order::STATUS_SELESAI)
            ->latest('id_order')
            ->take(5)
            ->get();

        return view('pelanggan.dashboard', compact('user', 'activeOrder', 'layananList', 'recentCompleted'));
    }

    public function createOrder()
    {
        $layananList = Layanan::with(['kategori' => function ($q) {
            $q->where('is_active', true);
        }])->where('is_active', true)->get();

        return view('pelanggan.create_order', compact('layananList'));
    }

    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'id_layanan' => 'required|exists:layanan,id_layanan',
            'id_kategori' => 'nullable|exists:kategori,id_kategori',
            'quantity' => 'required|numeric|min:0.5',
            'address_text' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'pickup_schedule' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $lat = (float) $validated['latitude'];
        $lng = (float) $validated['longitude'];

        // Strict 20 KM Distance Validation via DistanceService
        try {
            $distanceKm = $this->distanceService->ensureWithinRadius($lat, $lng);
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', DistanceService::OUT_OF_RANGE_MESSAGE);
        }

        $layanan = Layanan::findOrFail($validated['id_layanan']);
        $kategoriId = $validated['id_kategori'] ?? null;
        $kategori = $kategoriId ? Kategori::find($kategoriId) : null;

        // Pricing calculation
        $basePrice = (float) $layanan->price_per_kg;
        $kategoriPrice = $kategori ? (float) $kategori->unit_tariff : 0;
        $unitPrice = $basePrice + $kategoriPrice;
        $qty = (float) $validated['quantity'];
        $subtotal = $unitPrice * $qty;

        $orderCode = 'LK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        DB::beginTransaction();
        try {
            // Create Order
            $order = Order::create([
                'id_user' => $user->id_user,
                'order_code' => $orderCode,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'address_text' => $validated['address_text'],
                'latitude' => $lat,
                'longitude' => $lng,
                'distance_km' => $distanceKm,
                'notes' => $validated['notes'] ?? null,
                'pickup_schedule' => Carbon::parse($validated['pickup_schedule']),
                'status' => Order::STATUS_MENUNGGU_KONFIRMASI,
            ]);

            // Create Order Item
            OrderItem::create([
                'id_order' => $order->id_order,
                'id_layanan' => $layanan->id_layanan,
                'id_kategori' => $kategori?->id_kategori,
                'quantity' => $qty,
                'price_snapshot' => $unitPrice,
                'subtotal' => $subtotal,
            ]);

            // Create Initial Status Log
            StatusLog::create([
                'id_order' => $order->id_order,
                'id_user' => $user->id_user,
                'from_status' => null,
                'to_status' => Order::STATUS_MENUNGGU_KONFIRMASI,
                'note' => 'Pesanan baru dibuat oleh pelanggan (Jarak: ' . $distanceKm . ' KM).',
                'changed_at' => Carbon::now(),
            ]);

            // Create Notification
            Notification::create([
                'id_user' => $user->id_user,
                'id_order' => $order->id_order,
                'title' => 'Pesanan Berhasil Dibuat!',
                'message' => "Pesanan {$orderCode} telah diterima. Kami sedang mengonfirmasi jadwal penjemputan driver.",
                'is_read' => false,
            ]);

            DB::commit();

            return redirect()->route('pelanggan.orders.show', $order->id_order)
                ->with('success', 'Pesanan Anda berhasil dibuat! Admin kami akan segera menugaskan driver untuk pickup.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function orders()
    {
        $orders = Order::with(['items.layanan', 'invoice'])
            ->where('id_user', Auth::id())
            ->latest('id_order')
            ->paginate(10);

        return view('pelanggan.orders_index', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = Order::with([
            'items.layanan',
            'items.kategori',
            'pickupAssignment.driver.user',
            'deliveryAssignment.driver.user',
            'invoice.latestPayment',
            'statusLogs.user'
        ])
        ->where('id_user', Auth::id())
        ->findOrFail($id);

        return view('pelanggan.order_detail', compact('order'));
    }

    public function payOrder($id)
    {
        $order = Order::with('invoice.latestPayment')
            ->where('id_user', Auth::id())
            ->findOrFail($id);

        $invoice = $order->invoice;

        if (!$invoice) {
            return back()->with('error', 'Tagihan belum diterbitkan oleh pihak outlet.');
        }

        if ($invoice->status === 'paid') {
            return redirect()->route('pelanggan.orders.show', $order->id_order)
                ->with('success', 'Tagihan ini sudah lunas.');
        }

        // Get or generate Midtrans QRIS charge
        $latestPayment = $invoice->latestPayment;

        if (!$latestPayment || $latestPayment->status !== 'pending' || ($latestPayment->expires_at && $latestPayment->expires_at->isPast())) {
            $chargeResult = $this->midtransService->createQrisCharge($invoice, 30);
            $latestPayment = $chargeResult['payment'];
        }

        return view('pelanggan.payment_qris', compact('order', 'invoice', 'latestPayment'));
    }

    public function simulatePaymentSuccess($id)
    {
        $order = Order::with('invoice.latestPayment')
            ->where('id_user', Auth::id())
            ->findOrFail($id);

        $invoice = $order->invoice;

        if (!$invoice || !$invoice->latestPayment) {
            return back()->with('error', 'Pembayaran tidak ditemukan.');
        }

        $payment = $invoice->latestPayment;

        // Process webhook notification logic directly for simulation
        $this->midtransService->processNotification([
            'order_id' => $payment->gateway_order_id,
            'status_code' => '200',
            'gross_amount' => (string) round($invoice->total_amount),
            'signature_key' => 'mock-simulated-signature',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ]);

        return redirect()->route('pelanggan.orders.show', $order->id_order)
            ->with('success', 'Simulasi Pembayaran Berhasil! Status pesanan kini DIBAYAR.');
    }

    public function profile()
    {
        $user = Auth::user();
        $totalOrders = Order::where('id_user', $user->id_user)->count();
        $completedOrders = Order::where('id_user', $user->id_user)->where('status', Order::STATUS_SELESAI)->count();

        return view('pelanggan.profile', compact('user', 'totalOrders', 'completedOrders'));
    }

    public function notifications()
    {
        $user = Auth::user();
        $notifications = Notification::where('id_user', $user->id_user)->latest('id_notification')->get();

        // Mark as read
        Notification::where('id_user', $user->id_user)->where('is_read', false)->update(['is_read' => true]);

        return view('pelanggan.notifications', compact('notifications'));
    }
}
