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
<div class="space-y-4" x-data="{ previewImage: null, previewTitle: '' }">

    <!-- 1. Horizontal Status Stepper Component -->
    <x-status-stepper :currentStatus="$order->status" />

    <!-- 2. Invoice / Payment Action Card (Only when waiting payment) -->
    @if($order->status === \App\Models\Order::STATUS_MENUNGGU_PEMBAYARAN && $order->invoice && $order->invoice->status !== 'paid')
        <!-- Jika Menunggu Pembayaran -->
        <div class="rounded-3xl p-5 text-white shadow-xl shadow-amber-500/25 space-y-4 border border-amber-400/40 relative overflow-hidden"
             style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 60%, #b45309 100%) !important;">
            
            <div class="flex items-start justify-between gap-3 relative z-10">
                <div class="space-y-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-black/20 text-white text-[10px] font-black uppercase tracking-wider backdrop-blur-sm border border-white/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                        Tagihan Tersedia
                    </span>
                    <h3 class="text-base sm:text-lg font-black text-white drop-shadow-sm">Siap Dibayar via QRIS</h3>
                    <p class="text-[11px] text-amber-100 font-medium">Periksa rincian pesanan & selesaikan pembayaran</p>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[10px] text-amber-100 block font-bold uppercase tracking-wider">Total Tagihan</span>
                    <span class="text-xl sm:text-2xl font-black text-white drop-shadow-sm">
                        Rp {{ number_format($order->invoice->total_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="pt-1 relative z-10">
                <a href="{{ route('pelanggan.orders.pay', $order->id_order) }}" 
                   class="w-full py-3.5 px-4 rounded-2xl bg-white hover:bg-amber-50 text-slate-900 font-extrabold text-xs sm:text-sm shadow-md shadow-black/10 flex items-center justify-center gap-2 transition active:scale-[0.99] group">
                    <span class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs group-hover:scale-110 transition shadow-sm">
                        <i class="fa-solid fa-bolt"></i>
                    </span>
                    <span>Bayar via QRIS</span>
                    <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>

            <!-- Subtle background decorative glow -->
            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
            <div class="absolute -left-6 -top-6 w-24 h-24 rounded-full bg-amber-300/10 blur-lg pointer-events-none"></div>
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
                            @if($item->quantity > 0)
                                {{ number_format($item->quantity, 1, ',', '.') }} {{ $item->layanan->service_type === 'kiloan' ? 'Kg' : 'Pcs' }} × Rp {{ number_format($item->price_snapshot, 0, ',', '.') }}
                            @else
                                <span class="text-amber-600 font-semibold italic">⚖️ Menunggu penimbangan di outlet</span>
                            @endif
                        </span>
                    </div>
                    <span class="text-xs font-extrabold text-slate-900">
                        @if($item->subtotal > 0)
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        @else
                            <span class="text-[11px] text-slate-400 font-normal">Akan dihitung</span>
                        @endif
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

            <!-- Tombol Lihat Rincian Pembayaran / Bayar -->
            <div class="pt-2">
                @if($order->invoice->status === 'paid')
                    <a href="{{ route('pelanggan.orders.invoice', $order->id_order) }}" 
                       class="w-full py-2.5 px-3 rounded-xl bg-sky-50 hover:bg-sky-100 border border-sky-200 text-[#167dc0] font-bold text-xs flex items-center justify-center gap-2 transition shadow-xs">
                        <i class="fa-solid fa-file-invoice"></i>
                        <span>Lihat Rincian Pembayaran</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('pelanggan.orders.pay', $order->id_order) }}" 
                       class="w-full py-3 px-4 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-extrabold text-xs flex items-center justify-center gap-2 transition shadow-md shadow-[#3da4e0]/20">
                        <i class="fa-solid fa-qrcode text-sm"></i>
                        <span>Bayar Tagihan Sekarang (QRIS)</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endif
            </div>
        @else
            <div class="pt-2.5 border-t border-slate-100 bg-sky-50/70 p-2.5 rounded-xl text-[11px] text-sky-800 flex items-center gap-2">
                <span>ℹ️</span>
                <span>Tagihan akan diterbitkan admin setelah pakaian ditimbang resmi di outlet.</span>
            </div>
        @endif
    </div>

    <!-- 4.1. Bukti Foto Driver (Jika Tersedia) -->
    @if($order->pickup_photo_url || $order->delivery_photo_url || $order->proof_photo_url)
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                <span>Bukti Foto dari Driver</span>
                <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1">
                    <i class="fa-solid fa-camera"></i> Dokumentasi
                </span>
            </h3>
            <div class="grid grid-cols-2 gap-3 text-xs">
                @if($order->pickup_photo_url)
                    <div class="space-y-1">
                        <span class="text-[10px] font-semibold text-slate-500">Foto Penjemputan</span>
                        <div @click="previewImage = '{{ $order->pickup_photo_url }}'; previewTitle = 'Bukti Foto Penjemputan'"
                             class="block aspect-video rounded-xl overflow-hidden border border-slate-200 group relative bg-slate-100 cursor-pointer shadow-inner">
                            <img src="{{ $order->pickup_photo_url }}" 
                                 alt="Bukti Penjemputan" 
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[10px] font-bold gap-1">
                                <i class="fa-solid fa-magnifying-glass-plus"></i> Lihat Foto
                            </div>
                        </div>
                    </div>
                @endif
                @if($order->delivery_photo_url)
                    <div class="space-y-1">
                        <span class="text-[10px] font-semibold text-slate-500">Foto Pengantaran</span>
                        <div @click="previewImage = '{{ $order->delivery_photo_url }}'; previewTitle = 'Bukti Foto Pengantaran'"
                             class="block aspect-video rounded-xl overflow-hidden border border-slate-200 group relative bg-slate-100 cursor-pointer shadow-inner">
                            <img src="{{ $order->delivery_photo_url }}" 
                                 alt="Bukti Pengantaran" 
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[10px] font-bold gap-1">
                                <i class="fa-solid fa-magnifying-glass-plus"></i> Lihat Foto
                            </div>
                        </div>
                    </div>
                @elseif($order->proof_photo_url && !$order->pickup_photo_url)
                    <div class="space-y-1">
                        <span class="text-[10px] font-semibold text-slate-500">Foto Dokumentasi</span>
                        <div @click="previewImage = '{{ $order->proof_photo_url }}'; previewTitle = 'Bukti Foto Driver'"
                             class="block aspect-video rounded-xl overflow-hidden border border-slate-200 group relative bg-slate-100 cursor-pointer shadow-inner">
                            <img src="{{ $order->proof_photo_url }}" 
                                 alt="Bukti Foto Driver" 
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[10px] font-bold gap-1">
                                <i class="fa-solid fa-magnifying-glass-plus"></i> Lihat Foto
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

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

    <!-- Full-Screen Lightbox Modal Foto Driver -->
    <div x-show="previewImage" 
         x-transition.opacity
         x-cloak
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="previewImage = null">
        
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-sm w-full overflow-hidden shadow-2xl relative text-white space-y-3 p-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                <h4 class="text-xs font-bold text-slate-200" x-text="previewTitle"></h4>
                <button type="button" @click="previewImage = null" class="w-7 h-7 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-sm transition">
                    ✕
                </button>
            </div>
            
            <div class="rounded-2xl overflow-hidden bg-black flex items-center justify-center max-h-[70vh]">
                <img :src="previewImage" alt="Preview Foto Driver" class="w-full h-auto max-h-[68vh] object-contain">
            </div>

            <div class="pt-1 flex items-center justify-between text-xs">
                <a :href="previewImage" target="_blank" class="text-sky-400 hover:underline flex items-center gap-1 font-semibold text-[11px]">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Ukuran Asli
                </a>
                <button type="button" @click="previewImage = null" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 font-bold text-[11px] text-slate-200 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
