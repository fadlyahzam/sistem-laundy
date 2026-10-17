<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }} - LaundryKu</title>
    
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

            .page-break {
                page-break-after: auto;
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
    <div class="no-print max-w-4xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            @php
                $backUrl = auth()->check() && auth()->user()->role === 'admin' 
                    ? route('admin.orders.show', $order->id_order) 
                    : route('pelanggan.pesanan.pay', ['id' => $order->id_order, 'tab' => 'invoice']);
            @endphp
            <a href="{{ $backUrl }}" 
               id="btn-kembali"
               class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <span class="text-xs font-semibold text-slate-500">Pratinjau Lembar Cetak Invoice Resmi</span>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white text-xs font-extrabold transition shadow-md shadow-[#3da4e0]/20 flex items-center gap-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Invoice / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Printable Invoice Sheet -->
    <div class="print-container max-w-4xl mx-auto bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-200/80 space-y-8 relative overflow-hidden">
        
        <!-- Watermark Status if Paid -->
        @if($invoice->status === 'paid')
            <div class="absolute right-12 top-28 pointer-events-none select-none opacity-10 rotate-[-15deg]">
                <div class="border-8 border-emerald-600 rounded-3xl px-8 py-4 text-emerald-600 text-6xl font-black tracking-widest uppercase">
                    LUNAS
                </div>
            </div>
        @endif

        <!-- 1. Header: Brand Logo & Company Info -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 pb-6 border-b-2 border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-[#3da4e0] text-white flex items-center justify-center text-2xl shadow-md shrink-0">
                    <i class="fa-solid fa-shirt"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-tight">
                        Laundry<span class="text-[#3da4e0]">Ku</span>
                    </h1>
                    <p class="text-xs font-bold text-[#3da4e0] uppercase tracking-wider">Layanan Laundry & Dry Cleaning Antar Jemput</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Jl. Raya Laundry No. 88, Pusat Layanan • Hotline/WA: 0812-3456-7890</p>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                    {{ $invoice->status === 'paid' ? '✓ LUNAS' : '⏳ MENUNGGU PEMBAYARAN' }}
                </span>
                <h2 class="text-xl font-extrabold text-slate-900 mt-1">FAKTUR TAGIHAN</h2>
                <p class="text-xs font-mono font-bold text-slate-600">No: {{ $invoice->invoice_number }}</p>
            </div>
        </div>

        <!-- 2. Meta Information Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
            
            <!-- Customer Details -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">DITAGIHKAN KEPADA:</span>
                <p class="text-sm font-black text-slate-900">{{ $order->customer_name }}</p>
                <p class="text-slate-600 font-medium">WhatsApp / Telp: <span class="font-bold text-slate-800">{{ $order->customer_phone }}</span></p>
                <p class="text-slate-600 font-medium leading-relaxed">
                    Alamat Penjemputan / Antar:<br>
                    <span class="text-slate-800 font-semibold">{{ $order->address_text }}</span>
                </p>
            </div>

            <!-- Invoice Specifics -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">RINCIAN TRANSAKSI:</span>
                <div class="flex justify-between items-center py-0.5">
                    <span class="text-slate-500">Kode Pesanan:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $order->order_code }}</span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                    <span class="text-slate-500">Tanggal Terbit:</span>
                    <span class="font-bold text-slate-800">{{ ($invoice->issued_at ?? $invoice->created_at)->translatedFormat('d F Y, H:i') }} WIB</span>
                </div>
                <div class="flex justify-between items-center py-0.5">
                    <span class="text-slate-500">Metode Pembayaran:</span>
                    <span class="font-bold text-slate-800">Midtrans QRIS Otomatis</span>
                </div>
                @if($invoice->status === 'paid')
                    <div class="flex justify-between items-center py-0.5 pt-1.5 border-t border-slate-200">
                        <span class="text-emerald-700 font-bold">Waktu Pelunasan:</span>
                        <span class="font-bold text-emerald-700">{{ ($invoice->paid_at ?? now())->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>
                @endif
            </div>

        </div>

        <!-- 3. Items Table -->
        <div class="space-y-2">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-500">Rincian Cucian & Layanan</h3>
            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Deskripsi Layanan</th>
                            <th class="py-3 px-4">Tipe / Kategori</th>
                            <th class="py-3 px-4 text-center">Berat / Qty</th>
                            <th class="py-3 px-4 text-right">Tarif Satuan</th>
                            <th class="py-3 px-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($order->items as $idx => $item)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-extrabold text-slate-900 block">{{ $item->layanan->name }}</span>
                                    @if($item->kategori)
                                        <span class="text-[11px] text-slate-500 block">+ Variasi: {{ $item->kategori->name }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                        {{ $item->layanan->service_type }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-slate-800">
                                    {{ number_format($item->quantity, 1, ',', '.') }} {{ $item->layanan->service_type === 'kiloan' ? 'Kg' : 'Pcs' }}
                                </td>
                                <td class="py-3 px-4 text-right text-slate-600 font-medium">
                                    Rp {{ number_format($item->price_snapshot, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-black text-slate-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-slate-400 italic">Tidak ada item tercatat.</td>
                            </tr>
                        @endforelse

                        <!-- Ongkir / Delivery row -->
                        <tr class="bg-slate-50/70">
                            <td class="py-3 px-4 text-center font-bold text-slate-400">•</td>
                            <td colspan="4" class="py-3 px-4 font-semibold text-slate-700">
                                Layanan Antar-Jemput Cucian Langsung ke Lokasi (Radius {{ $order->distance_km ?? 0 }} KM)
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-600 uppercase">
                                GRATIS
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Summary & Terbilang -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start pt-2">
            
            <!-- Left: Terbilang & Notes -->
            <div class="space-y-4">
                <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-100 text-xs space-y-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#167dc0] block">JUMLAH TERBILANG:</span>
                    <p class="font-extrabold text-slate-900 italic capitalize leading-relaxed">
                        # {{ $invoice->terbilang }} #
                    </p>
                </div>

                <div class="text-[10px] text-slate-500 space-y-1 leading-relaxed border-l-2 border-[#3da4e0] pl-3">
                    <p class="font-bold text-slate-700 uppercase tracking-wider">Syarat & Ketentuan Layanan:</p>
                    <p>1. Simpan lembar invoice ini sebagai bukti resmi pengambilan atau serah terima pakaian.</p>
                    <p>2. Klaim / komplain atas kelalaian pencucian wajib dilaporkan maksimal 1x24 jam sejak pakaian diterima.</p>
                    <p>3. Pakaian yang tidak diambil dalam kurun waktu 30 hari berada di luar tanggung jawab pihak laundry.</p>
                </div>
            </div>

            <!-- Right: Subtotal & Total Amount -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 text-xs">
                <div class="flex justify-between items-center text-slate-600">
                    <span>Subtotal Biaya Cucian:</span>
                    <span class="font-bold text-slate-800">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-slate-600">
                    <span>Biaya Antar-Jemput:</span>
                    <span class="font-bold text-emerald-600">Rp 0 (Gratis)</span>
                </div>
                <div class="flex justify-between items-center text-slate-600">
                    <span>Biaya Admin & Layanan:</span>
                    <span class="font-bold text-emerald-600">Rp 0</span>
                </div>
                
                <div class="pt-3 border-t-2 border-slate-200 flex justify-between items-baseline">
                    <span class="text-sm font-black text-slate-900 uppercase">TOTAL TAGIHAN:</span>
                    <span class="text-2xl font-black text-[#167dc0]">
                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                    </span>
                </div>

                <div class="pt-2 text-right">
                    <span class="text-[11px] font-bold {{ $invoice->status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $invoice->status === 'paid' ? '✓ Tagihan Telah Lunas' : '⏳ Menunggu Pelunasan QRIS' }}
                    </span>
                </div>
            </div>

        </div>

        <!-- 5. Sign-off & System Stamp Footer -->
        <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-6 text-xs text-slate-500">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 font-mono text-[9px] text-center border border-slate-200 leading-tight p-1">
                    [QR VERIFIKASI]
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Verifikasi Digital:</span>
                    <span class="font-mono text-[11px] font-bold text-slate-700 block">INV-{{ md5($invoice->invoice_number . $invoice->created_at) }}</span>
                    <span class="text-[10px] text-slate-400">Dicetak otomatis oleh Sistem LaundryKu pada {{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
                </div>
            </div>

            <div class="text-center sm:text-right space-y-1">
                <span class="text-[11px] text-slate-400">Hormat Kami,</span>
                <div class="h-10 flex items-center justify-end">
                    <span class="px-3 py-1 rounded border-2 border-dashed border-[#3da4e0] text-[#167dc0] font-black text-[11px] uppercase tracking-wider">
                        LAUNDRYKU OFFICIAL
                    </span>
                </div>
                <p class="font-black text-slate-800 text-xs">LaundryKu Management</p>
            </div>
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
