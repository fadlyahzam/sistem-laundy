@extends('layouts.mobile_pelanggan')

@section('content')
<div class="space-y-5" x-data="orderForm()">
    
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#3da4e0] to-[#1b85c8] rounded-2xl p-4 text-white shadow-lg shadow-[#3da4e0]/20 flex items-center justify-between">
        <div>
            <span class="inline-block px-2 py-0.5 rounded-full bg-white/20 text-[10px] font-bold uppercase tracking-wider mb-1">Pesan Antar Jemput</span>
            <h2 class="text-lg font-extrabold leading-tight">Buat Pesanan Laundry</h2>
            <p class="text-xs text-white/85 mt-0.5">Driver kami siap jemput ke lokasi Anda!</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
    </div>

    <!-- Order Form -->
    <form action="{{ route('pelanggan.orders.store') }}" method="POST" class="space-y-4" @submit="handleSubmit($event)">
        @csrf

        <!-- 1. Data Pelanggan -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#3da4e0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Informasi Kontak
            </h3>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="customer_name" required value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                       placeholder="Contoh: Budi Santoso">
                @error('customer_name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                <input type="tel" name="customer_phone" required value="{{ old('customer_phone', auth()->user()->phone ?? '') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                       placeholder="Contoh: 081234567890">
                @error('customer_phone')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- 2. Pilih Layanan & Kategori -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#3da4e0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                Pilihan Paket Laundry
            </h3>

            <!-- Layanan Dropdown -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Layanan Utama</label>
                <select name="id_layanan" x-model="selectedLayananId" @change="updateLayananChange()" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition bg-white">
                    <option value="" disabled selected>-- Pilih Layanan --</option>
                    @foreach($layananList as $layanan)
                        <option value="{{ $layanan->id_layanan }}" 
                                data-type="{{ $layanan->service_type }}" 
                                data-price="{{ $layanan->price_per_kg }}">
                            {{ $layanan->name }} ({{ ucfirst($layanan->service_type) }} - Rp {{ number_format($layanan->price_per_kg, 0, ',', '.') }}/kg)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Optional Kategori Dropdown -->
            <div x-show="availableKategori.length > 0" x-cloak>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori / Item Khusus (Opsional)</label>
                <select name="id_kategori" x-model="selectedKategoriId"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition bg-white">
                    <option value="">-- Tanpa Kategori Khusus --</option>
                    <template x-for="kat in availableKategori" :key="kat.id_kategori">
                        <option :value="kat.id_kategori" x-text="kat.name + ' (+Rp ' + Number(kat.unit_tariff).toLocaleString('id-ID') + ')'"></option>
                    </template>
                </select>
            </div>

            <!-- Estimasi Jumlah / Berat -->
            <div class="grid grid-cols-2 gap-3 pt-1">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Estimasi Kuantitas</label>
                    <div class="relative">
                        <input type="number" step="0.5" min="1" name="quantity" x-model="estimatedQty" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                               placeholder="1">
                        <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-medium" x-text="isKiloan ? 'Kg' : 'Pcs'">Kg</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jadwal Penjemputan</label>
                    <input type="datetime-local" name="pickup_schedule" required 
                           min="{{ now()->addHours(1)->format('Y-m-d\TH:i') }}"
                           value="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition">
                </div>
            </div>
        </div>

        <!-- 3. Lokasi & Map Picker (Leaflet + OpenStreetMap) -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#3da4e0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pilih Lokasi Penjemputan
                </h3>
                
                <!-- GPS button -->
                <button type="button" @click="getCurrentLocation()"
                        class="text-[11px] font-semibold text-[#3da4e0] hover:text-[#1b85c8] flex items-center gap-1 bg-[#3da4e0]/10 px-2.5 py-1 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Lokasi Saya
                </button>
            </div>

            <!-- Map Container -->
            <div class="relative rounded-xl overflow-hidden border border-slate-200 shadow-inner">
                <div id="order-map" class="w-full h-56 z-10"></div>
                <div class="absolute bottom-2 left-2 z-20 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-lg text-[10px] text-slate-600 font-medium shadow border border-slate-200">
                    💡 Geser pin untuk menentukan alamat akurat
                </div>
            </div>

            <!-- Dynamic Distance Alert Box -->
            <div x-cloak class="transition-all duration-300">
                <template x-if="distanceKm !== null">
                    <div :class="isValidDistance 
                        ? 'bg-emerald-50 border-emerald-200 text-emerald-900' 
                        : 'bg-rose-50 border-rose-200 text-rose-900'"
                         class="p-3.5 rounded-xl border flex items-start gap-3 shadow-sm">
                        
                        <!-- Icon -->
                        <div class="shrink-0 mt-0.5">
                            <template x-if="isValidDistance">
                                <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold">✓</div>
                            </template>
                            <template x-if="!isValidDistance">
                                <div class="w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs font-bold">✕</div>
                            </template>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 text-xs">
                            <div class="font-bold flex items-center justify-between">
                                <span x-text="isValidDistance ? 'Lokasi Dalam Jangkauan' : 'Lokasi di Luar Jangkauan!'"></span>
                                <span class="font-extrabold text-sm" x-text="distanceKm + ' KM'"></span>
                            </div>
                            <p class="mt-0.5 text-slate-600" x-show="isValidDistance">
                                Estimasi jarak ke outlet <strong><span x-text="distanceKm"></span> KM</strong> (Maksimal radius 20 KM). Layanan pickup siap berangkat!
                            </p>
                            <p class="mt-0.5 text-rose-700 font-medium" x-show="!isValidDistance">
                                Maaf, lokasi Anda di luar jangkauan (Maksimal radius 20 KM). Silakan pilih lokasi yang lebih dekat dengan outlet kami.
                            </p>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Alamat Textarea -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Detail Alamat Lengkap</label>
                <textarea name="address_text" rows="2" required x-model="addressText"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                          placeholder="Nama jalan, nomor rumah, RT/RW, patokan lokasi..."></textarea>
                @error('address_text')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan Khusus -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Khusus (Opsional)</label>
                <textarea name="notes" rows="1"
                          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                          placeholder="Contoh: Baju putih dipisah, jangan pakai pelembut..."></textarea>
            </div>

            <!-- Hidden Inputs for Coordinates & Distance -->
            <input type="hidden" name="latitude" x-model="latitude">
            <input type="hidden" name="longitude" x-model="longitude">
            <input type="hidden" name="distance_km" x-model="distanceKm">
        </div>

        <!-- Submit Button (Disabled if distance > 20 KM) -->
        <div class="pt-2">
            <button type="submit" 
                    :disabled="!isValidDistance || isCalculating"
                    :class="(!isValidDistance || isCalculating) 
                        ? 'bg-slate-300 text-slate-500 cursor-not-allowed shadow-none' 
                        : 'bg-[#3da4e0] hover:bg-[#1b85c8] text-white shadow-lg shadow-[#3da4e0]/30 active:scale-[0.99]'"
                    class="w-full py-3.5 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                <template x-if="isCalculating">
                    <span>Menghitung Jarak...</span>
                </template>
                <template x-if="!isCalculating && isValidDistance">
                    <span>Konfirmasi & Buat Pesanan</span>
                </template>
                <template x-if="!isCalculating && !isValidDistance">
                    <span>Lokasi Melebihi Radius 20 KM</span>
                </template>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
            <p class="text-[11px] text-center text-slate-400 mt-2">
                Setelah pesanan dibuat, tim admin akan mengonfirmasi penugasan driver.
            </p>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
function orderForm() {
    return {
        // Services & Categories JSON from server
        allLayanan: @json($layananList),
        selectedLayananId: '{{ $layananList->first()?->id_layanan ?? "" }}',
        selectedKategoriId: '',
        availableKategori: [],
        isKiloan: true,
        estimatedQty: 2,

        // Outlet Coordinates (Jakarta Monas default)
        outletLat: {{ config('services.outlet.latitude', -6.175392) }},
        outletLng: {{ config('services.outlet.longitude', 106.827153) }},
        maxRadiusKm: {{ config('services.outlet.max_radius_km', 20.0) }},

        // Customer Coordinates (Default near outlet)
        latitude: -6.180500,
        longitude: 106.832000,
        addressText: '',
        distanceKm: null,
        isValidDistance: true,
        isCalculating: false,

        // Map instances
        map: null,
        customerMarker: null,
        outletMarker: null,
        routeLine: null,

        init() {
            this.updateLayananChange();
            this.$nextTick(() => {
                this.initLeafletMap();
            });
        },

        updateLayananChange() {
            const found = this.allLayanan.find(l => l.id_layanan == this.selectedLayananId);
            if (found) {
                this.isKiloan = found.service_type === 'kiloan';
                this.availableKategori = found.kategori || [];
                this.selectedKategoriId = '';
            }
        },

        initLeafletMap() {
            const defaultPos = [this.latitude, this.longitude];
            const outletPos = [this.outletLat, this.outletLng];

            // Initialize Map
            this.map = L.map('order-map', {
                center: defaultPos,
                zoom: 13,
                zoomControl: true
            });

            // Add OpenStreetMap Tile Layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(this.map);

            // Outlet Icon & Marker
            const outletIcon = L.divIcon({
                className: 'custom-outlet-marker',
                html: `<div style="background-color: #0f3049; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border: 2px solid white; font-size: 16px;">🏠</div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });

            this.outletMarker = L.marker(outletPos, { icon: outletIcon })
                .addTo(this.map)
                .bindPopup('<b>LaundryKu Pusat</b><br>Outlet Utama');

            // Customer Icon & Draggable Marker
            const customerIcon = L.divIcon({
                className: 'custom-customer-marker',
                html: `<div style="background-color: #3da4e0; color: white; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(61,164,224,0.5); border: 2.5px solid white; font-size: 16px; cursor: grab;">📍</div>`,
                iconSize: [34, 34],
                iconAnchor: [17, 34]
            });

            this.customerMarker = L.marker(defaultPos, {
                draggable: true,
                icon: customerIcon
            }).addTo(this.map);

            // Polyline connecting Outlet to Customer
            this.routeLine = L.polyline([outletPos, defaultPos], {
                color: '#3da4e0',
                weight: 3,
                dashArray: '6, 6',
                opacity: 0.8
            }).addTo(this.map);

            // Event: Marker dragged
            this.customerMarker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                this.updateCoordinates(pos.lat, pos.lng);
            });

            // Event: Click on map to move marker
            this.map.on('click', (e) => {
                this.customerMarker.setLatLng(e.latlng);
                this.updateCoordinates(e.latlng.lat, e.latlng.lng);
            });

            // Calculate initial distance
            this.calculateDistance(this.latitude, this.longitude);
        },

        updateCoordinates(lat, lng) {
            this.latitude = parseFloat(lat.toFixed(7));
            this.longitude = parseFloat(lng.toFixed(7));

            // Update polyline
            if (this.routeLine) {
                this.routeLine.setLatLngs([
                    [this.outletLat, this.outletLng],
                    [this.latitude, this.longitude]
                ]);
            }

            // Reverse geocode address suggestion via OpenStreetMap Nominatim
            this.reverseGeocode(this.latitude, this.longitude);

            // Recalculate distance
            this.calculateDistance(this.latitude, this.longitude);
        },

        // Client-side Haversine formula calculation with API confirmation
        calculateDistance(lat, lng) {
            const R = 6371; // Earth radius in KM
            const dLat = (lat - this.outletLat) * Math.PI / 180;
            const dLon = (lng - this.outletLng) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(this.outletLat * Math.PI / 180) * Math.cos(lat * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            const d = parseFloat((R * c).toFixed(2));

            this.distanceKm = d;
            this.isValidDistance = (d <= this.maxRadiusKm);

            // Update polyline style based on validation
            if (this.routeLine) {
                this.routeLine.setStyle({
                    color: this.isValidDistance ? '#3da4e0' : '#ef4444'
                });
            }
        },

        getCurrentLocation() {
            if (navigator.geolocation) {
                this.isCalculating = true;
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        if (this.customerMarker && this.map) {
                            this.customerMarker.setLatLng([lat, lng]);
                            this.map.setView([lat, lng], 15);
                        }
                        this.updateCoordinates(lat, lng);
                        this.isCalculating = false;
                    },
                    (error) => {
                        alert('Tidak dapat mendeteksi lokasi GPS otomatis: ' + error.message);
                        this.isCalculating = false;
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            } else {
                alert('Browser Anda tidak mendukung geolokasi.');
            }
        },

        reverseGeocode(lat, lng) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
                headers: { 'Accept-Language': 'id' }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.display_name && !this.addressText) {
                    this.addressText = data.display_name;
                }
            })
            .catch(() => {});
        },

        handleSubmit(e) {
            if (!this.isValidDistance) {
                e.preventDefault();
                alert('Maaf, lokasi Anda di luar jangkauan (Maksimal radius 20 KM).');
                return false;
            }
        }
    }
}
</script>
@endpush
