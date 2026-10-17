@extends('layouts.mobile_pelanggan')

@section('header_back')
    <a href="{{ route('pelanggan.orders.show', $order->id_order) }}" 
       id="header-back-link"
       class="p-1.5 -ml-1 text-slate-600 dark:text-slate-300 hover:text-[#3da4e0] transition"
       title="Kembali">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tagihan & Pembayaran</span>
        <h1 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">Invoice #{{ $invoice->invoice_number }}</h1>
    </div>
@endsection

@push('styles')
<style>
@media print {
    header, aside, nav, .no-print, button, a.btn, [role="switch"] {
        display: none !important;
    }
    body, html, main {
        background-color: #ffffff !important;
        color: #0f172a !important;
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
        box-shadow: none !important;
    }
    .dark * {
        background-color: #ffffff !important;
        color: #0f172a !important;
        border-color: #e2e8f0 !important;
    }
}
</style>
@endpush

@section('content')
@php
    $payment = $payment ?? $latestPayment ?? $invoice->latestPayment;
    $expiresAtIso = $payment && $payment->expires_at 
        ? $payment->expires_at->toIso8601String() 
        : now()->addMinutes(30)->toIso8601String();
    $serverRemainingSeconds = $payment && $payment->expires_at 
        ? max(0, (int) now()->diffInSeconds($payment->expires_at, false)) 
        : 1800;
@endphp

