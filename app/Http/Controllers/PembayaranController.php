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
            $order = Order::with('invoice.latestPayment')
                ->where('id_user', Auth::id())
                ->findOrFail($order);
        } else {
            $order->loadMissing('invoice.latestPayment');
        }

        $invoice = $order->invoice;

        if (!$invoice) {
            return back()->with('error', 'Tagihan belum diterbitkan oleh pihak outlet.');
        }

        if ($invoice->status === 'paid') {
            return redirect()->route('pelanggan.orders.show', $order->id_order)
                ->with('success', 'Tagihan ini sudah lunas.');
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

            // 1. Update payment status -> 'paid' and set paid_at -> now()
            if ($payment) {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => $now,
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

            // 4. Create status_log entry: "Pembayaran berhasil diselesaikan (Mode Simulasi Demo)"
            StatusLog::create([
                'id_order' => $order->id_order,
                'id_user' => Auth::id(),
                'from_status' => $fromStatus,
                'to_status' => Order::STATUS_DIBAYAR,
                'note' => 'Pembayaran berhasil diselesaikan (Mode Simulasi Demo)',
                'changed_at' => $now,
            ]);

            // Create notification for customer
            Notification::create([
                'id_user' => $order->id_user,
                'id_order' => $order->id_order,
                'title' => 'Pembayaran Berhasil (Demo)!',
                'message' => "Pembayaran pesanan {$order->order_code} telah dikonfirmasi via mode simulasi demo.",
                'is_read' => false,
            ]);

            DB::commit();

            // 5. Redirect to pelanggan.pesanan.detail with success message: "Pembayaran Berhasil Diselesaikan (Mode Demo)!"
            return redirect()->route('pelanggan.pesanan.detail', $order->id_order)
                ->with('success', 'Pembayaran Berhasil Diselesaikan (Mode Demo)!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses simulasi pembayaran: ' . $e->getMessage());
        }
    }
}
