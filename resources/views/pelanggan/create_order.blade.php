@extends('layouts.mobile_pelanggan')

@section('content')
<div class="space-y-5 w-full max-w-full overflow-hidden" x-data="orderForm()">
    
    <!-- Header Banner -->
    <div class="w-full max-w-full overflow-hidden rounded-2xl p-4.5 sm:p-5 text-white shadow-xl shadow-[#3da4e0]/25 flex items-center justify-between bg-[#3da4e0] bg-gradient-to-r from-[#3da4e0] via-[#2ba0e5] to-[#167dc0]"
         style="background: linear-gradient(135deg, #3da4e0 0%, #2092d6 50%, #167dc0 100%) !important;">
        <div class="min-w-0 flex-1 mr-3 space-y-1">
            <span class="inline-block px-2.5 py-0.5 rounded-full bg-white text-[#166ba2] text-[10px] font-black uppercase tracking-wider shadow-sm">
                Pesan Antar Jemput
            </span>
            <h2 class="text-xl font-black text-white leading-tight drop-shadow-sm truncate">
                Buat Pesanan Laundry
            </h2>
            <p class="text-xs text-sky-100 font-medium leading-relaxed truncate">
                Driver kami siap jemput & antar cucian Anda sampai wangi!
            </p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-white text-[#3da4e0] flex items-center justify-center shrink-0 shadow-md">
            <i class="fa-solid fa-shirt text-xl"></i>
        </div>
    </div>

    <!-- Order Form -->
    <form action="{{ route('pelanggan.orders.store') }}" method="POST" class="space-y-4 w-full max-w-full overflow-hidden" @submit="handleSubmit($event)">
        @csrf

        <!-- 1. Data Pelanggan -->
        <div class="w-full max-w-full overflow-hidden bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-100 dark:border-slate-800 shadow-sm space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#3da4e0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Informasi Kontak
            </h3>

            <div class="w-full max-w-full overflow-hidden">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap</label>
                <input type="text" name="customer_name" required value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                       class="w-full max-w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                       placeholder="Contoh: Budi Santoso">
                @error('customer_name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="w-full max-w-full overflow-hidden">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp / HP</label>
                <input type="tel" name="customer_phone" required value="{{ old('customer_phone', auth()->user()->phone ?? '') }}"
                       class="w-full max-w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                       placeholder="Contoh: 081234567890">
                @error('customer_phone')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- 2. Pilih Layanan -->
        <div class="w-full max-w-full overflow-hidden bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-100 dark:border-slate-800 shadow-sm space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-shirt text-sm text-[#3da4e0]"></i>
                Pilihan Paket Laundry
            </h3>

            <!-- Layanan Dropdown with Mobile-Friendly Truncation & Helper Info -->
            <div class="w-full max-w-full overflow-hidden space-y-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Layanan Utama</label>
                <select name="id_layanan"
                        x-model="selectedLayanan" 
                        class="w-full max-w-full truncate text-ellipsis bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs rounded-xl p-3 focus:ring-2 focus:ring-[#3da4e0] focus:outline-none transition"
                        required>
                    <option value="">-- Pilih Layanan --</option>
                    <template x-for="item in layananList" :key="item.id_layanan">
                        <option :value="item.id_layanan" 
                                x-text="item.name + ' • Rp ' + Number(item.price_per_kg).toLocaleString('id-ID') + (item.service_type === 'kiloan' ? '/kg' : '/pcs')">
                        </option>
                    </template>
                </select>
                
                <!-- Dynamic Helper Text Below Select Box -->
                <template x-if="getSelectedLayananInfo()">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-[#3da4e0]"></i>
                        <span x-text="getSelectedLayananInfo().description"></span>
                    </p>
                </template>
            </div>

            <!-- Jadwal Penjemputan -->
            <div class="w-full max-w-full overflow-hidden pt-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jadwal Penjemputan</label>
                <input type="datetime-local" name="pickup_schedule" required 
                       min="{{ now()->addHours(1)->format('Y-m-d\TH:i') }}"
                       value="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}"
                       class="w-full max-w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition">
            </div>

            <!-- Notice: Konfirmasi Detail & Tarif di Outlet -->
            <div class="w-full max-w-full overflow-hidden p-3.5 rounded-xl bg-sky-50 dark:bg-sky-950/30 border border-sky-100 dark:border-sky-800/50 flex items-start gap-2.5 text-xs text-sky-900 dark:text-sky-200 shadow-sm">
                <svg class="w-5 h-5 text-[#3da4e0] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="space-y-0.5 min-w-0 flex-1">
                    <span class="font-bold text-sky-950 dark:text-sky-100">Konfirmasi Detail & Tarif di Outlet:</span>
                    <p class="text-[11px] text-sky-800 dark:text-sky-300 leading-relaxed">
                        Anda cukup memilih layanan utama. Penimbangan berat (kiloan), pengecekan jenis pakaian / item satuan, serta durasi pengerjaan (reguler/express) akan dikonfirmasi dan dihitung secara resmi oleh pihak outlet saat pakaian diterima untuk menerbitkan tagihan yang pasti.
                    </p>
                </div>
            </div>
        </div>

        <!-- 3. Lokasi & Map Picker (Leaflet + OpenStreetMap) -->
        <div class="w-full max-w-full bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-100 dark:border-slate-800 shadow-sm space-y-3 relative">
            <div class="w-full max-w-full flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#3da4e0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pilih Lokasi Penjemputan
                </h3>
                
                <!-- GPS button -->
                <button type="button" @click="getCurrentLocation()"
                        :disabled="isLocatingGps"
                        class="text-[11px] font-semibold text-[#3da4e0] hover:text-[#1b85c8] flex items-center gap-1.5 bg-[#3da4e0]/10 hover:bg-[#3da4e0]/20 px-2.5 py-1.5 rounded-lg transition shrink-0 disabled:opacity-50">
                    <template x-if="isLocatingGps">
                        <svg class="animate-spin w-3.5 h-3.5 text-[#3da4e0]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <template x-if="!isLocatingGps">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <span>Lokasi Saya</span>
                </button>
            </div>

            <!-- Input Alamat di Peta (Search Box) -->
            <div class="relative w-full" @click.outside="showSearchResults = false">
                <div class="relative flex items-center">
                    <div class="absolute left-3 text-slate-400 dark:text-slate-500 pointer-events-none flex items-center">
                        <svg class="w-4 h-4 text-[#3da4e0]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                           x-model="searchQuery"
                           @input="handleSearchInput()"
                           @keydown.enter.prevent="searchAddress(true)"
                           @focus="if(searchResults.length > 0) showSearchResults = true"
                           placeholder="Ketik alamat untuk sesuaikan titik di peta..."
                           class="w-full pl-9 pr-24 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-xl border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition shadow-sm placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                    
                    <div class="absolute right-1.5 flex items-center gap-1">
                        <!-- Clear button -->
                        <button type="button" 
                                x-show="searchQuery && searchQuery.length > 0" 
                                @click="searchQuery = ''; searchResults = []; showSearchResults = false; searchError = ''"
                                class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded-lg text-xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Search Button -->
                        <button type="button"
                                @click="searchAddress(true)"
                                :disabled="isSearching"
                                class="px-2.5 py-1.5 bg-[#3da4e0] hover:bg-[#1b85c8] text-white rounded-lg text-[11px] font-bold flex items-center gap-1 shadow-sm transition active:scale-95 disabled:opacity-50">
                            <template x-if="isSearching">
                                <svg class="animate-spin w-3 h-3 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <template x-if="!isSearching">
                                <span>Cari</span>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- Dropdown Hasil Pencarian -->
                <div x-show="showSearchResults"
                     x-transition
                     x-cloak
                     class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xl max-h-56 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                    
                    <!-- Searching loader -->
                    <div x-show="isSearching" class="p-3 text-center text-xs text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2">
                        <svg class="animate-spin w-4 h-4 text-[#3da4e0]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mencari lokasi di peta...</span>
                    </div>

                    <!-- Error or not found -->
                    <div x-show="!isSearching && searchError" class="p-3 text-xs text-rose-500 dark:text-rose-400 flex items-start gap-2">
                        <span class="text-sm">⚠️</span>
                        <span x-text="searchError"></span>
                    </div>

                    <!-- Results List -->
                    <template x-for="(result, index) in searchResults" :key="index">
                        <button type="button"
                                @click="selectAddress(result)"
                                class="w-full text-left p-2.5 hover:bg-sky-50/60 dark:hover:bg-slate-800 transition flex items-start gap-2.5 group">
                            <div class="mt-0.5 text-[#3da4e0] shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate" x-text="result.name || result.display_name.split(',')[0]"></p>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5" x-text="result.display_name"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Map Container -->
            <div class="relative w-full max-w-full rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-inner">
                <div id="order-map" class="w-full h-56 z-10"></div>
                <div class="absolute bottom-2 left-2 z-20 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm px-2.5 py-1 rounded-lg text-[10px] text-slate-600 dark:text-slate-300 font-medium shadow border border-slate-200 dark:border-slate-700">
                    💡 Geser pin atau ketik alamat di atas untuk sesuaikan titik
                </div>
            </div>

            <!-- Dynamic Distance Alert Box -->
            <div x-cloak class="w-full max-w-full overflow-hidden transition-all duration-300">
                <template x-if="distanceKm !== null">
                    <div :class="isValidDistance 
                        ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200' 
                        : 'bg-rose-50 dark:bg-rose-950/30 border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200'"
                         class="p-3.5 rounded-xl border flex items-start gap-3 shadow-sm w-full max-w-full">
                        
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
                        <div class="flex-1 min-w-0 text-xs">
                            <div class="font-bold flex items-center justify-between">
                                <span class="truncate" x-text="isValidDistance ? 'Lokasi Dalam Jangkauan' : 'Lokasi di Luar Jangkauan!'"></span>
                                <span class="font-extrabold text-sm ml-2 shrink-0" x-text="distanceKm + ' KM'"></span>
                            </div>
                            <p class="mt-0.5 text-slate-600 dark:text-slate-300 leading-relaxed" x-show="isValidDistance">
                                Estimasi jarak ke outlet <strong><span x-text="distanceKm"></span> KM</strong> (Maksimal radius 20 KM). Layanan pickup siap berangkat!
                            </p>
                            <p class="mt-0.5 text-rose-700 dark:text-rose-300 font-medium leading-relaxed" x-show="!isValidDistance">
                                Maaf, lokasi Anda di luar jangkauan (Maksimal radius 20 KM). Silakan pilih lokasi yang lebih dekat dengan outlet kami.
                            </p>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Alamat Textarea -->
            <div class="w-full max-w-full overflow-hidden">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Detail Alamat Lengkap</label>
                    <button type="button" 
                            @click="geocodeManualAddress()" 
                            class="text-[11px] font-semibold text-[#3da4e0] hover:text-[#1b85c8] flex items-center gap-1 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <span>Sesuaikan Titik dari Alamat</span>
                    </button>
                </div>
                <textarea name="address_text" rows="2" required x-model="addressText"
                          class="w-full max-w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                          placeholder="Nama jalan, nomor rumah, RT/RW, patokan lokasi..."></textarea>
                @error('address_text')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan Khusus -->
            <div class="w-full max-w-full overflow-hidden">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Khusus (Opsional)</label>
                <textarea name="notes" rows="1"
                          class="w-full max-w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                          placeholder="Contoh: Baju putih dipisah, jangan pakai pelembut..."></textarea>
            </div>

            <!-- Hidden Inputs for Coordinates & Distance -->
            <input type="hidden" name="latitude" x-model="latitude">
            <input type="hidden" name="longitude" x-model="longitude">
            <input type="hidden" name="distance_km" x-model="distanceKm">
        </div>

        <!-- Submit Button (Disabled completely if distance > 20 KM) -->
        <div class="pt-2 w-full max-w-full overflow-hidden">
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

    <!-- Modal Backdrop Overlay -->
    <div x-show="showOutOfRangeModal" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         x-transition.opacity
         x-cloak>
        
        <!-- Modal Card (Constrained to Mobile Width) -->
        <div class="bg-white rounded-3xl p-6 w-full max-w-xs mx-auto shadow-2xl text-center space-y-4"
             @click.outside="showOutOfRangeModal = false">
            
            <!-- Icon Pin -->
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto text-xl shadow-sm">
                <i class="fa-solid fa-location-dot"></i>
            </div>

            <h3 class="font-bold text-slate-800 text-base">Area di Luar Jangkauan!</h3>
            
            <p class="text-xs text-slate-500 leading-relaxed">
                Maaf, lokasi Anda (<span class="font-bold text-rose-500" x-text="distance"></span> KM) melebihi radius maksimal layanan antar-jemput kami (20 KM). Silakan pilih lokasi lain.
            </p>

            <button type="button" 
                    @click="showOutOfRangeModal = false"
                    class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition">
                Pilih Lokasi Lain
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function orderForm() {
    return {
        // Services JSON from server
        allLayanan: @json($layananList),
        layananList: @json($layananList),
        selectedLayanan: '{{ $layananList->first()?->id_layanan ?? "" }}',
        isKiloan: true,

        // Outlet Coordinates (Jakarta Monas default)
        outletLat: {{ config('services.outlet.latitude', -6.175392) }},
        outletLng: {{ config('services.outlet.longitude', 106.827153) }},
        maxRadiusKm: {{ config('services.outlet.max_radius_km', 20.0) }},

        // Customer Coordinates (Default near outlet)
        latitude: -6.180500,
        longitude: 106.832000,
        addressText: '',
        distanceKm: null,
        distance: 0,
        isValidDistance: true,
        isCalculating: false,
        showOutOfRangeModal: false,

        // Address Search state
        searchQuery: '',
        searchResults: [],
        isSearching: false,
        searchTimeout: null,
        showSearchResults: false,
        searchError: '',
        isLocatingGps: false,

        // Map instances
        map: null,
        customerMarker: null,
        outletMarker: null,
        routeLine: null,

        init() {
            if (!this.selectedLayanan && this.layananList.length > 0) {
                this.selectedLayanan = this.layananList[0].id_layanan;
            }
            this.updateLayananChange();
            this.$watch('selectedLayanan', () => {
                this.updateLayananChange();
            });
            this.$nextTick(() => {
                this.initLeafletMap();
            });
        },

        updateLayananChange() {
            const currentId = this.selectedLayanan;
            const found = this.layananList.find(l => l.id_layanan == currentId);
            if (found) {
                this.isKiloan = found.service_type === 'kiloan';
            }
        },

        getSelectedLayananInfo() {
            const currentId = this.selectedLayanan;
            const found = this.layananList.find(l => l.id_layanan == currentId);
            if (!found) return null;

            return {
                name: found.name,
                service_type: found.service_type,
                price: found.price_per_kg,
                description: found.description || (found.service_type === 'kiloan'
                    ? 'Cuci + Kering + Setrika rapi (Paket Kiloan, ditimbang presisi saat tiba di outlet)'
                    : 'Pencucian pakaian satuan / per potong (Dihitung resmi saat tiba di outlet)')
            };
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
                this.updateCoordinates(pos.lat, pos.lng, true);
            });

            // Event: Click on map to move marker
            this.map.on('click', (e) => {
                this.customerMarker.setLatLng(e.latlng);
                this.updateCoordinates(e.latlng.lat, e.latlng.lng, true);
            });

            // Calculate initial distance
            this.calculateDistance(this.latitude, this.longitude);

            // Initial reverse geocode if addressText is empty
            if (!this.addressText) {
                this.reverseGeocode(this.latitude, this.longitude);
            }
        },

        // Handle typing in search input with debounce
        handleSearchInput() {
            clearTimeout(this.searchTimeout);
            this.searchError = '';
            const q = this.searchQuery ? this.searchQuery.trim() : '';
            if (q.length < 3) {
                this.searchResults = [];
                this.showSearchResults = false;
                return;
            }
            this.searchTimeout = setTimeout(() => {
                this.searchAddress(false);
            }, 400);
        },

        // Search address via OpenStreetMap Nominatim
        async searchAddress(autoSelectFirst = false) {
            const q = this.searchQuery ? this.searchQuery.trim() : '';
            if (!q || q.length < 2) {
                this.searchError = 'Ketik minimal 2 karakter alamat untuk mencari.';
                this.showSearchResults = true;
                return;
            }

            this.isSearching = true;
            this.searchError = '';
            this.showSearchResults = true;

            // Viewbox boundary around outlet to prioritize nearby locations
            const boxDelta = 0.35;
            const left = (this.outletLng - boxDelta).toFixed(4);
            const right = (this.outletLng + boxDelta).toFixed(4);
            const top = (this.outletLat + boxDelta).toFixed(4);
            const bottom = (this.outletLat - boxDelta).toFixed(4);

            try {
                // Prioritize area around outlet first
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=5&countrycodes=id&addressdetails=1&viewbox=${left},${top},${right},${bottom}`;
                const res = await fetch(url, {
                    headers: { 'Accept-Language': 'id' }
                });
                let data = await res.json();

                // If nothing found in biased viewbox, fallback to general search in Indonesia
                if (!Array.isArray(data) || data.length === 0) {
                    const fallbackUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=5&countrycodes=id&addressdetails=1`;
                    const fallbackRes = await fetch(fallbackUrl, {
                        headers: { 'Accept-Language': 'id' }
                    });
                    data = await fallbackRes.json();
                }

                this.isSearching = false;

                if (Array.isArray(data) && data.length > 0) {
                    this.searchResults = data;
                    if (autoSelectFirst) {
                        this.selectAddress(data[0]);
                    }
                } else {
                    this.searchResults = [];
                    this.searchError = 'Alamat tidak ditemukan. Coba ketik nama jalan, patokan, atau daerah lebih lengkap.';
                }
            } catch (err) {
                this.isSearching = false;
                this.searchResults = [];
                this.searchError = 'Gagal mencari alamat. Periksa koneksi internet Anda.';
            }
        },

        // Select an address from results and adjust the marker point
        selectAddress(item) {
            const lat = parseFloat(item.lat);
            const lng = parseFloat(item.lon);
            if (isNaN(lat) || isNaN(lng)) return;

            // Move marker to new coordinates
            if (this.customerMarker) {
                this.customerMarker.setLatLng([lat, lng]);
            }

            // Fly map to selected coordinates
            if (this.map) {
                this.map.flyTo([lat, lng], 16, { animate: true, duration: 1.2 });
            }

            // Sync address texts
            this.addressText = item.display_name;
            this.searchQuery = item.name || item.display_name;
            this.searchResults = [];
            this.showSearchResults = false;
            this.searchError = '';

            // Update internal coordinates, polyline, and distance (false to prevent reverseGeocode overwriting)
            this.updateCoordinates(lat, lng, false);
        },

        // Helper to adjust pin directly from address entered in the textarea
        geocodeManualAddress() {
            if (!this.addressText || this.addressText.trim().length < 3) {
                alert('Silakan ketik alamat pada kolom Detail Alamat Lengkap terlebih dahulu.');
                return;
            }
            this.searchQuery = this.addressText.trim();
            this.searchAddress(true);
        },

        updateCoordinates(lat, lng, doReverse = true) {
            this.latitude = parseFloat(Number(lat).toFixed(7));
            this.longitude = parseFloat(Number(lng).toFixed(7));

            // Update polyline
            if (this.routeLine) {
                this.routeLine.setLatLngs([
                    [this.outletLat, this.outletLng],
                    [this.latitude, this.longitude]
                ]);
            }

            // Recalculate distance
            this.calculateDistance(this.latitude, this.longitude);

            // Reverse geocode address suggestion via OpenStreetMap Nominatim
            if (doReverse) {
                this.reverseGeocode(this.latitude, this.longitude);
            }
        },

        // Client-side Haversine formula calculation
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
            this.distance = d;
            this.isValidDistance = (d <= this.maxRadiusKm);

            // Trigger Pop-Up Modal when distance > 20 KM
            if (!this.isValidDistance) {
                this.showOutOfRangeModal = true;
            }

            // Update polyline style based on validation
            if (this.routeLine) {
                this.routeLine.setStyle({
                    color: this.isValidDistance ? '#3da4e0' : '#ef4444'
                });
            }
        },

        getCurrentLocation() {
            if (navigator.geolocation) {
                this.isLocatingGps = true;
                this.isCalculating = true;
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        if (this.customerMarker && this.map) {
                            this.customerMarker.setLatLng([lat, lng]);
                            this.map.flyTo([lat, lng], 16, { animate: true, duration: 1.2 });
                        }
                        this.updateCoordinates(lat, lng, true);
                        this.isLocatingGps = false;
                        this.isCalculating = false;
                    },
                    (error) => {
                        alert('Tidak dapat mendeteksi lokasi GPS otomatis: ' + error.message);
                        this.isLocatingGps = false;
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
                if (data && data.display_name) {
                    this.addressText = data.display_name;
                    this.searchQuery = data.display_name;
                }
            })
            .catch(() => {});
        },

        handleSubmit(e) {
            if (!this.isValidDistance || (this.distanceKm !== null && this.distanceKm > this.maxRadiusKm)) {
                e.preventDefault();
                this.showOutOfRangeModal = true;
                return false;
            }
        }
    }
}
</script>
@endpush
