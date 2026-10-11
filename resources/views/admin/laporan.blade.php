@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Buttons -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-extrabold text-slate-900">Laporan Transaksi &amp; Keuangan</h2>
            <p class="text-xs text-slate-500">Ringkasan operasional dan keuangan outlet antar jemput</p>
        </div>
        
        <!-- Dual Export Buttons (Excel & PDF) -->
        <div class="flex items-center gap-2.5">
            <!-- Export Excel -->
            <a href="{{ route('admin.laporan.export_excel', request()->query()) }}" 
               class="px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export Excel</span>
            </a>

            <!-- Export PDF -->
            <a href="{{ route('admin.laporan.export_pdf', request()->query()) }}" 
               target="_blank"
               class="px-3.5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Export PDF</span>
            </a>
        </div>
    </div>

    <!-- Date Range / Period Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('admin.laporan') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-3">
            <div class="w-full sm:w-auto">
                <label for="start_date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                    Tanggal Mulai
                </label>
                <input type="date" 
                       id="start_date" 
                       name="start_date" 
                       value="{{ request('start_date') }}"
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
            </div>

            <div class="w-full sm:w-auto">
                <label for="end_date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                    Tanggal Selesai
                </label>
                <input type="date" 
                       id="end_date" 
                       name="end_date" 
                       value="{{ request('end_date') }}"
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" 
                        class="px-4 py-2 rounded-xl bg-[#3da4e0] hover:bg-[#328bc0] text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5 flex-1 sm:flex-none">
                    <i class="fa-solid fa-filter"></i>
                    <span>Terapkan Filter</span>
                </button>
                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('admin.laporan') }}" 
                       class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition" 
                       title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kas Masuk (Lunas)</span>
            <h3 class="text-2xl font-black text-emerald-600 mt-1">
                Rp {{ number_format($totalPaidRevenue, 0, ',', '.') }}
            </h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Volume Transaksi</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">
                {{ $totalOrdersCount }}
            </h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pesanan Selesai Diantar</span>
            <h3 class="text-2xl font-black text-[#3da4e0] mt-1">
                {{ $completedOrdersCount }}
            </h3>
        </div>
    </div>

    <!-- Transactions Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Rincian Transaksi Pesanan Laundry</h3>
            <span class="text-xs font-bold text-slate-400">Total: {{ $orders->total() }} Data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-5 py-3.5">Kode Order</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Pelanggan</th>
                        <th class="px-5 py-3.5">Layanan</th>
                        <th class="px-5 py-3.5">Berat / Qty</th>
                        <th class="px-5 py-3.5">Total Biaya</th>
                        <th class="px-5 py-3.5">Status Pembayaran</th>
                        <th class="px-5 py-3.5">Driver</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        @php
                            $layananNames = $order->items->map(fn($item) => $item->layanan?->name ?? 'Layanan')->unique()->filter()->implode(', ');
                            if (empty($layananNames)) $layananNames = 'Reguler Kiloan';
                            $weight = $order->berat_total ? $order->berat_total . ' Kg' : ($order->items->sum('quantity') > 0 ? $order->items->sum('quantity') . ' Pcs/Kg' : '-');
                            $amount = $order->invoice?->total_amount ?? $order->items->sum('subtotal');
                            $isPaid = $order->invoice && $order->invoice->status === 'paid';
                            $driverName = $order->pickupAssignment?->driver?->user?->name ?? $order->deliveryAssignment?->driver?->user?->name ?? 'Belum Ditugaskan';
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 font-bold font-mono text-slate-900">
                                <a href="{{ route('admin.orders.show', $order->id_order) }}" class="text-[#3da4e0] hover:underline">
                                    {{ $order->order_code }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">
                                {{ $order->customer_name ?? $order->user?->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $layananNames }}
                            </td>
                            <td class="px-5 py-3.5 font-medium text-slate-700">
                                {{ $weight }}
                            </td>
                            <td class="px-5 py-3.5 font-extrabold text-slate-900">
                                Rp {{ number_format($amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $isPaid ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $isPaid ? 'LUNAS' : 'BELUM LUNAS' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-motorcycle text-slate-400 text-xs"></i>
                                    <span>{{ $driverName }}</span>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                Belum ada data transaksi tercatat pada periode ini.
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
