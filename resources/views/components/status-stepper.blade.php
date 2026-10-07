@props(['currentStatus' => 'MENUNGGU_KONFIRMASI'])

@php
    $steps = [
        ['code' => 'MENUNGGU_KONFIRMASI', 'title' => 'Menunggu Konfirmasi', 'desc' => 'Admin meninjau pesanan'],
        ['code' => 'DRIVER_DITUGASKAN',   'title' => 'Driver Ditugaskan',   'desc' => 'Menuju lokasi penjemputan'],
        ['code' => 'LAUNDRY_DIAMBIL',     'title' => 'Laundry Diambil',     'desc' => 'Cucian dibawa ke outlet'],
        ['code' => 'SAMPAI_OUTLET',       'title' => 'Sampai di Outlet',    'desc' => 'Penimbangan & sortir'],
        ['code' => 'MENUNGGU_PEMBAYARAN', 'title' => 'Menunggu Pembayaran', 'desc' => 'Siap bayar via QRIS'],
        ['code' => 'DIBAYAR',             'title' => 'Dibayar',             'desc' => 'Pembayaran terkonfirmasi'],
        ['code' => 'SEDANG_DIPROSES',     'title' => 'Sedang Diproses',     'desc' => 'Proses pencucian & setrika'],
        ['code' => 'SIAP_DIANTAR',        'title' => 'Siap Diantar',        'desc' => 'Packing rapi & wangi'],
        ['code' => 'MENUNGGU_PENGANTARAN','title' => 'Menunggu Pengantaran','desc' => 'Driver mengantar ke lokasi'],
        ['code' => 'SELESAI',             'title' => 'Selesai',             'desc' => 'Cucian diterima pelanggan'],
    ];

    $currentIndex = 0;
    foreach ($steps as $idx => $step) {
        if ($step['code'] === $currentStatus) {
            $currentIndex = $idx;
            break;
        }
    }
@endphp

<div class="w-full bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
    <div class="flex items-center justify-between mb-3 px-1">
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#3da4e0]">Status Pesanan</h3>
            <p class="text-sm font-extrabold text-slate-800">{{ $steps[$currentIndex]['title'] }}</p>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-[#3da4e0]/10 text-[#3da4e0]">
            Langkah {{ $currentIndex + 1 }} dari {{ count($steps) }}
        </span>
    </div>

    <!-- Horizontal Scrollable Bulletin Progress Bar -->
    <div class="relative overflow-x-auto no-scrollbar py-2" x-data x-init="$nextTick(() => {
        const activeEl = $el.querySelector('[data-active=true]');
        if (activeEl) {
            activeEl.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }
    })">
        <div class="flex items-center min-w-max px-2">
            @foreach($steps as $index => $step)
                @php
                    $isPassed = $index < $currentIndex;
                    $isActive = $index === $currentIndex;
                    $isFuture = $index > $currentIndex;
                @endphp

                <!-- Step Item -->
                <div class="flex items-center {{ $index === count($steps) - 1 ? '' : 'pr-2' }}"
                     data-active="{{ $isActive ? 'true' : 'false' }}">
                    <div class="flex flex-col items-center text-center w-28 group">
                        
                        <!-- Icon Circle -->
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all duration-300 relative
                            {{ $isPassed ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30' : '' }}
                            {{ $isActive ? 'bg-white border-2 border-[#3da4e0] text-[#3da4e0] shadow-lg shadow-[#3da4e0]/40 ring-4 ring-[#3da4e0]/20 scale-110' : '' }}
                            {{ $isFuture ? 'bg-slate-100 text-slate-400 border border-slate-200' : '' }}">
                            
                            @if($isPassed)
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @elseif($isActive)
                                <span class="relative flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#3da4e0] opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-[#3da4e0]"></span>
                                </span>
                            @else
                                <span>{{ $index + 1 }}</span>
                            @endif
                        </div>

                        <!-- Step Labels -->
                        <div class="mt-2.5">
                            <p class="text-xs font-bold leading-tight line-clamp-2
                                {{ $isActive ? 'text-[#3da4e0]' : ($isPassed ? 'text-slate-800' : 'text-slate-400') }}">
                                {{ $step['title'] }}
                            </p>
                            <p class="text-[10px] text-slate-400 mt-0.5 leading-tight hidden sm:block">
                                {{ $step['desc'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Connecting Line -->
                    @if($index < count($steps) - 1)
                        <div class="w-12 h-0.5 transition-all duration-300 -mt-6
                            {{ $index < $currentIndex ? 'bg-[#3da4e0]' : 'bg-slate-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
