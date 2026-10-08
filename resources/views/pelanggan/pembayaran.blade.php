@extends('layouts.mobile_pelanggan')

@section('header_back')
    <a href="{{ route('pelanggan.orders.show', $order->id_order) }}" class="p-1.5 -ml-1 text-slate-600 hover:text-[#3da4e0] transition">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pembayaran QRIS</span>
        <h1 class="text-sm font-bold text-slate-900 leading-tight">Midtrans Payment</h1>
    </div>
@endsection

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

    <!-- QRIS Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-lg text-center space-y-4">
        
        <!-- Header QRIS & GoPay / BCA Logo -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="text-left">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#3da4e0]">Pembayaran Resmi</span>
                <h3 class="text-base font-extrabold text-slate-900">QRIS LaundryKu</h3>
            </div>
            <div class="px-2.5 py-1 rounded-lg bg-slate-900 text-white text-[11px] font-black tracking-widest">
                QRIS
            </div>
        </div>

        <!-- 30-Minute Dynamic Countdown Timer (MM:SS) -->
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border text-xs font-bold transition-all"
             :class="isExpired 
                ? 'bg-rose-100 border-rose-300 text-rose-800' 
                : 'bg-rose-50 border-rose-200 text-rose-700'">
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
        <div x-show="isExpired" x-cloak class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-center space-y-3">
            <div class="flex items-center justify-center gap-1.5 text-rose-800 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                Sesi Pembayaran Telah Kedaluwarsa!
            </div>
            <p class="text-xs text-rose-700 leading-relaxed">
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
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">
                Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
            </h2>
            <p class="text-[11px] text-slate-400">Order ID: {{ $payment->gateway_order_id ?? $invoice->invoice_number }}</p>
        </div>

        <!-- Instruction Steps -->
        <div class="bg-slate-50 rounded-2xl p-3.5 text-left text-xs text-slate-600 space-y-1.5 border border-slate-100">
            <p class="font-bold text-slate-800">Cara Pembayaran:</p>
            <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-500">
                <li>Buka aplikasi m-Banking atau e-Wallet (BCA, GoPay, OVO, Dana, ShopeePay, dll).</li>
                <li>Pilih menu <strong>Scan / Bayar QRIS</strong>.</li>
                <li>Arahkan kamera ke kode QR di atas sebelum batas waktu habis.</li>
                <li>Periksa nama penerima <strong>LaundryKu</strong> dan selesaikan pembayaran.</li>
            </ol>
        </div>

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

    return {
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
</script>
@endpush