<div class="space-y-4" x-data="qrisCountdown('{{ $expiresAtIso }}', {{ $serverRemainingSeconds }})">

    <!-- Tab Navigasi: Rincian Invoice (Pertama) & Pembayaran QRIS / Bukti Pelunasan -->
    <div class="flex items-center p-1 bg-slate-200/80 dark:bg-slate-800 rounded-2xl shadow-inner text-xs font-bold">
        <button type="button" 
                @click="currentTab = 'invoice'"
                :class="currentTab === 'invoice' 
                    ? 'bg-white dark:bg-slate-900 text-[#3da4e0] shadow-sm' 
                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                class="flex-1 py-2 px-3 rounded-xl flex items-center justify-center gap-1.5 transition">
            <i class="fa-solid fa-file-invoice"></i>
            <span>{{ $invoice->status === 'paid' ? '1. Rincian Invoice (Lunas)' : '1. Rincian Invoice' }}</span>
        </button>

        <button type="button" 
                @click="currentTab = 'qris'"
                :class="currentTab === 'qris' 
                    ? 'bg-white dark:bg-slate-900 {{ $invoice->status === 'paid' ? 'text-emerald-600' : 'text-[#3da4e0]' }} shadow-sm' 
                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                class="flex-1 py-2 px-3 rounded-xl flex items-center justify-center gap-1.5 transition relative">
            <i class="fa-solid {{ $invoice->status === 'paid' ? 'fa-circle-check text-emerald-500' : 'fa-qrcode' }}"></i>
            <span>{{ $invoice->status === 'paid' ? '2. Bukti Pelunasan' : '2. Bayar QRIS' }}</span>
            @if($invoice->status !== 'paid')
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse ml-0.5"></span>
            @endif
        </button>
    </div>

    <!-- ========================================== -->
    <!-- MENU 1: INVOICE / RINCIAN PEMBELIAN       -->
    <!-- Ditampilkan PERTAMA sebelum menuju QRIS    -->
    <!-- ========================================== -->
    <div x-show="currentTab === 'invoice'" x-transition class="space-y-4">
        
        <!-- Card Invoice -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-lg space-y-4">
            
            <!-- Header Invoice -->
            <div class="flex items-start justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-[#3da4e0]/10 text-[#3da4e0] flex items-center justify-center text-lg font-black shadow-sm">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#3da4e0]">Tagihan Resmi</span>
                        <h2 class="text-sm font-black text-slate-900 dark:text-white">Laundry<span class="text-[#3da4e0]">Ku</span> Pusat</h2>
                    </div>
                </div>

                <div class="text-right">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $invoice->status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $invoice->status === 'paid' ? 'bg-emerald-500' : 'bg-amber-500 animate-ping' }}"></span>
                        {{ $invoice->status === 'paid' ? 'LUNAS' : 'MENUNGGU PEMBAYARAN' }}
                    </span>
                    <span class="block text-[10px] text-slate-400 mt-1">Invoice: {{ $invoice->invoice_number }}</span>
                </div>
            </div>

            @if($invoice->status === 'paid')
                <!-- Status Lunas Banner Resmi -->
                <div class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 shadow-sm">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Status Pembayaran</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-emerald-600 text-white uppercase">✓ Lunas</span>
                        </div>
                        <p class="text-xs font-bold text-emerald-900 dark:text-emerald-100 mt-0.5">Tagihan Telah Selesai Dibayar</p>
                        <p class="text-[10px] text-emerald-700 dark:text-emerald-300 mt-0.5">
                            Pembayaran resmi via Midtrans QRIS terverifikasi pada {{ ($invoice->paid_at ?? now())->format('d M Y, H:i') }} WIB.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Meta Data Invoice -->
            <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 dark:bg-slate-800/60 p-3 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-[10px] text-slate-400 font-semibold block">Kode Pesanan</span>
                    <span class="font-extrabold text-slate-800 dark:text-slate-200 text-xs">{{ $order->order_code }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-semibold block">Tanggal Terbit</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-300 text-[11px]">
                        {{ ($invoice->issued_at ?? $invoice->created_at)->format('d M Y, H:i') }} WIB
                    </span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-semibold block">Nama Pelanggan</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-xs">{{ $order->customer_name }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 font-semibold block">Nomor WhatsApp</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-300 text-xs">{{ $order->customer_phone }}</span>
                </div>
                <div class="col-span-2 pt-1 border-t border-slate-200/60 dark:border-slate-700/60">
                    <span class="text-[10px] text-slate-400 font-semibold block">Alamat Penjemputan</span>
                    <span class="text-[11px] text-slate-700 dark:text-slate-300 line-clamp-2 leading-relaxed">
                        📍 {{ $order->address_text }}
                    </span>
                </div>
            </div>

            <!-- Rincian Pembelian / Item Cucian -->
            <div class="space-y-2 pt-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        Rincian Pembelian
                    </h3>
                    <span class="text-[10px] font-semibold text-slate-400">
                        Total {{ $order->items->count() }} Layanan
                    </span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden bg-white dark:bg-slate-900">
                    @forelse($order->items as $item)
                        <div class="p-3.5 flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                        {{ $item->layanan->name }}
                                    </h4>
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-[#3da4e0]/10 text-[#3da4e0] uppercase">
                                        {{ $item->layanan->service_type }}
                                    </span>
                                </div>

                                @if($item->kategori)
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                        + {{ $item->kategori->name }}
                                    </p>
                                @endif

                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                                    <span class="font-bold text-slate-700 dark:text-slate-300">
                                        {{ number_format($item->quantity, 1, ',', '.') }} {{ $item->layanan->service_type === 'kiloan' ? 'Kg' : 'Pcs' }}
                                    </span>
                                    <span>×</span>
                                    <span>Rp {{ number_format($item->price_snapshot, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-xs font-black text-slate-900 dark:text-white">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400">
                            Tidak ada rincian item.
                        </div>
                    @endforelse

                    <!-- Ongkos Kirim / Antar-Jemput -->
                    <div class="p-3.5 bg-slate-50/70 dark:bg-slate-800/40 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[#3da4e0]">🛵</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">Layanan Antar-Jemput (Radius {{ $order->distance_km ?? 0 }} KM)</span>
                        </div>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 uppercase text-[11px]">
                            Gratis
                        </span>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Total Tagihan -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Subtotal Tagihan Cucian</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">
                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Biaya Penanganan / Admin</span>
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">Rp 0 (Gratis)</span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-dashed border-slate-200 dark:border-slate-700">
                    <div>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Total Yang Harus Dibayar</span>
                        <span class="text-[10px] text-slate-400">Metode: Midtrans QRIS Otomatis</span>
                    </div>
                    <span class="text-xl font-black text-slate-900 dark:text-white">
                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Tombol Aksi -->
            @if($invoice->status === 'paid')
                <div class="pt-2 space-y-2">
                    <a href="{{ route('pelanggan.orders.print_invoice', $order->id_order) }}?print=1" 
                       class="w-full py-3 px-4 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 transition shadow-md active:scale-[0.99]">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Invoice</span>
                    </a>

                    <button type="button" 
                            @click="currentTab = 'qris'" 
                            class="w-full py-2.5 px-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 font-bold text-xs flex items-center justify-center gap-2 transition border border-emerald-200 dark:border-emerald-800">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Lihat Bukti Pelunasan Resmi</span>
                    </button>

                    <a href="{{ route('pelanggan.orders.show', $order->id_order) }}" 
                       class="w-full py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-center text-xs font-bold transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[11px]"></i>
                        <span>Kembali ke Detail Pesanan</span>
                    </a>
                </div>
            @else
                <div class="pt-2 space-y-2">
                    <button type="button" 
                            @click="currentTab = 'qris'"
                            class="w-full py-3.5 px-4 rounded-2xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-extrabold text-sm shadow-lg shadow-[#3da4e0]/30 flex items-center justify-center gap-2 transition active:scale-[0.99]">
                        <i class="fa-solid fa-qrcode text-base"></i>
                        <span>Lanjut ke Pembayaran QRIS</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>

                    <a href="{{ route('pelanggan.orders.print_invoice', $order->id_order) }}?print=1" 
                       class="w-full py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center justify-center gap-2 transition border border-slate-200 dark:border-slate-700">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Invoice</span>
                    </a>

                    <p class="text-[11px] text-center text-slate-400 dark:text-slate-500">
                        💡 Periksa rincian pembelian di atas sebelum memindai kode QRIS.
                    </p>
                </div>
            @endif

        </div>

    </div>

    <!-- ========================================== -->
    <!-- MENU 2: STATUS PELUNASAN / PEMBAYARAN QRIS-->
    <!-- ========================================== -->
    <div x-show="currentTab === 'qris'" x-transition x-cloak class="space-y-4">
        
        @if($invoice->status === 'paid')
            <!-- Bukti Pelunasan Card Resmi -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-emerald-200/80 dark:border-emerald-900/60 shadow-lg text-center space-y-4">
                <!-- Header Back -->
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <button type="button" 
                            @click="currentTab = 'invoice'"
                            class="text-xs font-bold text-[#3da4e0] hover:text-[#1b85c8] flex items-center gap-1.5 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Lihat Rincian Invoice</span>
                    </button>

                    <div class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[11px] font-black tracking-wider uppercase">
                        ✓ LUNAS
                    </div>
                </div>

                <!-- Big Checkmark -->
                <div class="w-20 h-20 mx-auto rounded-3xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-500 flex items-center justify-center text-4xl shadow-inner border border-emerald-100 dark:border-emerald-800">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Bukti Pembayaran Resmi</span>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mt-0.5">Pembayaran Berhasil!</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Tagihan telah dilunasi dan pesanan Anda sedang diproses oleh tim LaundryKu.
                    </p>
                </div>

                <!-- Nominal Box -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-1">
                    <span class="text-[11px] text-slate-400 font-medium block">Nominal Pelunasan</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white block">
                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold block">
                        Metode: Midtrans QRIS Otomatis
                    </span>
                </div>

                <!-- Receipt Info Grid -->
                <div class="text-left text-xs bg-white dark:bg-slate-800/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 divide-y divide-slate-100 dark:divide-slate-800">
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-400">Nomor Invoice</span>
                        <span class="font-extrabold text-slate-800 dark:text-slate-200">#{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-400">Kode Pesanan</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $order->order_code }}</span>
                    </div>
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-400">Nama Pelanggan</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $order->customer_name }}</span>
                    </div>
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-400">Waktu Bayar</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">
                            {{ ($invoice->paid_at ?? now())->format('d M Y, H:i') }} WIB
                        </span>
                    </div>
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-400">Ref. Transaksi Gateway</span>
                        <span class="font-mono text-[11px] font-bold text-slate-700 dark:text-slate-300 truncate max-w-[160px]">
                            {{ $payment->gateway_order_id ?? $payment->transaction_id ?? $invoice->invoice_number }}
                        </span>
                    </div>
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-400">Status</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            VERIFIED & PAID
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-2 space-y-2">
                    <a href="{{ route('pelanggan.orders.print_receipt', $order->id_order) }}?print=1" 
                       class="w-full py-3.5 px-4 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-md flex items-center justify-center gap-2 transition active:scale-[0.99]">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Bukti Pembayaran</span>
                    </a>
                    <a href="{{ route('pelanggan.orders.show', $order->id_order) }}" 
                       class="w-full py-2.5 px-4 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-center text-xs font-bold transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[11px]"></i>
                        <span>Kembali ke Detail Pesanan</span>
                    </a>
                </div>
            </div>
        @else
            <!-- QRIS Card Unpaid -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-lg text-center space-y-4">
                
                <!-- Quick Back to Invoice -->
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <button type="button" 
                            @click="currentTab = 'invoice'"
                            class="text-xs font-bold text-[#3da4e0] hover:text-[#1b85c8] flex items-center gap-1.5 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Lihat Rincian Invoice</span>
                    </button>

                    <div class="px-2.5 py-1 rounded-lg bg-slate-900 text-white text-[11px] font-black tracking-widest">
                        QRIS
                    </div>
                </div>

                <!-- Header QRIS Info -->
                <div class="text-center">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#3da4e0]">Pembayaran Resmi</span>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">QRIS LaundryKu</h3>
                </div>

                <!-- 30-Minute Dynamic Countdown Timer (MM:SS) -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border text-xs font-bold transition-all"
                     :class="isExpired 
                        ? 'bg-rose-100 border-rose-300 text-rose-800 dark:bg-rose-950/40 dark:border-rose-800 dark:text-rose-300' 
                        : 'bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-950/20 dark:border-rose-900 dark:text-rose-400'">
                    <span class="w-2 h-2 rounded-full bg-rose-500" :class="isExpired ? '' : 'animate-ping'"></span>
                    <span>Batas Waktu: <strong x-text="formatRemaining()">29:59</strong></span>
                </div>

                <!-- QR Code Display Box (Disabled ONLY on Expiry) -->
                <div class="relative mx-auto w-64 h-64 p-3 rounded-2xl bg-white border-2 border-slate-200 shadow-inner flex items-center justify-center overflow-hidden">
                    @php
                        $qrContent = urlencode($payment->qr_string ?? '00020101021226670016ID.CO.LAUNDRYKU.WWW011893600998000000015204581253033605400500005802ID5913LaundryKu6007JAKARTA62070703A016304');
                    @endphp
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ $qrContent }}" 
                         alt="QRIS Code" 
                         :class="isExpired ? 'opacity-15 blur-[2px] grayscale pointer-events-none' : 'shadow-sm'"
                         class="w-full h-full object-contain rounded-xl transition-all duration-300">

                    <!-- Expired Mask Overlay -->
                    <div x-show="isExpired" x-cloak class="absolute inset-0 bg-slate-900/75 backdrop-blur-[2px] flex flex-col items-center justify-center p-4 text-center text-white">
                        <div class="w-12 h-12 rounded-full bg-rose-500/90 text-white flex items-center justify-center text-xl mb-2 shadow-lg">
                            ⏰
                        </div>
                        <span class="text-xs font-black uppercase tracking-wider text-rose-300">QRIS Kedaluwarsa</span>
                        <p class="text-[11px] text-slate-200 mt-1 leading-snug">Kode QRIS ini telah kedaluwarsa demi keamanan transaksi.</p>
                    </div>
                </div>

                <!-- DEMO SIMULATION PAYMENT BUTTON (Below QRIS Image) -->
                <div class="pt-1">
                    <form action="{{ route('pelanggan.pesanan.simulate_pay', $order->id_order) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm text-xs w-full flex items-center justify-center gap-2 transition active:scale-[0.99]">
                            <i class="fa-solid fa-vial"></i>
                            <span>[DEMO] Simulasi Bayar Selesai</span>
                        </button>
                    </form>
                    <p class="text-[10px] text-slate-400 mt-1.5">
                        💡 Tombol demo untuk menguji pelunasan pesanan secara instan tanpa Midtrans Simulator.
                    </p>
                </div>

                <!-- Expiration Alert Notice & Regenerate Button (Shown ONLY when timer hits 00:00) -->
                <div x-show="isExpired" x-cloak class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-center space-y-3">
                    <div class="flex items-center justify-center gap-1.5 text-rose-800 dark:text-rose-300 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                        Sesi Pembayaran Telah Kedaluwarsa!
                    </div>
                    <p class="text-xs text-rose-700 dark:text-rose-400 leading-relaxed">
                        Waktu 30 menit pembayaran telah habis. Silakan buat ulang kode pembayaran QRIS baru di bawah ini.
                    </p>
                    <a href="{{ route('pelanggan.orders.pay', ['id' => $order->id_order, 'regenerate' => 1]) }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-bold text-xs shadow-lg shadow-[#3da4e0]/30 transition w-full">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span>Buat Ulang Pembayaran QRIS Baru</span>
                    </a>
                </div>

                <!-- Nominal Info -->
                <div class="space-y-0.5 pt-1">
                    <span class="text-xs text-slate-400 font-medium">Total Pembayaran</span>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                    </h2>
                    <p class="text-[11px] text-slate-400">Order ID: {{ $payment->gateway_order_id ?? $invoice->invoice_number }}</p>
                </div>

                <!-- Instruction Steps -->
                <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-3.5 text-left text-xs text-slate-600 dark:text-slate-300 space-y-1.5 border border-slate-100 dark:border-slate-800">
                    <p class="font-bold text-slate-800 dark:text-slate-200">Cara Pembayaran:</p>
                    <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-500 dark:text-slate-400">
                        <li>Buka aplikasi m-Banking atau e-Wallet (BCA, GoPay, OVO, Dana, ShopeePay, dll).</li>
                        <li>Pilih menu <strong>Scan / Bayar QRIS</strong>.</li>
                        <li>Arahkan kamera ke kode QR di atas sebelum batas waktu habis.</li>
                        <li>Periksa nama penerima <strong>LaundryKu</strong> dan selesaikan pembayaran.</li>
                    </ol>
                </div>

            </div>
        @endif

    </div>

</div>
@endsection

@push('scripts')
<script>
function qrisCountdown(targetIso, serverSeconds) {
    let targetTime = new Date(targetIso).getTime();
    const now = new Date().getTime();

    // Prevent timezone skew: if targetTime is invalid or desynchronized behind current time while server remaining > 0
    if (isNaN(targetTime) || (targetTime <= now && serverSeconds > 0)) {
        targetTime = now + (serverSeconds * 1000);
    }

    const urlTab = new URLSearchParams(window.location.search).get('tab');
    return {
        currentTab: urlTab || '{{ ($invoice->status === "paid" && request("tab") === "qris") ? "qris" : "invoice" }}', // Menampilkan invoice sebelum QRIS muncul
        targetTime: targetTime,
        remainingSeconds: Math.max(0, Math.floor((targetTime - now) / 1000)),
        isExpired: false,
        timer: null,

        init() {
            this.updateTime();
            this.timer = setInterval(() => {
                this.updateTime();
            }, 1000);
        },

        updateTime() {
            const current = new Date().getTime();
            const diff = Math.floor((this.targetTime - current) / 1000);

            if (isNaN(diff) || diff <= 0) {
                this.remainingSeconds = 0;
                this.isExpired = true;
                if (this.timer) {
                    clearInterval(this.timer);
                }
            } else {
                this.remainingSeconds = diff;
                this.isExpired = false;
            }
        },

        formatRemaining() {
            if (this.isExpired || this.remainingSeconds <= 0) {
                return '00:00 (Kedaluwarsa)';
            }
            const minutes = Math.floor(this.remainingSeconds / 60);
            const seconds = this.remainingSeconds % 60;
            return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const backLink = document.getElementById('header-back-link');
    if (backLink && document.referrer) {
        try {
            const ref = new URL(document.referrer);
            if (ref.origin === window.location.origin && 
                !ref.pathname.includes('/cetak-') && 
                ref.pathname !== window.location.pathname) {
                backLink.href = document.referrer;
            }
        } catch (e) {}
    }
});
</script>
@endpush
