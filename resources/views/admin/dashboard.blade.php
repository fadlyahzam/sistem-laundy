@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Top KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Omset -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between transition">
            <div>
                <span class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Total Pendapatan</span>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h3>
                <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 mt-0.5">
                    ✓ Terbayar via QRIS
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-bold">
                💰
            </div>
        </div>

        <!-- Pesanan Aktif -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between transition">
            <div>
                <span class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Pesanan Aktif</span>
                <h3 class="text-2xl font-black text-[#3da4e0] mt-1">
                    {{ $activeOrdersCount }}
                </h3>
                <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Dalam proses operasional</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#3da4e0]/10 dark:bg-[#3da4e0]/20 text-[#3da4e0] flex items-center justify-center text-xl font-bold">
                🧺
            </div>
        </div>

        <!-- Menunggu Konfirmasi -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between transition">
            <div>
                <span class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Butuh Konfirmasi</span>
                <h3 class="text-2xl font-black text-amber-500 mt-1">
                    {{ $pendingConfirmationCount }}
                </h3>
                <span class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-0.5">Perlu ditugaskan driver</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-bold">
                ⏳
            </div>
        </div>

        <!-- Driver Standby -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between transition">
            <div>
                <span class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Driver Standby</span>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">
                    {{ $availableDriversCount }}
                </h3>
                <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5">Siap ambil & antar</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-xl font-bold">
                🛵
            </div>
        </div>

    </div>

    <!-- Status Overview Pills (10 Stages High Contrast Cards) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-list-check text-[#3da4e0]"></i>
                <span>Ringkasan Status 10 Tahap Pesanan</span>
            </h3>
            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Update Realtime</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            @foreach(\App\Models\Order::STATUSES as $statusCode => $statusTitle)
                @php $c = $statusCounts[$statusCode] ?? 0; @endphp
                <a href="{{ route('admin.orders', ['status' => $statusCode]) }}" 
                   class="p-3 rounded-xl border transition-all duration-200 flex items-center justify-between group
                          bg-slate-100 hover:bg-slate-200/80 border-slate-200 text-slate-800
                          dark:bg-slate-800 dark:hover:bg-slate-700/80 dark:border-slate-700 dark:text-slate-100 shadow-2xs hover:shadow-sm">
                    <div class="min-w-0 pr-1 flex-1">
                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-200 block truncate group-hover:text-[#3da4e0] dark:group-hover:text-[#3da4e0] transition">{{ $statusTitle }}</span>
                        <span class="text-base font-black {{ $c > 0 ? 'text-[#3da4e0] dark:text-[#3da4e0]' : 'text-slate-400 dark:text-slate-400' }}">{{ $c }}</span>
                    </div>
                    <div class="w-2.5 h-2.5 rounded-full {{ $c > 0 ? 'bg-[#3da4e0] shadow-xs shadow-[#3da4e0]/50 animate-pulse' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Pesanan Terbaru Masuk</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Daftar transaksi orderan laundry teranyar</p>
            </div>
            <a href="{{ route('admin.orders') }}" class="text-xs font-bold text-[#3da4e0] hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-5 py-3.5">Kode Order</th>
                        <th class="px-5 py-3.5">Pelanggan</th>
                        <th class="px-5 py-3.5">Layanan</th>
                        <th class="px-5 py-3.5">Jarak (KM)</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $order->order_code }}
                                <span class="block text-[10px] text-slate-400 dark:text-slate-500 font-normal">{{ $order->created_at->format('d M, H:i') }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $order->customer_name }}</span>
                                <span class="block text-[11px] text-slate-500 dark:text-slate-400">{{ $order->customer_phone }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300">
                                {{ $order->items->first()?->layanan?->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-[11px]">
                                    {{ $order->distance_km }} KM
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold
                                    {{ $order->status === 'SELESAI' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : '' }}
                                    {{ $order->status === 'MENUNGGU_KONFIRMASI' ? 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-extrabold' : '' }}
                                    {{ $order->status === 'DIBAYAR' ? 'bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300' : '' }}
                                    {{ !in_array($order->status, ['SELESAI', 'MENUNGGU_KONFIRMASI', 'DIBAYAR']) ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300' : '' }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.orders.show', $order->id_order) }}" 
                                   class="px-3 py-1.5 rounded-lg bg-[#3da4e0] text-white font-bold text-[11px] hover:bg-[#1b85c8] transition shadow-sm">
                                    Kelola
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400 dark:text-slate-500">
                                Belum ada pesanan yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
