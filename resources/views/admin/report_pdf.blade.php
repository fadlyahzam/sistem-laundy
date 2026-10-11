<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi & Keuangan - LaundryKu</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .brand-subtitle {
            font-size: 11px;
            color: #0284c7;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .brand-desc {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .report-title-box {
            text-align: right;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }
        .report-meta {
            font-size: 10px;
            color: #64748b;
            margin-top: 3px;
        }

        /* Stats Cards Table */
        .stats-table {
            width: 100%;
            margin-bottom: 20px;
            border-spacing: 10px 0;
            margin-left: -10px;
            margin-right: -10px;
        }
        .stat-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            width: 33.33%;
        }
        .stat-label {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }
        .stat-val {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
        }
        .stat-val.revenue {
            color: #059669;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #0f172a;
        }
        .data-table td {
            padding: 7px 10px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-lunas {
            background-color: #dcfce7;
            color: #15803d;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #b45309;
        }

        /* Footer & Signature Block */
        .signature-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 250px;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #0f172a;
            font-weight: bold;
            padding-bottom: 4px;
        }
        .signature-role {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: top;">
                <div class="brand-subtitle">Outlet Pusat Antar-Jemput</div>
                <div class="brand-title">LaundryKu Express</div>
                <div class="brand-desc">
                    Jl. Babarsari No. 42, Sleman, D.I. Yogyakarta 55281<br>
                    Telp / WhatsApp: +62 812-3456-7890 &bull; Email: admin@laundryku.com
                </div>
            </td>
            <td class="report-title-box" style="vertical-align: top;">
                <div class="report-title">LAPORAN TRANSAKSI &amp; KEUANGAN</div>
                <div class="report-meta">
                    <strong>Periode:</strong> {{ $periodText }}<br>
                    <strong>Dicetak Pada:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB<br>
                    <strong>Operator:</strong> {{ auth()->user()->name ?? 'Administrator' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Summary Statistics Cards -->
    <table class="stats-table">
        <tr>
            <td class="stat-card">
                <div class="stat-label">Total Penerimaan Lunas</div>
                <div class="stat-val revenue">Rp {{ number_format($totalPaidRevenue, 0, ',', '.') }}</div>
            </td>
            <td class="stat-card">
                <div class="stat-label">Total Volume Transaksi</div>
                <div class="stat-val">{{ $totalOrdersCount }} Transaksi</div>
            </td>
            <td class="stat-card">
                <div class="stat-label">Pesanan Selesai / Terkirim</div>
                <div class="stat-val">{{ $completedOrdersCount }} Selesai</div>
            </td>
        </tr>
    </table>

    <!-- Main Orders Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 14%;">Kode Order</th>
                <th style="width: 13%;">Tanggal</th>
                <th style="width: 16%;">Nama Pelanggan</th>
                <th style="width: 15%;">Jenis Layanan</th>
                <th style="width: 10%;">Berat / Qty</th>
                <th style="width: 12%; text-align: right;">Total Biaya</th>
                <th style="width: 15%;">Driver</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                @php
                    $layananNames = $order->items->map(fn($item) => $item->layanan?->name ?? 'Layanan')->unique()->filter()->implode(', ');
                    if (empty($layananNames)) $layananNames = 'Reguler Kiloan';
                    $weight = $order->berat_total ? $order->berat_total . ' Kg' : ($order->items->sum('quantity') > 0 ? $order->items->sum('quantity') . ' Pcs/Kg' : '-');
                    $amount = $order->invoice?->total_amount ?? $order->items->sum('subtotal');
                    $isPaid = $order->invoice && $order->invoice->status === 'paid';
                    $driverName = $order->pickupAssignment?->driver?->user?->name ?? $order->deliveryAssignment?->driver?->user?->name ?? 'Belum Ditugaskan';
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $order->order_code }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $order->customer_name ?? $order->user?->name ?? '-' }}</strong></td>
                    <td>{{ $layananNames }}</td>
                    <td>{{ $weight }}</td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp {{ number_format($amount, 0, ',', '.') }}
                        <div style="margin-top: 2px;">
                            <span class="badge {{ $isPaid ? 'badge-lunas' : 'badge-pending' }}">
                                {{ $isPaid ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        </div>
                    </td>
                    <td>{{ $driverName }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #94a3b8;">
                        Tidak ada data transaksi pada periode yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Block -->
    <table class="signature-table">
        <tr>
            <td style="width: 60%;">
                <div style="font-size: 9px; color: #64748b; line-height: 1.5;">
                    * Dokumen ini dibuat secara resmi melalui sistem otomasi LaundryKu.<br>
                    * Seluruh data transaksi terekam otomatis dan terverifikasi oleh server Midtrans &amp; GPS Dispatch.
                </div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="signature-box" style="display: inline-block;">
                    <div>Yogyakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                    <div style="font-weight: bold; margin-top: 4px;">Mengetahui,</div>
                    <div class="signature-line">{{ auth()->user()->name ?? 'Manager Operasional' }}</div>
                    <div class="signature-role">Kepala Outlet &bull; LaundryKu Pusat</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
