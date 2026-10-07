@extends('layouts.mobile_pelanggan')

@section('content')
<div class="space-y-4">

    <!-- Top Greeting & Quick Order Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#3da4e0] via-[#2092d6] to-[#126fa9] p-5 text-white shadow-xl shadow-[#3da4e0]/25">
        <div class="relative z-10 space-y-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/20 text-white text-[10px] font-bold tracking-wide uppercase backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                Outlet Buka • Radius 20 KM
            </span>
            <h2 class="text-xl font-extrabold leading-snug">
                Halo, {{ explode(' ', $user->name)[0] }}! 👋
            </h2>
            <p class="text-xs text-white/90 leading-relaxed max-w-[260px]">
                Cucian numpuk? Santai aja, biar kurir LaundryKu yang jemput & antar sampai wangi!
            </p>
            <div class="pt-2">
                <a href="{{ route('pelanggan.orders.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-[#1b85c8] font-bold text-xs shadow-md shadow-black/10 hover:bg-slate-50 transition active:scale-95">
                    <svg class="w-4 h-4 text-[#3da4e0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Pesan Laundry Sekarang
                </a>
            </div>
        </div>

        <!-- Decorative background circles -->
        <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/10 blur-xl"></div>
        <div class="absolute right-4 top-4 text-white/20">
            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
        </div>
    </div>

    <!-- Active Order Tracker (if any) -->
    @if($activeOrder)
        <div class="space-y-2">
            <div class="flex items-center justify-between px-1">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Pesanan Sedang Berlangsung</h3>
                <a href="{{ route('pelanggan.orders.show', $activeOrder->id_order) }}" class="text-xs font-bold text-[#3da4e0] hover:underline">
                    Lihat Detail &rarr;
                </a>
            </div>

            <!-- Horizontal Status Tracker Component -->
            <x-status-stepper :currentStatus="$activeOrder->status" />

            <!-- Quick Action Card for Active Order -->
            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kode: {{ $activeOrder->order_code }}</span>
                    <p class="text-sm font-bold text-slate-900 mt-0.5">
                        {{ $activeOrder->items->first()?->layanan?->name ?? 'Layanan Laundry' }}
                    </p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Jadwal Jemput: {{ $activeOrder->pickup_schedule->format('d M, H:i') }}
                    </p>
                </div>

                @if($activeOrder->status === \App\Models\Order::STATUS_MENUNGGU_PEMBAYARAN)
                    <a href="{{ route('pelanggan.orders.pay', $activeOrder->id_order) }}" 
                       class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-md shadow-amber-500/25 flex items-center gap-1.5 animate-bounce">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        Bayar QRIS
                    </a>
                @else
                    <a href="{{ route('pelanggan.orders.show', $activeOrder->id_order) }}" 
                       class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                        Detail
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- Services Catalog -->
    <div class="space-y-2.5">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 px-1">Layanan Laundry Kami</h3>
        <div class="grid grid-cols-1 gap-2.5">
            @foreach($layananList as $layanan)
                <div class="bg-white rounded-2xl p-3.5 border border-slate-100 shadow-sm flex items-center justify-between hover:border-[#3da4e0]/30 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-[#3da4e0]/10 text-[#3da4e0] flex items-center justify-center font-bold text-lg group-hover:bg-[#3da4e0] group-hover:text-white transition">
                            @if($layanan->service_type === 'kiloan') 🧺 @else 👔 @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">{{ $layanan->name }}</h4>
                            <p class="text-xs text-slate-400">
                                Mulai <span class="font-bold text-[#3da4e0]">Rp {{ number_format($layanan->price_per_kg, 0, ',', '.') }}</span>/{{ $layanan->service_type === 'kiloan' ? 'kg' : 'pcs' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('pelanggan.orders.create') }}" 
                       class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-[#3da4e0] hover:text-white transition">
                        Pilih
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Outlet Info Banner -->
    <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200/80 flex items-center gap-3 text-xs text-slate-600">
        <div class="w-8 h-8 rounded-xl bg-slate-200 flex items-center justify-center shrink-0 text-slate-600 font-bold">
            📍
        </div>
        <div>
            <p class="font-bold text-slate-800">Outlet LaundryKu Pusat</p>
            <p class="text-[11px] text-slate-500">Maksimal radius penjemputan 20 KM dari pusat outlet.</p>
        </div>
    </div>

</div>
@endsection
