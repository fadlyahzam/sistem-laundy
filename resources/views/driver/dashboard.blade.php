@extends('layouts.mobile_driver')

@section('content')
<div class="space-y-4">

    <!-- Active Task Card -->
    @if($activeAssignment)
        @php
            $order = $activeAssignment->order;
            $isPickup = $activeAssignment->type === 'pickup';
        @endphp
        <div class="bg-white rounded-3xl p-5 border-2 border-[#3da4e0]/30 shadow-xl space-y-4 relative overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="text-xs font-black uppercase tracking-wider {{ $isPickup ? 'text-amber-600' : 'text-[#3da4e0]' }}">
                        Tugas {{ $isPickup ? 'Penjemputan' : 'Pengantaran' }}
                    </span>
                </div>
                <span class="text-[11px] font-bold text-slate-400">{{ $order->order_code }}</span>
            </div>

            <!-- Customer Details & Distance -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">{{ $order->customer_name }}</h3>
                        <p class="text-xs text-slate-500">Jarak: <strong class="text-[#3da4e0]">{{ $order->distance_km }} KM</strong> dari Outlet</p>
                    </div>
                    @if($order->customer_phone)
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $order->customer_phone)) }}" 
                           target="_blank"
                           class="p-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white shadow-md shadow-emerald-500/30 transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </a>
                    @endif
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed">
                    <span class="font-bold block text-slate-800 mb-0.5">📍 Alamat:</span>
                    {{ $order->address_text }}
                </div>

                @if($order->notes)
                    <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-100 text-[11px] text-amber-800">
                        <strong>Catatan:</strong> {{ $order->notes }}
                    </div>
                @endif
            </div>

            <!-- Action Controls according to state -->
            <div class="pt-2 border-t border-slate-100 space-y-2">
                <form action="{{ route('driver.tasks.update', $activeAssignment->id_assignment) }}" method="POST">
                    @csrf

                    @if($isPickup)
                        @if($order->status === \App\Models\Order::STATUS_DRIVER_DITUGASKAN)
                            <button type="submit" name="target_status" value="LAUNDRY_DIAMBIL"
                                    class="w-full py-3 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-bold text-xs shadow-lg shadow-[#3da4e0]/30 transition">
                                🧺 Sudah Ambil Laundry dari Pelanggan
                            </button>
                        @elseif($order->status === \App\Models\Order::STATUS_LAUNDRY_DIAMBIL)
                            <button type="submit" name="target_status" value="SAMPAI_OUTLET"
                                    class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition">
                                🏠 Sudah Sampai di Outlet Pusat
                            </button>
                        @endif
                    @else
                        @if($order->status === \App\Models\Order::STATUS_DRIVER_DITUGASKAN || $order->status === \App\Models\Order::STATUS_SIAP_DIANTAR)
                            <button type="submit" name="target_status" value="MENUNGGU_PENGANTARAN"
                                    class="w-full py-3 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-bold text-xs shadow-lg shadow-[#3da4e0]/30 transition">
                                🛵 Mulai Antar ke Pelanggan
                            </button>
                        @elseif($order->status === \App\Models\Order::STATUS_MENUNGGU_PENGANTARAN)
                            <button type="submit" name="target_status" value="SELESAI"
                                    class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition">
                                ✓ Selesai Antar (Cucian Diterima)
                            </button>
                        @endif
                    @endif
                </form>

                <a href="{{ route('driver.orders.show', $activeAssignment->id_assignment) }}" 
                   class="block text-center text-xs font-bold text-slate-500 hover:text-[#3da4e0] py-1">
                    Buka Peta & Navigasi &rarr;
                </a>
            </div>
        </div>
    @else
        <!-- No Active Task State -->
        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm text-center space-y-3">
            <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-3xl">
                🛵
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Belum Ada Tugas Aktif</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                    Tugas penjemputan atau pengantaran dari admin akan otomatis muncul di sini.
                </p>
            </div>
        </div>
    @endif

    <!-- Performance Stats -->
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tugas Hari Ini</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ $todayCompletedCount }}</span>
                <span class="text-xs text-emerald-600 font-bold">Selesai</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tugas</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-[#3da4e0]">{{ $totalCompletedCount }}</span>
                <span class="text-xs text-slate-500 font-medium">Terkirim</span>
            </div>
        </div>
    </div>

    <!-- Vehicle Info Card -->
    <div class="bg-slate-900 rounded-2xl p-4 text-white flex items-center justify-between shadow-md">
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
