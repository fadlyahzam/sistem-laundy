<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran #{{ $invoice->invoice_number }} - LaundryKu</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1e293b;
            background-color: #f1f5f9;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        @media print {
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .print-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            @page {
                size: A4;
                margin: 1.2cm;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8 antialiased">

    <!-- Top Action Bar (Hidden on print) -->
    <div class="no-print max-w-3xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            @php
                $backUrl = auth()->check() && auth()->user()->role === 'admin' 
                    ? route('admin.orders.show', $order->id_order) 
                    : route('pelanggan.pesanan.pay', ['id' => $order->id_order, 'tab' => 'qris']);
            @endphp
            <a href="{{ $backUrl }}" 
               id="btn-kembali"
               class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <span class="text-xs font-semibold text-slate-500">Kuitansi / Bukti Pembayaran Sah</span>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold transition shadow-md shadow-emerald-600/20 flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Kuitansi / Bukti Pembayaran</span>
            </button>
        </div>
    </div>

    <!-- Official Printable Payment Receipt Sheet -->
    <div class="print-container max-w-3xl mx-auto bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-200/80 space-y-7 relative overflow-hidden">
        
        <!-- Watermark Lunas Stamp -->
        <div class="absolute right-10 top-24 pointer-events-none select-none opacity-10 rotate-[-12deg]">
            <div class="border-8 border-emerald-600 rounded-3xl px-8 py-3 text-emerald-600 text-5xl font-black tracking-widest uppercase">
                PAID & VERIFIED
            </div>
        </div>

        <!-- 1. Header: Brand Logo & Title -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 pb-6 border-b-2 border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-md shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-tight">
                        Laundry<span class="text-[#3da4e0]">Ku</span>
                    </h1>
                    <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Kuitansi / Bukti Pembayaran Resmi</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Layanan Laundry & Antar Jemput Online Terpercaya</p>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-500 text-white shadow-sm">
                    <i class="fa-solid fa-check text-[11px]"></i>
                    <span>LUNAS DIVERIFIKASI</span>
                </span>
                <p class="text-xs text-slate-400 mt-1 font-mono">Status: Verified & Settlement</p>
            </div>
        </div>

        <!-- 2. Nominal Display Box -->
        <div class="p-6 rounded-3xl bg-emerald-50/70 border border-emerald-200 text-center space-y-1">
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-emerald-800 block">TOTAL PEMBAYARAN DITERIMA:</span>
            <span class="text-3xl sm:text-4xl font-black text-emerald-700 block">
                Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
            </span>
            <span class="text-xs font-bold text-slate-700 block italic pt-1 capitalize">
                # {{ $invoice->terbilang }} #
            </span>
        </div>

        <!-- 3. Transaction Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            
            <!-- Left: Transaction Info -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block border-b border-slate-200 pb-1">
                    DETAIL TRANSAKSI PEMBAYARAN
                </span>

                <div class="flex justify-between items-center py-0.5">
                    <span class="text-slate-500">Nomor Invoice:</span>
                    <span class="font-extrabold text-slate-800">#{{ $invoice->invoice_number }}</span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                    <span class="text-slate-500">Kode Pesanan:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $order->order_code }}</span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                    <span class="text-slate-500">Ref. Transaksi Gateway:</span>
                    <span class="font-mono text-[11px] font-bold text-slate-800 truncate max-w-[160px]">
                        {{ $payment->gateway_order_id ?? 'MIDTRANS-' . $order->order_code }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                    <span class="text-slate-500">Waktu Bayar:</span>
                    <span class="font-bold text-emerald-700">
                        {{ ($invoice->paid_at ?? $payment->paid_at ?? now())->translatedFormat('d F Y, H:i') }} WIB
                    </span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                    <span class="text-slate-500">Metode Bayar:</span>
                    <span class="font-bold text-slate-800">Midtrans QRIS Otomatis</span>
                </div>
            </div>

            <!-- Right: Payer Info -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block border-b border-slate-200 pb-1">
                    INFORMASI PELANGGAN & PENERIMA
                </span>

                <div class="flex justify-between items-center py-0.5">
                    <span class="text-slate-500">Nama Pelanggan:</span>
                    <span class="font-extrabold text-slate-900">{{ $order->customer_name }}</span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                    <span class="text-slate-500">WhatsApp / Telp:</span>
                    <span class="font-semibold text-slate-800">{{ $order->customer_phone }}</span>
                </div>
                <div class="py-0.5">
                    <span class="text-slate-500 block">Alamat Cucian:</span>
                    <span class="font-medium text-slate-800 line-clamp-2 mt-0.5">{{ $order->address_text }}</span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                    <span class="text-slate-500">Status Tagihan:</span>
                    <span class="font-black text-emerald-600 uppercase">LUNAS (SISA Rp 0)</span>
                </div>
            </div>

        </div>

        <!-- 4. Rincian Layanan Ringkas -->
        <div class="p-5 rounded-2xl border border-slate-200/80 space-y-3">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">
                RINGKASAN ITEM CUCIAN YANG DILUNASI:
            </span>

            <div class="divide-y divide-slate-100 text-xs">
                @foreach($order->items as $item)
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800">{{ $item->layanan->name }}</span>
                            @if($item->kategori)
                                <span class="text-slate-500 text-[11px] block">+ {{ $item->kategori->name }}</span>
                            @endif
                            <span class="text-slate-400 text-[10px]">
                                {{ number_format($item->quantity, 1, ',', '.') }} {{ $item->layanan->service_type === 'kiloan' ? 'Kg' : 'Pcs' }} × Rp {{ number_format($item->price_snapshot, 0, ',', '.') }}
                            </span>
                        </div>
                        <span class="font-black text-slate-900">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
                
                <div class="py-2.5 flex items-center justify-between text-slate-500">
                    <span>Layanan Antar-Jemput (Radius {{ $order->distance_km ?? 0 }} KM)</span>
                    <span class="font-bold text-emerald-600 uppercase">GRATIS</span>
                </div>
            </div>
        </div>

        <!-- 5. Footer Sign-off & System Verification -->
        <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-6 text-xs text-slate-500">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700 text-xl font-bold">
                    <i class="fa-solid fa-stamp"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Validasi Digital:</span>
                    <span class="font-mono text-[11px] font-bold text-slate-700 block">PAY-{{ strtoupper(substr(md5($order->order_code . ($invoice->paid_at ?? now())), 0, 16)) }}</span>
                    <span class="text-[10px] text-emerald-600 font-semibold">Telah terverifikasi sah otomatis oleh payment gateway.</span>
                </div>
            </div>

            <div class="text-center sm:text-right space-y-1">
                <span class="text-[11px] text-slate-400">Penerima Pembayaran,</span>
                <div class="h-10 flex items-center justify-end">
                    <span class="px-3 py-1 rounded border-2 border-dashed border-emerald-500 text-emerald-700 font-black text-[11px] uppercase tracking-wider">
                        ✓ LUNAS - SISTEM RESMI
                    </span>
                </div>
                <p class="font-black text-slate-800 text-xs">LaundryKu Finance</p>
            </div>
        </div>

        <!-- Thank you note -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-500 italic">
                Terima kasih telah menggunakan jasa laundry kami. Cucian Anda sedang diproses dengan higienis dan rapi.
            </p>
        </div>

    </div>

    <!-- Auto Print & Navigation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnKembali = document.getElementById('btn-kembali');
            if (btnKembali && document.referrer) {
                try {
                    const ref = new URL(document.referrer);
                    if (ref.origin === window.location.origin && ref.pathname !== window.location.pathname) {
                        btnKembali.href = document.referrer;
                    }
                } catch (e) {}
            }
        });

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === '1' || urlParams.get('auto_print') === '1') {
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 400);
            });
        }
    </script>
</body>
</html>
