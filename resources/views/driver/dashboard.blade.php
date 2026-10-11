@extends('layouts.mobile_driver')

@section('content')
<div class="space-y-4" x-data="{ 
    showModal: false, 
    modalType: 'LAUNDRY_DIAMBIL', 
    photoPreview: null,
    openModal(type) { 
        this.modalType = type; 
        this.photoPreview = null; 
        this.showModal = true; 
    } 
}">

    <!-- Active Task Card -->
    @if($activeAssignment)
        @php
            $order = $activeAssignment->order;
            $isPickup = $activeAssignment->type === 'pickup';
        @endphp
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border-2 border-[#3da4e0]/40 shadow-xl space-y-4 relative overflow-hidden transition">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-xs font-black uppercase tracking-wider {{ $isPickup ? 'text-amber-600 dark:text-amber-400' : 'text-[#3da4e0]' }}">
                        Tugas {{ $isPickup ? 'Penjemputan' : 'Pengantaran' }}
                    </span>
                </div>
                <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500">{{ $order->order_code }}</span>
            </div>

            <!-- Customer Details & Distance -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">{{ $order->customer_name }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Jarak: <strong class="text-[#3da4e0]">{{ $order->distance_km }} KM</strong> dari Outlet</p>
                    </div>
                    @if($order->customer_phone)
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $order->customer_phone)) }}" 
                           target="_blank"
                           title="Hubungi WhatsApp"
                           class="p-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white shadow-md shadow-emerald-500/30 transition flex items-center justify-center">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                        </a>
                    @endif
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                    <span class="font-bold block text-slate-900 dark:text-white mb-0.5">📍 Alamat Pelanggan:</span>
                    {{ $order->address_text }}
                </div>

                @if($order->notes)
                    <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-900/40 text-[11px] text-amber-900 dark:text-amber-300">
                        <strong>Catatan Khusus:</strong> {{ $order->notes }}
                    </div>
                @endif
            </div>

            <!-- Action Controls according to state -->
            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2.5">
                
                @if($isPickup)
                    @if($order->status === \App\Models\Order::STATUS_DRIVER_DITUGASKAN)
                        <!-- 1. Pickup: Trigger Detail & Photo Upload Modal -->
                        <button type="button" 
                                @click="openModal('LAUNDRY_DIAMBIL')"
                                class="w-full py-3.5 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-bold text-xs shadow-lg shadow-[#3da4e0]/30 transition flex items-center justify-center gap-2 transform active:scale-[0.99] cursor-pointer">
                            <i class="fa-solid fa-camera"></i>
                            <span>🧺 Sudah Ambil Laundry dari Pelanggan</span>
                        </button>
                    @elseif($order->status === \App\Models\Order::STATUS_LAUNDRY_DIAMBIL)
                        <!-- 2. Pickup: Mark Arrived at Outlet -->
                        <form action="{{ route('driver.tasks.update', $activeAssignment->id_assignment) }}" method="POST">
                            @csrf
                            <button type="submit" name="target_status" value="SAMPAI_OUTLET"
                                    class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2 transform active:scale-[0.99] cursor-pointer">
                                <i class="fa-solid fa-house-chimney"></i>
                                <span>🏠 Sudah Sampai di Outlet Pusat</span>
                            </button>
                        </form>
                    @endif
                @else
                    @if($order->status === \App\Models\Order::STATUS_DRIVER_DITUGASKAN || $order->status === \App\Models\Order::STATUS_SIAP_DIANTAR)
                        <!-- 1. Delivery: Start Driving to Customer -->
                        <form action="{{ route('driver.tasks.update', $activeAssignment->id_assignment) }}" method="POST">
                            @csrf
                            <button type="submit" name="target_status" value="MENUNGGU_PENGANTARAN"
                                    class="w-full py-3.5 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-bold text-xs shadow-lg shadow-[#3da4e0]/30 transition flex items-center justify-center gap-2 transform active:scale-[0.99] cursor-pointer">
                                <i class="fa-solid fa-motorcycle"></i>
                                <span>🛵 Mulai Antar ke Pelanggan</span>
                            </button>
                        </form>
                    @elseif($order->status === \App\Models\Order::STATUS_MENUNGGU_PENGANTARAN)
                        <!-- 2. Delivery: Trigger Finish & Photo Upload Modal -->
                        <button type="button" 
                                @click="openModal('SELESAI')"
                                class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2 transform active:scale-[0.99] cursor-pointer">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>✓ Selesai Antar (Cucian Diterima)</span>
                        </button>
                    @endif
                @endif

                <!-- Navigation & Map Direct Link (Requirement 4.2) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $order->latitude }},{{ $order->longitude }}" 
                       target="_blank"
                       class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-[#3da4e0]/10 hover:text-[#3da4e0] hover:border-[#3da4e0]/40 transition text-center shadow-2xs">
                        <i class="fa-solid fa-map-location-dot text-[#3da4e0]"></i>
                        <span>Buka Peta & Navigasi</span>
                    </a>

                    <a href="{{ route('driver.orders.show', $activeAssignment->id_assignment) }}" 
                       class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 text-xs font-bold hover:text-[#3da4e0] hover:border-[#3da4e0]/40 transition text-center">
                        <i class="fa-solid fa-circle-info text-slate-400"></i>
                        <span>Detail Rute & Riwayat</span>
                    </a>
                </div>

            </div>
        </div>

        <!-- Detail Modal: Proof Photo & Status Confirmation (Alpine.js) -->
        <div x-show="showModal" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
             role="dialog" 
             aria-modal="true">
            <!-- Modal Backdrop -->
            <div @click="showModal = false" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

            <!-- Modal Content Card -->
            <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 space-y-4 z-10 transition-all transform">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#3da4e0]/10 text-[#3da4e0] flex items-center justify-center">
                            <i :class="modalType === 'LAUNDRY_DIAMBIL' ? 'fa-solid fa-shirt' : 'fa-solid fa-circle-check'"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white"
                                x-text="modalType === 'LAUNDRY_DIAMBIL' ? 'Konfirmasi Penjemputan Laundry' : 'Konfirmasi Pengantaran Selesai'"></h3>
                            <p class="text-[10px] text-slate-400">{{ $order->order_code }} &bull; {{ $order->customer_name }}</p>
                        </div>
                    </div>
                    <button @click="showModal = false" type="button" class="text-slate-400 hover:text-slate-700 dark:hover:text-white p-1">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-300">
                    <p class="font-bold text-slate-800 dark:text-white mb-0.5">📍 Lokasi: {{ $order->address_text }}</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                        <span x-text="modalType === 'LAUNDRY_DIAMBIL' ? 'Pastikan cucian sudah dicek bersama pelanggan sebelum konfirmasi.' : 'Pastikan pelanggan telah menerima cucian dalam kondisi baik.'"></span>
                    </p>
                </div>

                <!-- Update Status Form with Photo Upload -->
                <form action="{{ route('driver.tasks.update', $activeAssignment->id_assignment) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="target_status" :value="modalType">

                    <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/40 text-xs text-amber-900 dark:text-amber-300 space-y-2">
                        <div class="font-bold flex items-center gap-1.5 text-amber-800 dark:text-amber-300">
                            <i class="fa-solid fa-camera text-amber-600 dark:text-amber-400"></i>
                            <span x-text="modalType === 'LAUNDRY_DIAMBIL' ? 'Wajib Unggah Foto Penjemputan' : 'Wajib Unggah Foto Serah Terima'"></span>
                        </div>
                        <p class="text-[11px] text-amber-700 dark:text-amber-300/80 leading-relaxed">
                            Ambil foto pakaian / serah terima langsung di lokasi sebagai bukti operasional yang sah.
                        </p>

                        <!-- Camera / File Input -->
                        <div class="pt-1">
                            <input type="file" 
                                   name="proof_photo" 
                                   accept="image/*" 
                                   capture="environment" 
                                   required
                                   @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => photoPreview = e.target.result; reader.readAsDataURL(file); }"
                                   class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#3da4e0] file:text-white hover:file:bg-[#1b85c8] cursor-pointer">
                        </div>

                        <!-- Live Photo Preview Box -->
                        <div x-show="photoPreview" x-cloak class="mt-2">
                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Preview Foto:</span>
                            <div class="mt-1 relative w-full h-36 rounded-xl overflow-hidden border border-slate-300 dark:border-slate-700 shadow-inner bg-slate-100 dark:bg-slate-800">
                                <img :src="photoPreview" alt="Preview Bukti Foto" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <button type="button" 
                                @click="showModal = false"
                                class="w-1/3 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="w-2/3 py-3 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-bold text-xs shadow-lg shadow-[#3da4e0]/30 transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span x-text="modalType === 'LAUNDRY_DIAMBIL' ? 'Unggah & Konfirmasi Ambil' : 'Unggah & Selesaikan Tugas'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    @else
        <!-- No Active Task State -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-100 dark:border-slate-800 shadow-sm text-center space-y-3 transition">
            <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-3xl">
                🛵
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Belum Ada Tugas Aktif</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">
                    Tugas penjemputan atau pengantaran dari admin akan otomatis muncul di sini.
                </p>
            </div>
        </div>
    @endif

    <!-- Performance Stats -->
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-100 dark:border-slate-800 shadow-sm transition">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tugas Hari Ini</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $todayCompletedCount }}</span>
                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold">Selesai</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-100 dark:border-slate-800 shadow-sm transition">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tugas</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-[#3da4e0]">{{ $totalCompletedCount }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Terkirim</span>
            </div>
        </div>
    </div>

    <!-- Vehicle Info Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 text-white flex items-center justify-between shadow-md">
        <div>
            <span class="text-[10px] font-bold text-[#3da4e0] uppercase tracking-wider">Kendaraan Operasional</span>
            <h4 class="text-sm font-bold mt-0.5">{{ $driver->vehicle_type }}</h4>
            <p class="text-xs text-slate-400 font-mono">{{ $driver->plate_number }}</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-xl">
            🏍️
        </div>
    </div>

</div>
@endsection
