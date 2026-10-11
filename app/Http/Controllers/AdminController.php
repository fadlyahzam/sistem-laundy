<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\Invoice;
use App\Models\Kategori;
use App\Models\Layanan;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalRevenue = Invoice::where('status', 'paid')->sum('total_amount');
        $activeOrdersCount = Order::where('status', '!=', Order::STATUS_SELESAI)->count();
        $pendingConfirmationCount = Order::where('status', Order::STATUS_MENUNGGU_KONFIRMASI)->count();
        $availableDriversCount = Driver::where('availability', 'available')->count();

        $recentOrders = Order::with(['items.layanan', 'user'])
            ->latest('id_order')
            ->take(8)
            ->get();

        $statusCounts = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'activeOrdersCount',
            'pendingConfirmationCount',
            'availableDriversCount',
            'recentOrders',
            'statusCounts'
        ));
    }

    public function orders(Request $request)
    {
        $query = Order::with(['items.layanan', 'user', 'invoice', 'pickupAssignment.driver.user', 'deliveryAssignment.driver.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest('id_order')->paginate(15)->withQueryString();

        return view('admin.orders_index', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = Order::with([
            'items.layanan',
            'items.kategori',
            'user',
            'invoice.latestPayment',
            'assignments.driver.user',
            'statusLogs.user'
        ])->findOrFail($id);

        $availableDrivers = Driver::with('user')->where('availability', '!=', 'offline')->get();

        return view('admin.order_detail', compact('order', 'availableDrivers'));
    }

    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'id_driver' => 'required|exists:drivers,id_driver',
            'type' => 'required|in:pickup,delivery',
        ]);

        $order = Order::findOrFail($id);
        $driver = Driver::with('user')->findOrFail($request->id_driver);

        DB::beginTransaction();
        try {
            $assignment = Assignment::create([
                'id_order' => $order->id_order,
                'id_driver' => $driver->id_driver,
                'type' => $request->type,
                'status' => 'pending',
                'assigned_at' => Carbon::now(),
            ]);

            $fromStatus = $order->status;
            $newStatus = $request->type === 'pickup' ? Order::STATUS_DRIVER_DITUGASKAN : Order::STATUS_DRIVER_DITUGASKAN;
            $order->update(['status' => $newStatus]);

            $driverName = $driver->user->name ?? 'Driver';
            $typeLabel = $request->type === 'pickup' ? 'Penjemputan' : 'Pengantaran';
            $note = "Driver {$driverName} telah ditugaskan untuk {$typeLabel}.";

            // Status Log
            StatusLog::create([
                'id_order' => $order->id_order,
                'id_user' => Auth::id(),
                'from_status' => $fromStatus,
                'to_status' => $newStatus,
                'note' => $note,
                'changed_at' => Carbon::now(),
            ]);

            // Notify Customer
            Notification::create([
                'id_user' => $order->id_user,
                'id_order' => $order->id_order,
                'title' => "Driver {$typeLabel} Ditugaskan",
                'message' => "Driver {$driverName} ({$driver->plate_number}) sedang menuju lokasi Anda untuk {$typeLabel} cucian.",
                'is_read' => false,
            ]);

            // Update driver availability to on_duty
            $driver->update(['availability' => 'on_duty']);

            DB::commit();

            return back()->with('success', "Driver {$driverName} berhasil ditugaskan untuk {$typeLabel}.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menugaskan driver: ' . $e->getMessage());
        }
    }

    public function issueInvoice(Request $request, $id)
    {
        $request->validate([
            'final_quantity' => 'required|numeric|min:0.1',
        ]);

        $order = Order::with('items')->findOrFail($id);
        $item = $order->items->first();

        DB::beginTransaction();
        try {
            $qty = (float) $request->final_quantity;
            $unitPrice = $item ? (float) $item->price_snapshot : 8000;
            $subtotal = $qty * $unitPrice;

            if ($item) {
                $item->update([
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ]);
            }

            $invoiceNumber = 'INV-LK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $invoice = Invoice::updateOrCreate(
                ['id_order' => $order->id_order],
                [
                    'invoice_number' => $invoiceNumber,
                    'total_amount' => $subtotal,
                    'status' => 'unpaid',
                    'issued_at' => Carbon::now(),
                ]
            );

            $fromStatus = $order->status;
            $order->update([
                'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
                'berat_total' => $qty,
            ]);

            $note = "Penimbangan selesai: {$qty} kg. Tagihan diterbitkan sebesar Rp " . number_format($subtotal, 0, ',', '.') . " (Status: Menunggu Pembayaran).";

            StatusLog::create([
                'id_order' => $order->id_order,
                'id_user' => Auth::id(),
                'from_status' => $fromStatus,
                'to_status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
                'note' => $note,
                'changed_at' => Carbon::now(),
            ]);

            Notification::create([
                'id_user' => $order->id_user,
                'id_order' => $order->id_order,
                'title' => 'Tagihan Laundry Siap Dibayar',
                'message' => "Cucian telah ditimbang ({$qty} kg). Total tagihan Anda Rp " . number_format($subtotal, 0, ',', '.') . ". Silakan bayar via QRIS.",
                'is_read' => false,
            ]);

            DB::commit();

            return back()->with('success', 'Tagihan berhasil diterbitkan dan status diubah ke Menunggu Pembayaran.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menerbitkan tagihan: ' . $e->getMessage());
        }
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:SEDANG_DIPROSES,SIAP_DIANTAR,SELESAI',
            'note' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);
        $targetStatus = $request->status;
        $fromStatus = $order->status;

        DB::beginTransaction();
        try {
            $order->update(['status' => $targetStatus]);

            $note = $request->note ?? ("Status pesanan diperbarui menjadi: " . $order->status_label);

            StatusLog::create([
                'id_order' => $order->id_order,
                'id_user' => Auth::id(),
                'from_status' => $fromStatus,
                'to_status' => $targetStatus,
                'note' => $note,
                'changed_at' => Carbon::now(),
            ]);

            Notification::create([
                'id_user' => $order->id_user,
                'id_order' => $order->id_order,
                'title' => 'Perubahan Status Pesanan',
                'message' => $note,
                'is_read' => false,
            ]);

            DB::commit();

            return back()->with('success', "Status berhasil diperbarui ke: {$order->status_label}");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    public function reports(Request $request)
    {
        return app(AdminReportController::class)->index($request);
    }

    public function products()
    {
        $layananList = Layanan::with('kategori')->latest('id_layanan')->get();
        return view('admin.products', compact('layananList'));
    }

    public function storeLayanan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'service_type' => 'required|in:kiloan,satuan',
            'price_per_kg' => 'required|numeric|min:0',
        ]);

        Layanan::create([
            'name' => $request->name,
            'service_type' => $request->service_type,
            'price_per_kg' => $request->price_per_kg,
            'is_active' => true,
        ]);

        return back()->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function updateLayanan(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'service_type' => 'required|in:kiloan,satuan',
            'price_per_kg' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $layanan->update($request->only('name', 'service_type', 'price_per_kg', 'is_active'));

        return back()->with('success', 'Layanan berhasil diperbarui.');
    }

    public function deleteLayanan($id)
    {
        $layanan = Layanan::findOrFail($id);
        $layanan->delete();
        return back()->with('success', 'Layanan berhasil dihapus.');
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'id_layanan' => 'required|exists:layanan,id_layanan',
            'name' => 'required|string|max:255',
            'unit_tariff' => 'required|numeric|min:0',
        ]);

        Kategori::create([
            'id_layanan' => $request->id_layanan,
            'name' => $request->name,
            'unit_tariff' => $request->unit_tariff,
            'is_active' => true,
        ]);

        return back()->with('success', 'Kategori/item khusus berhasil ditambahkan.');
    }

    public function deleteKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function drivers()
    {
        $drivers = Driver::with(['user', 'assignments' => function ($q) {
            $q->whereIn('status', ['pending', 'in_progress']);
        }])->get();

        return view('admin.drivers', compact('drivers'));
    }

    public function storeDriver(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'plate_number' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:50',
            'password' => 'required|min:6',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'driver',
                'is_active' => true,
            ]);

            Driver::create([
                'id_user' => $user->id_user,
                'plate_number' => $request->plate_number,
                'vehicle_type' => $request->vehicle_type,
                'availability' => 'available',
            ]);

            DB::commit();
            return back()->with('success', 'Driver baru berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan driver: ' . $e->getMessage());
        }
    }
}
