@extends('layouts.mobile_pelanggan')

@section('header_back')
    <a href="{{ route('pelanggan.orders') }}" class="p-1.5 -ml-1 text-slate-600 hover:text-[#3da4e0] transition">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Detail Pesanan</span>
        <h1 class="text-sm font-bold text-slate-900 leading-tight">{{ $order->order_code }}</h1>
    </div>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. Horizontal Status Stepper Component -->
    <x-status-stepper :currentStatus="$order->status" />

    <!-- 2. Payment Action Card (If MENUNGGU_PEMBAYARAN) -->
    @if($order->status === \App\Models\Order::STATUS_MENUNGGU_PEMBAYARAN && $order->invoice && $order->invoice->status !== 'paid')
        <div class="rounded-2xl p-4 bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-500/25 space-y-3 animate-pulse">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider bg-white/20 px-2 py-0.5 rounded-full">Tagihan Tersedia</span>
                    <h3 class="text-base font-extrabold mt-1">Siap Dibayar via QRIS</h3>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-white/80 block">Total Tagihan</span>
                    <span class="text-lg font-black">Rp {{ number_format($order->invoice->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
            <a href="{{ route('pelanggan.orders.pay', $order->id_order) }}" 
               class="block w-full py-2.5 px-4 rounded-xl bg-white text-amber-700 font-extrabold text-xs text-center shadow-md hover:bg-slate-50 transition">
                ⚡ Bayar Sekarang via QRIS (Midtrans)
            </a>
        </div>
    @endif

    <!-- 3. Driver Info Card (If Assigned) -->
    @php
        $activeDriver = $order->deliveryAssignment?->driver ?? $order->pickupAssignment?->driver;
        $activeType = $order->deliveryAssignment ? 'Pengantaran' : 'Penjemputan';
    @endphp
    @if($activeDriver)
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#3da4e0]">Driver {{ $activeType }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Aktif Bertugas
                </span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-base shadow">
                        🛵
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $activeDriver->user->name ?? 'Driver' }}</h4>
                        <p class="text-xs text-slate-500">{{ $activeDriver->vehicle_type }} • {{ $activeDriver->plate_number }}</p>
                    </div>
                </div>
                @if($activeDriver->user && $activeDriver->user->phone)
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $activeDriver->user->phone)) }}" 
                       target="_blank"
                       class="px-3 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-sm flex items-center gap-1.5 transition">
                        <span>WA</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- 4. Rincian Layanan & Cucian -->
    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Rincian Layanan</h3>
        
        <div class="divide-y divide-slate-100">
            @foreach($order->items as $item)
                <div class="py-2.5 flex items-center justify-between first:pt-0 last:pb-0">
                    <div>
                        <h4 class="text-xs font-bold text-slate-800">{{ $item->layanan->name }}</h4>
                        @if($item->kategori)
                            <p class="text-[11px] text-slate-500">+ {{ $item->kategori->name }}</p>
                        @endif
                        <span class="text-[10px] text-slate-400">
                            {{ $item->quantity }} {{ $item->layanan->service_type === 'kiloan' ? 'Kg' : 'Pcs' }} × Rp {{ number_format($item->price_snapshot, 0, ',', '.') }}
                        </span>
                    </div>
                    <span class="text-xs font-extrabold text-slate-900">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>

        @if($order->invoice)
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-700">Total Tagihan:</span>
                    <span class="text-[10px] block text-slate-400">No. {{ $order->invoice->invoice_number }}</span>
                </div>
                <div class="text-right">
                    <span class="text-sm font-black text-slate-900">
                        Rp {{ number_format($order->invoice->total_amount, 0, ',', '.') }}
                    </span>
                    <span class="block text-[10px] font-bold {{ $order->invoice->status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $order->invoice->status === 'paid' ? '✓ Lunas' : '⏳ Belum Dibayar' }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- 5. Lokasi Penjemputan -->
    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-2">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Lokasi Penjemputan</h3>
            <span class="text-xs font-bold text-[#3da4e0]">{{ $order->distance_km }} KM</span>
        </div>
        <p class="text-xs text-slate-800 leading-relaxed font-medium">{{ $order->address_text }}</p>
        <p class="text-[11px] text-slate-500">
            Jadwal: <strong>{{ $order->pickup_schedule->format('d M Y, H:i') }} WIB</strong>
        </p>
        @if($order->notes)
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-600">
                <span class="font-bold text-slate-700">Catatan:</span> {{ $order->notes }}
            </div>
        @endif
    </div>

    <!-- 6. Riwayat Perjalanan Status (Audit Logs) -->
    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Riwayat Status</h3>
        <div class="relative pl-5 space-y-3.5 border-l-2 border-slate-100">
            @foreach($order->statusLogs as $log)
                <div class="relative">
                    <div class="absolute -left-[27px] top-1 w-3 h-3 rounded-full bg-[#3da4e0] ring-4 ring-white"></div>
                    <div>
                        <div class="flex items-center justify-between">
                            <h5 class="text-xs font-bold text-slate-800">
                                {{ \App\Models\Order::STATUSES[$log->to_status] ?? $log->to_status }}
                            </h5>
                            <span class="text-[10px] text-slate-400">{{ $log->changed_at->format('H:i, d M') }}</span>
                        </div>
                        @if($log->note)
                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $log->note }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
