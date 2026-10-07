@extends('layouts.admin')

@section('content')
<div class="space-y-5">
    
    <!-- Header & Search Filter -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Manajemen Orderan</h2>
                <p class="text-xs text-slate-500">Kelola dan pantau seluruh orderan laundry pelanggan</p>
            </div>
            
            <form action="{{ route('admin.orders') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#3da4e0] bg-white">
                    <option value="">-- Semua Status --</option>
                    @foreach(\App\Models\Order::STATUSES as $code => $title)
                        <option value="{{ $code }}" {{ request('status') === $code ? 'selected' : '' }}>
                            {{ $title }}
                        </option>
                    @endforeach
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari kode/nama/hp..."
                           class="px-3 py-2 pl-8 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <button type="submit" class="px-4 py-2 rounded-xl bg-[#3da4e0] text-white font-bold text-xs hover:bg-[#1b85c8] transition">
                    Filter
                </button>

                @if(request()->hasAny(['status', 'search']))
                    <a href="{{ route('admin.orders') }}" class="px-3 py-2 rounded-xl bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-5 py-3.5">Kode Order</th>
                        <th class="px-5 py-3.5">Pelanggan</th>
                        <th class="px-5 py-3.5">Layanan</th>
                        <th class="px-5 py-3.5">Jarak (KM)</th>
                        <th class="px-5 py-3.5">Jadwal Pickup</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $order->order_code }}
                                <span class="block text-[10px] text-slate-400 font-normal">{{ $order->created_at->format('d M, H:i') }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-800">{{ $order->customer_name }}</span>
                                <span class="block text-[11px] text-slate-500">{{ $order->customer_phone }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-700">
                                {{ $order->items->first()?->layanan?->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[11px]">
                                    {{ $order->distance_km }} KM
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $order->pickup_schedule->format('d M Y, H:i') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold
                                    {{ $order->status === 'SELESAI' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                    {{ $order->status === 'MENUNGGU_KONFIRMASI' ? 'bg-rose-50 text-rose-700 border border-rose-200 font-extrabold' : '' }}
                                    {{ $order->status === 'DIBAYAR' ? 'bg-sky-50 text-sky-700 border border-sky-200' : '' }}
                                    {{ !in_array($order->status, ['SELESAI', 'MENUNGGU_KONFIRMASI', 'DIBAYAR']) ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.orders.show', $order->id_order) }}" 
                                   class="px-3 py-1.5 rounded-lg bg-[#3da4e0] text-white font-bold text-[11px] hover:bg-[#1b85c8] transition shadow-sm">
                                    Kelola Order
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                Tidak ada data orderan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection
