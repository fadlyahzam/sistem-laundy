@extends('layouts.mobile_pelanggan')

@section('header_back')
    <a href="{{ route('pelanggan.orders.show', $order->id_order) }}" class="p-1.5 -ml-1 text-slate-600 hover:text-[#3da4e0] transition">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pembayaran QRIS</span>
        <h1 class="text-sm font-bold text-slate-900 leading-tight">Midtrans Sandbox</h1>
    </div>
@endsection

@section('content')
<div class="space-y-4" x-data="qrisTimer({{ $latestPayment->expires_at ? max(0, $latestPayment->expires_at->diffInSeconds(now())) : 1800 }})">

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

        <!-- 30-Minute Dynamic Countdown Timer -->
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
            <span>Batas Waktu: <strong x-text="formatTime()">29:59</strong></span>
        </div>

        <!-- QR Code Display Box -->
        <div class="relative mx-auto w-64 h-64 p-3 rounded-2xl bg-white border-2 border-slate-200 shadow-inner flex items-center justify-center">
            @php
                $qrContent = urlencode($latestPayment->qr_string ?? '00020101021226670016ID.CO.LAUNDRYKU.WWW011893600998000000015204581253033605400500005802ID5913LaundryKu6007JAKARTA62070703A016304');
            @endphp
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ $qrContent }}" 
                 alt="QRIS Code" 
                 class="w-full h-full object-contain rounded-xl shadow-sm">
        </div>

        <!-- Nominal -->
        <div class="space-y-0.5">
            <span class="text-xs text-slate-400 font-medium">Total Pembayaran</span>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">
                Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
            </h2>
            <p class="text-[11px] text-slate-400">Order ID: {{ $latestPayment->gateway_order_id }}</p>
        </div>

        <!-- Instruction Steps -->
        <div class="bg-slate-50 rounded-2xl p-3.5 text-left text-xs text-slate-600 space-y-1.5 border border-slate-100">
            <p class="font-bold text-slate-800">Cara Pembayaran:</p>
            <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-500">
                <li>Buka aplikasi m-Banking atau e-Wallet (BCA, GoPay, OVO, Dana, ShopeePay, dll).</li>
                <li>Pilih menu <strong>Scan / Bayar QRIS</strong>.</li>
                <li>Arahkan kamera ke kode QR di atas.</li>
                <li>Periksa nama penerima <strong>LaundryKu</strong> dan selesaikan pembayaran.</li>
            </ol>
        </div>

    </div>

    <!-- Testing & Sandbox Simulation Trigger -->
    <div class="bg-amber-50 rounded-2xl p-4 border border-amber-200 text-center space-y-2.5">
        <div class="flex items-center justify-center gap-1.5 text-amber-800 text-xs font-bold">
            <span>🧪</span> Mode Uji Coba (Sandbox Midtrans)
        </div>
        <p class="text-[11px] text-amber-700">
            Untuk menguji aliran sistem secara instan tanpa aplikasi perbankan, klik tombol di bawah untuk menyimulasikan webhook settlement:
        </p>
        <form action="{{ route('pelanggan.orders.simulate_pay', $order->id_order) }}" method="POST">
            @csrf
            <button type="submit" 
                    class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simulasikan Pembayaran Berhasil (Auto DIBAYAR)
            </button>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function qrisTimer(initialSeconds) {
    return {
        secondsLeft: initialSeconds,
        interval: null,

        init() {
            this.interval = setInterval(() => {
                if (this.secondsLeft > 0) {
                    this.secondsLeft--;
                } else {
                    clearInterval(this.interval);
                }
            }, 1000);
        },

        formatTime() {
            if (this.secondsLeft <= 0) return '00:00 (Kedaluwarsa)';
            const m = Math.floor(this.secondsLeft / 60);
            const s = this.secondsLeft % 60;
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }
    }
}
</script>
@endpush
