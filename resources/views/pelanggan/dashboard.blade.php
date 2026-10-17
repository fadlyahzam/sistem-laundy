@extends('layouts.mobile_pelanggan')

@section('content')
<div class="space-y-4">

    <!-- Top Greeting & Quick Order Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#1b85c8] via-[#126fa9] to-[#0c4a75] p-5 text-white shadow-xl shadow-[#3da4e0]/25"
         style="background: linear-gradient(135deg, #1b85c8 0%, #126fa9 50%, #0c4a75 100%) !important;">
        <div class="relative z-10 space-y-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/20 text-white text-[10px] font-bold tracking-wide uppercase backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                Outlet Buka • Radius 20 KM
            </span>
            <h2 class="text-xl sm:text-2xl font-black leading-snug text-white drop-shadow-md">
                Halo, {{ explode(' ', $user->name)[0] }}! 👋
            </h2>
            <p class="text-xs text-sky-100 font-medium leading-relaxed max-w-[270px] drop-shadow-xs">
                Cucian numpuk? Santai aja, biar kurir LaundryKu yang jemput & antar sampai wangi!
            </p>
            <div class="pt-2">
                <a href="{{ route('pelanggan.orders.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-[#126fa9] font-black text-xs shadow-md shadow-black/10 hover:bg-slate-50 transition active:scale-95">
                    <svg class="w-4 h-4 text-[#3da4e0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Pesan Laundry Sekarang
                </a>
            </div>
        </div>

        <!-- Decorative background circles & brand shirt icon -->
        <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
        <div class="absolute right-4 top-4 text-white/15 pointer-events-none">
            <i class="fa-solid fa-shirt text-8xl transform rotate-12"></i>
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
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kode: {{ $activeOrder->order_code }}</span>
                    <p class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">
                        {{ $activeOrder->items->first()?->layanan?->name ?? 'Layanan Laundry' }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Jadwal Jemput: {{ $activeOrder->pickup_schedule->format('d M, H:i') }}
                    </p>
                </div>

                <div class="flex items-center gap-1.5">
                    <a href="{{ route('pelanggan.orders.show', $activeOrder->id_order) }}" 
                       class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition">
                        Detail
                    </a>

                    @if($activeOrder->invoice)
                        <a href="{{ route('pelanggan.orders.invoice', $activeOrder->id_order) }}" 
                           class="px-3 py-1.5 rounded-xl {{ $activeOrder->invoice->status === 'paid' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-sky-50 dark:bg-sky-950/40 text-[#1b85c8] dark:text-[#3da4e0] border border-sky-200 dark:border-sky-800' }} text-xs font-bold transition flex items-center gap-1">
                            <i class="fa-solid fa-file-invoice text-[10px]"></i>
                            <span>{{ $activeOrder->invoice->status === 'paid' ? 'Invoice (Lunas)' : 'Lihat Invoice' }}</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Services Catalog -->
    <div class="space-y-2.5">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 px-1">Layanan Laundry Kami</h3>
        <div class="grid grid-cols-1 gap-2.5">
            @foreach($layananList as $layanan)
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between hover:border-[#3da4e0]/30 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-[#3da4e0]/10 text-[#3da4e0] flex items-center justify-center font-bold text-lg group-hover:bg-[#3da4e0] group-hover:text-white transition">
                            @if($layanan->service_type === 'kiloan') 🧺 @else 👔 @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white">{{ $layanan->name }}</h4>
                            <p class="text-xs text-slate-400">
                                Mulai <span class="font-bold text-[#3da4e0]">Rp {{ number_format($layanan->price_per_kg, 0, ',', '.') }}</span>/{{ $layanan->service_type === 'kiloan' ? 'kg' : 'pcs' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('pelanggan.orders.create') }}" 
                       class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold text-xs hover:bg-[#3da4e0] hover:text-white transition">
                        Pilih
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Outlet Info Banner -->
    <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-3.5 border border-slate-200/80 dark:border-slate-800 flex items-center gap-3 text-xs text-slate-600 dark:text-slate-300">
        <div class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center shrink-0 text-slate-600 dark:text-slate-300 font-bold">
            📍
        </div>
        <div>
            <p class="font-bold text-slate-800 dark:text-white">Outlet LaundryKu Pusat</p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">Maksimal radius penjemputan 20 KM dari pusat outlet.</p>
        </div>
    </div>

</div>
@endsection
