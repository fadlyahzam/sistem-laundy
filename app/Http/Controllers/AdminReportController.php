<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminReportController extends Controller
{
    /**
     * Display the financial and order report dashboard with period filtering.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Order::with(['user', 'items.layanan', 'invoice', 'pickupAssignment.driver.user', 'deliveryAssignment.driver.user'])
            ->latest('id_order');

        if ($startDate) {
            $query->whereDate('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $orders = $query->paginate(15)->withQueryString();

        // Statistics query based on same period
        $statsQuery = Order::with('invoice');
        if ($startDate) {
            $statsQuery->whereDate('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $statsQuery->whereDate('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }
        $allPeriodOrders = $statsQuery->get();

        $totalPaidRevenue = $allPeriodOrders->sum(function ($order) {
            return $order->invoice && $order->invoice->status === 'paid' ? (float) $order->invoice->total_amount : 0;
        });

        $totalOrdersCount = $allPeriodOrders->count();
        $completedOrdersCount = $allPeriodOrders->where('status', Order::STATUS_SELESAI)->count();
        $pendingPaymentOrdersCount = $allPeriodOrders->filter(function ($order) {
            return $order->invoice && $order->invoice->status === 'unpaid';
        })->count();

        return view('admin.laporan', compact(
            'orders',
            'totalPaidRevenue',
            'totalOrdersCount',
            'completedOrdersCount',
            'pendingPaymentOrdersCount',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export reports as structured CSV / Excel spreadsheet.
     * GET /admin/laporan/export-excel
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Order::with(['user', 'items.layanan', 'invoice', 'pickupAssignment.driver.user', 'deliveryAssignment.driver.user'])
            ->latest('id_order');

        if ($startDate) {
            $query->whereDate('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $orders = $query->get();

        $filename = 'laporan-laundryku-' . ($startDate ? $startDate . '-to-' : '') . ($endDate ?? date('Y-m-d')) . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Write UTF-8 BOM for Microsoft Excel proper encoding
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header Row
            fputcsv($file, [
                'Kode Order',
                'Tanggal',
                'Nama Pelanggan',
                'Jenis Layanan',
                'Berat/Qty',
                'Total Biaya (Rp)',
                'Status Pembayaran',
                'Driver',
            ]);

            foreach ($orders as $order) {
                // Service Type
                $layananNames = $order->items->map(function ($item) {
                    return $item->layanan?->name ?? 'Layanan Laundry';
                })->unique()->filter()->implode(', ');
                if (empty($layananNames)) {
                    $layananNames = 'Reguler Kiloan';
                }

                // Weight / Qty
                $weightQty = $order->berat_total 
                    ? $order->berat_total . ' Kg'
                    : ($order->items->sum('quantity') > 0 ? $order->items->sum('quantity') . ' Pcs/Kg' : '-');

                // Total Amount
                $amount = $order->invoice?->total_amount ?? $order->items->sum('subtotal');

                // Payment Status
                $paymentStatus = $order->invoice && $order->invoice->status === 'paid' ? 'LUNAS' : 'BELUM LUNAS';

                // Driver
                $driverName = $order->pickupAssignment?->driver?->user?->name 
                    ?? $order->deliveryAssignment?->driver?->user?->name 
                    ?? 'Belum Ditugaskan';

                fputcsv($file, [
                    $order->order_code,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->customer_name ?? $order->user?->name ?? '-',
                    $layananNames,
                    $weightQty,
                    $amount,
                    $paymentStatus,
                    $driverName,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export printable PDF report document via Dompdf.
     * GET /admin/laporan/export-pdf
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Order::with(['user', 'items.layanan', 'invoice', 'pickupAssignment.driver.user', 'deliveryAssignment.driver.user'])
            ->latest('id_order');

        if ($startDate) {
            $query->whereDate('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $orders = $query->get();

        $totalPaidRevenue = $orders->sum(function ($order) {
            return $order->invoice && $order->invoice->status === 'paid' ? (float) $order->invoice->total_amount : 0;
        });
        $totalOrdersCount = $orders->count();
        $completedOrdersCount = $orders->where('status', Order::STATUS_SELESAI)->count();

        $periodText = ($startDate ? Carbon::parse($startDate)->translatedFormat('d M Y') : 'Awal') 
            . ' s/d ' 
            . ($endDate ? Carbon::parse($endDate)->translatedFormat('d M Y') : Carbon::now()->translatedFormat('d M Y'));

        $html = view('admin.report_pdf', compact(
            'orders',
            'totalPaidRevenue',
            'totalOrdersCount',
            'completedOrdersCount',
            'periodText',
            'startDate',
            'endDate'
        ))->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'laporan-laundryku-' . ($startDate ? $startDate . '-to-' : '') . ($endDate ?? date('Y-m-d')) . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }
}
