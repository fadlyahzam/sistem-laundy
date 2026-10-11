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
    <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm relative">
        <div id="driver-map" class="w-full h-56 z-10"></div>
        <div class="p-3 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
            <div>
                <span class="text-slate-500 dark:text-slate-400">Jarak Outlet:</span>
                <span class="font-bold text-slate-900 dark:text-white">{{ $order->distance_km }} KM</span>
            </div>
            <a href="https://www.google.com/maps/search/?api=1&query={{ $order->latitude }},{{ $order->longitude }}" 
               target="_blank"
               class="px-3 py-1.5 rounded-lg bg-[#3da4e0] text-white font-bold text-[11px] hover:bg-[#1b85c8] transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-location-arrow text-xs"></i>
                <span>Buka Google Maps</span> &rarr;
            </a>
        </div>
    </div>

    <!-- Customer Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-100 dark:border-slate-800 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Data Pelanggan</span>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">{{ $order->customer_name }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $order->customer_phone }}</p>
            </div>
            @if($order->customer_phone)
                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $order->customer_phone)) }}" 
                   target="_blank"
                   class="px-3 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Chat WhatsApp</span>
                </a>
            @endif
        </div>

        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300">
            <strong class="text-slate-900 dark:text-white">Alamat:</strong> {{ $order->address_text }}
        </div>

        @if($order->notes)
            <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-900/40 text-[11px] text-amber-800 dark:text-amber-300">
                <strong>Catatan Khusus:</strong> {{ $order->notes }}
            </div>
        @endif
    </div>

    <!-- Existing Proof Photos Showcase (if already uploaded) -->
    @if($assignment->proof_photo_url || $order->pickup_photo_url || $order->delivery_photo_url)
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Bukti Foto Tersimpan
            </h4>
            <div class="grid grid-cols-2 gap-3">
                @if($order->pickup_photo_url)
                    <div class="space-y-1">
                        <span class="text-[10px] font-semibold text-slate-500">Bukti Penjemputan</span>
                        <a href="{{ $order->pickup_photo_url }}" target="_blank" class="block aspect-video rounded-xl overflow-hidden border border-slate-200 bg-slate-50 group relative">
                            <img src="{{ $order->pickup_photo_url }}" alt="Bukti Penjemputan" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[11px] font-semibold">
                                Lihat Foto
                            </div>
                        </a>
                    </div>
                @endif
                @if($order->delivery_photo_url)
                    <div class="space-y-1">
                        <span class="text-[10px] font-semibold text-slate-500">Bukti Pengantaran</span>
                        <a href="{{ $order->delivery_photo_url }}" target="_blank" class="block aspect-video rounded-xl overflow-hidden border border-slate-200 bg-slate-50 group relative">
                            <img src="{{ $order->delivery_photo_url }}" alt="Bukti Pengantaran" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[11px] font-semibold">
                                Lihat Foto
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Action Progression Form -->
    @if($assignment->status !== 'completed')
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Aksi Pembaruan Status</h4>
            
            @if($isPickup)
                {{-- Step 1 Pickup: Driver Pickup Confirmation (Requires Photo) --}}
                @if($order->status === \App\Models\Order::STATUS_DRIVER_DITUGASKAN)
                    <form action="{{ route('driver.tasks.update', $assignment->id_assignment) }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          x-data="{ photoPreview: null }"
                          class="space-y-3">
                        @csrf
                        <input type="hidden" name="target_status" value="LAUNDRY_DIAMBIL">

                        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-2">
                            <div class="font-bold flex items-center gap-1.5 text-amber-800">
                                <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                </svg>
                                <span>Wajib Unggah Bukti Foto Penjemputan</span>
                            </div>
                            <p class="text-[11px] text-amber-700 leading-relaxed">
                                Ambil foto cucian saat diserahkan oleh pelanggan di lokasi penjemputan sebagai bukti resmi serah terima.
                            </p>

                            <!-- Photo Input & Preview -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Ambil Foto / Pilih Gambar</label>
                                <input type="file" 
                                       name="proof_photo" 
                                       accept="image/*" 
                                       capture="environment" 
                                       required
                                       @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => photoPreview = e.target.result; reader.readAsDataURL(file); }"
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#3da4e0] file:text-white hover:file:bg-[#1b85c8] cursor-pointer">
                                @error('proof_photo')
                                    <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Live Image Preview Box -->
                            <div x-show="photoPreview" x-cloak class="mt-2">
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Preview Foto:</span>
                                <div class="mt-1 relative w-full h-44 rounded-xl overflow-hidden border border-slate-300 shadow-inner bg-slate-100">
                                    <img :src="photoPreview" alt="Preview Foto Penjemputan" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full py-3.5 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-bold text-xs shadow-lg shadow-[#3da4e0]/30 transition flex items-center justify-center gap-2">
                            <span>🧺 Konfirmasi & Unggah Bukti Penjemputan</span>
                        </button>
                    </form>

                {{-- Step 2 Pickup: Arrived at Outlet (No photo needed, just status update) --}}
                @elseif($order->status === \App\Models\Order::STATUS_LAUNDRY_DIAMBIL)
                    <form action="{{ route('driver.tasks.update', $assignment->id_assignment) }}" method="POST">
                        @csrf
                        <input type="hidden" name="target_status" value="SAMPAI_OUTLET">
                        <button type="submit"
                                class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                            <span>🏠 Konfirmasi Sudah Sampai di Outlet</span>
                        </button>
                    </form>
                @endif

            @else
                {{-- Step 1 Delivery: Start Delivering --}}
                @if($order->status === \App\Models\Order::STATUS_DRIVER_DITUGASKAN || $order->status === \App\Models\Order::STATUS_SIAP_DIANTAR)
                    <form action="{{ route('driver.tasks.update', $assignment->id_assignment) }}" method="POST">
                        @csrf
                        <input type="hidden" name="target_status" value="MENUNGGU_PENGANTARAN">
                        <button type="submit"
                                class="w-full py-3.5 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-bold text-xs shadow-lg shadow-[#3da4e0]/30 transition flex items-center justify-center gap-2">
                            <span>🛵 Mulai Pengantaran ke Lokasi Pelanggan</span>
                        </button>
                    </form>

                {{-- Step 2 Delivery: Complete Delivery (Requires Photo Proof) --}}
                @elseif($order->status === \App\Models\Order::STATUS_MENUNGGU_PENGANTARAN)
                    <form action="{{ route('driver.tasks.update', $assignment->id_assignment) }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          x-data="{ photoPreview: null }"
                          class="space-y-3">
                        @csrf
                        <input type="hidden" name="target_status" value="SELESAI">

                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 space-y-2">
                            <div class="font-bold flex items-center gap-1.5 text-emerald-800">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                </svg>
                                <span>Wajib Unggah Bukti Foto Serah Terima / Sampai Tujuan</span>
                            </div>
                            <p class="text-[11px] text-emerald-700 leading-relaxed">
                                Ambil foto pakaian bersih yang telah diserahkan kepada pelanggan di lokasi tujuan sebagai bukti tugas selesai.
                            </p>

                            <!-- Photo Input & Preview -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Ambil Foto / Pilih Gambar</label>
                                <input type="file" 
                                       name="proof_photo" 
                                       accept="image/*" 
                                       capture="environment" 
                                       required
                                       @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => photoPreview = e.target.result; reader.readAsDataURL(file); }"
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                                @error('proof_photo')
                                    <p class="text-[11px] text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Live Image Preview Box -->
                            <div x-show="photoPreview" x-cloak class="mt-2">
                                <span class="text-[10px] font-bold text-slate-500 uppercase">Preview Foto:</span>
                                <div class="mt-1 relative w-full h-44 rounded-xl overflow-hidden border border-slate-300 shadow-inner bg-slate-100">
                                    <img :src="photoPreview" alt="Preview Foto Serah Terima" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                            <span>✓ Selesai Antar & Unggah Bukti</span>
                        </button>
                    </form>
                @endif
            @endif

        </div>
    @else
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold text-center space-y-1">
            <div class="text-base">✓</div>
            <div>Tugas ini telah selesai dikerjakan pada {{ $assignment->finished_at ? $assignment->finished_at->format('d M Y, H:i') : '' }}.</div>
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
