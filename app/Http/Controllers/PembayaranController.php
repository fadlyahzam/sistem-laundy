<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StatusLog;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Show QRIS payment view for customer order.
     */
    public function showPayment(Request $request, $order)
    {
        if (!$order instanceof Order) {
            $order = Order::with(['invoice.latestPayment', 'items.layanan', 'items.kategori'])
                ->where('id_user', Auth::id())
                ->findOrFail($order);
        } else {
            $order->loadMissing(['invoice.latestPayment', 'items.layanan', 'items.kategori']);
        }

        $invoice = $order->invoice;

        if (!$invoice) {
            return back()->with('error', 'Tagihan belum diterbitkan oleh pihak outlet.');
        }

        if ($invoice->status === 'paid') {
            $payment = $invoice->latestPayment;
            $latestPayment = $payment;
            $expiresAt = $payment?->expires_at;

            return view('pelanggan.pembayaran', compact('order', 'invoice', 'payment', 'latestPayment', 'expiresAt'));
        }

        // Get or generate Midtrans QRIS charge with 30-minute validity
        $payment = $invoice->latestPayment;
        $forceRegenerate = $request->boolean('regenerate');

        if ($forceRegenerate && $payment && $payment->status === 'pending') {
            $payment->update(['status' => 'expired']);
            $payment = null;
        }

        // Ensure expires_at is set to 30 minutes in the future when creating/refreshing
        if (!$payment || $payment->status !== 'pending' || !$payment->expires_at || $payment->expires_at->isPast()) {
            $chargeResult = $this->midtransService->createQrisCharge($invoice, 30);
            $payment = $chargeResult['payment'];
        }

        // Also alias latestPayment for backward compatibility with existing views
        $latestPayment = $payment;
        $expiresAt = $payment->expires_at;

        return view('pelanggan.pembayaran', compact('order', 'invoice', 'payment', 'latestPayment', 'expiresAt'));
    }

    /**
     * Show official invoice view for customer order (both paid & unpaid).
     */
    public function showInvoice(Request $request, $order)
    {
        return $this->showPayment($request, $order);
    }

    /**
     * Show printable formal invoice view.
     */
    public function printInvoice(Request $request, $order)
    {
        if (!$order instanceof Order) {
            $order = Order::with(['invoice.latestPayment', 'items.layanan', 'items.kategori', 'user'])
                ->where('id_user', Auth::id())
                ->findOrFail($order);
        } else {
            $order->loadMissing(['invoice.latestPayment', 'items.layanan', 'items.kategori', 'user']);
        }

        $invoice = $order->invoice;
        if (!$invoice) {
            return back()->with('error', 'Tagihan belum diterbitkan untuk pesanan ini.');
        }

        $payment = $invoice->latestPayment;
        return view('pelanggan.cetak_invoice', compact('order', 'invoice', 'payment'));
    }

    /**
     * Show printable formal payment receipt view.
     */
    public function printReceipt(Request $request, $order)
    {
        if (!$order instanceof Order) {
            $order = Order::with(['invoice.latestPayment', 'items.layanan', 'items.kategori', 'user'])
                ->where('id_user', Auth::id())
                ->findOrFail($order);
        } else {
            $order->loadMissing(['invoice.latestPayment', 'items.layanan', 'items.kategori', 'user']);
        }

        $invoice = $order->invoice;
        if (!$invoice) {
            return back()->with('error', 'Tagihan belum diterbitkan untuk pesanan ini.');
        }

        if ($invoice->status !== 'paid') {
            return redirect()->route('pelanggan.orders.pay', $order->id_order)
                ->with('error', 'Bukti pembayaran resmi hanya tersedia untuk tagihan yang telah lunas.');
        }

        $payment = $invoice->latestPayment;
        if (!$payment) {
            $payment = Payment::create([
                'id_invoice' => $invoice->id_invoice,
                'gateway_order_id' => 'PAY-' . $order->order_code . '-' . strtoupper(substr(uniqid(), -4)),
                'amount' => $invoice->total_amount,
                'status' => 'paid',
                'paid_at' => $invoice->paid_at ?? Carbon::now(),
            ]);
        }

        return view('pelanggan.cetak_bukti', compact('order', 'invoice', 'payment'));
    }

    /**
     * Demo simulation payment bypass.
     * POST /pelanggan/pesanan/{order}/simulate-pay
     */
    public function simulatePayment(Request $request, $order)
    {
        if (!$order instanceof Order) {
            $order = Order::with(['invoice.latestPayment', 'user'])
                ->where('id_user', Auth::id())
                ->findOrFail($order);
        } else {
            $order->loadMissing(['invoice.latestPayment', 'user']);
        }

        $invoice = $order->invoice;
        $payment = $invoice?->latestPayment;

        DB::beginTransaction();
        try {
            $fromStatus = $order->status;
            $now = Carbon::now();

            // 1. Update or create payment record with paid status & timestamp
            if ($payment) {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => $now,
                ]);
            } elseif ($invoice) {
                $payment = Payment::create([
                    'id_invoice' => $invoice->id_invoice,
                    'gateway_order_id' => 'DEMO-' . $order->order_code . '-' . strtoupper(substr(uniqid(), -4)),
                    'amount' => $invoice->total_amount,
                    'status' => 'paid',
                    'paid_at' => $now,
                    'qr_string' => 'demo-qris-simulated',
                ]);
            }

            // 2. Update invoice status -> 'paid' and set paid_at -> now()
            if ($invoice) {
                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => $now,
                ]);
            }

            // 3. Update order status -> 'DIBAYAR'
            $order->update([
                'status' => Order::STATUS_DIBAYAR,
            ]);

            // 4. Create status_log entry
            StatusLog::create([
                'id_order' => $order->id_order,
                'id_user' => Auth::id(),
                'from_status' => $fromStatus,
                'to_status' => Order::STATUS_DIBAYAR,
                'note' => 'Pembayaran berhasil diselesaikan via Midtrans QRIS (Mode Simulasi)',
                'changed_at' => $now,
            ]);

            // Create notification for customer
            Notification::create([
                'id_user' => $order->id_user,
                'id_order' => $order->id_order,
                'title' => 'Pembayaran Berhasil Dikonfirmasi!',
                'message' => "Pembayaran pesanan {$order->order_code} sebesar Rp " . number_format($invoice->total_amount, 0, ',', '.') . " telah sukses diterima.",
                'is_read' => false,
            ]);

            DB::commit();

            // 5. Redirect to payment page (showing bukti pelunasan) or order detail with success message
            return redirect()->route('pelanggan.pesanan.pay', $order->id_order)
                ->with('success', 'Pembayaran berhasil diselesaikan! Tagihan telah lunas.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses simulasi pembayaran: ' . $e->getMessage());
        }
    }
}
