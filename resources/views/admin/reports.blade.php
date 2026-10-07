@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-base font-extrabold text-slate-900">Laporan Keuangan & Transaksi</h2>
            <p class="text-xs text-slate-500">Ringkasan penerimaan kas laundry dari pembayaran QRIS</p>
        </div>
        <button onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition flex items-center gap-1.5">
            <span>Cetak Laporan</span>
        </button>
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
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Volume Orderan</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">
                {{ $totalOrdersCount }}
            </h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Orderan Selesai</span>
            <h3 class="text-2xl font-black text-[#3da4e0] mt-1">
                {{ $completedOrdersCount }}
            </h3>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Riwayat Seluruh Tagihan (Invoices)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-5 py-3">No. Invoice</th>
                        <th class="px-5 py-3">Kode Order</th>
                        <th class="px-5 py-3">Pelanggan</th>
                        <th class="px-5 py-3">Nominal</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Tanggal Lunas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentInvoices as $inv)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 font-bold font-mono text-slate-900">{{ $inv->invoice_number }}</td>
                            <td class="px-5 py-3.5 font-semibold text-slate-700">{{ $inv->order?->order_code ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-slate-800">{{ $inv->order?->customer_name ?? '-' }}</td>
                            <td class="px-5 py-3.5 font-extrabold text-slate-900">Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $inv->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ strtoupper($inv->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">
                                {{ $inv->paid_at ? $inv->paid_at->format('d M Y, H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                Belum ada data invoice tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $recentInvoices->links() }}
        </div>
    </div>

</div>
@endsection
