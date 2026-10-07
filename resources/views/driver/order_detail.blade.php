@extends('layouts.mobile_driver')

@section('content')
@php
    $order = $assignment->order;
    $isPickup = $assignment->type === 'pickup';
@endphp

<div class="space-y-4">

    <!-- Header & Back button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('driver.dashboard') }}" class="p-2 -ml-2 text-slate-600 hover:text-slate-900 flex items-center gap-1 text-xs font-bold">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $isPickup ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800' }}">
            Tugas {{ $isPickup ? 'Penjemputan' : 'Pengantaran' }}
        </span>
    </div>

    <!-- Map Preview -->
    <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm relative">
        <div id="driver-map" class="w-full h-56 z-10"></div>
        <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between text-xs">
            <div>
                <span class="text-slate-500">Jarak Outlet:</span>
                <span class="font-bold text-slate-900">{{ $order->distance_km }} KM</span>
            </div>
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $order->latitude }},{{ $order->longitude }}" 
               target="_blank"
               class="px-3 py-1.5 rounded-lg bg-[#3da4e0] text-white font-bold text-[11px] hover:bg-[#1b85c8] transition flex items-center gap-1">
                <span>Buka Google Maps</span> &rarr;
            </a>
        </div>
    </div>

    <!-- Customer Card -->
    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Data Pelanggan</span>
                <h3 class="text-base font-extrabold text-slate-900">{{ $order->customer_name }}</h3>
                <p class="text-xs text-slate-500 font-mono">{{ $order->customer_phone }}</p>
            </div>
            @if($order->customer_phone)
                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $order->customer_phone)) }}" 
                   target="_blank"
                   class="px-3 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                    <span>Chat WhatsApp</span>
                </a>
            @endif
        </div>

        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700">
            <strong>Alamat:</strong> {{ $order->address_text }}
        </div>

        @if($order->notes)
            <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-100 text-[11px] text-amber-800">
                <strong>Catatan Khusus:</strong> {{ $order->notes }}
            </div>
        @endif
    </div>

    <!-- Action Progression Form -->
    @if($assignment->status !== 'completed')
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Aksi Pembaruan Status</h4>
            <form action="{{ route('driver.tasks.update', $assignment->id_assignment) }}" method="POST">
                @csrf
                @if($isPickup)
                    @if($order->status === \App\Models\Order::STATUS_DRIVER_DITUGASKAN)
                        <button type="submit" name="target_status" value="LAUNDRY_DIAMBIL"
                                class="w-full py-3.5 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-bold text-xs shadow-lg shadow-[#3da4e0]/30 transition">
                            🧺 Konfirmasi Cucian Sudah Diambil dari Pelanggan
                        </button>
                    @elseif($order->status === \App\Models\Order::STATUS_LAUNDRY_DIAMBIL)
                        <button type="submit" name="target_status" value="SAMPAI_OUTLET"
                                class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition">
                            🏠 Konfirmasi Sudah Sampai di Outlet
                        </button>
                    @endif
                @else
                    @if($order->status === \App\Models\Order::STATUS_DRIVER_DITUGASKAN || $order->status === \App\Models\Order::STATUS_SIAP_DIANTAR)
                        <button type="submit" name="target_status" value="MENUNGGU_PENGANTARAN"
                                class="w-full py-3.5 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-bold text-xs shadow-lg shadow-[#3da4e0]/30 transition">
                            🛵 Mulai Pengantaran ke Lokasi Pelanggan
                        </button>
                    @elseif($order->status === \App\Models\Order::STATUS_MENUNGGU_PENGANTARAN)
                        <button type="submit" name="target_status" value="SELESAI"
                                class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition">
                            ✓ Selesai Antar (Cucian Diterima Pelanggan)
                        </button>
                    @endif
                @endif
            </form>
        </div>
    @else
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold text-center">
            ✓ Tugas ini telah selesai dikerjakan pada {{ $assignment->finished_at ? $assignment->finished_at->format('d M Y, H:i') : '' }}.
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const custPos = [{{ $order->latitude }}, {{ $order->longitude }}];
    const outletPos = [{{ config('services.outlet.latitude', -6.175392) }}, {{ config('services.outlet.longitude', 106.827153) }}];

    const map = L.map('driver-map').setView(custPos, 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Outlet Marker
    L.marker(outletPos).addTo(map).bindPopup('Outlet Pusat');

    // Customer Marker
    const custMarker = L.marker(custPos).addTo(map).bindPopup('Lokasi Pelanggan: {{ $order->customer_name }}');
    custMarker.openPopup();

    // Polyline
    L.polyline([outletPos, custPos], { color: '#3da4e0', weight: 3, dashArray: '5, 5' }).addTo(map);
});
</script>
@endpush
