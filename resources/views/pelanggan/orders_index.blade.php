@extends('layouts.mobile_pelanggan')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-extrabold text-slate-900">Riwayat Pesanan</h2>
            <p class="text-xs text-slate-500">Semua transaksi & pesanan laundry Anda</p>
        </div>
        <a href="{{ route('pelanggan.orders.create') }}" class="px-3 py-1.5 rounded-xl bg-[#3da4e0] text-white text-xs font-bold shadow-sm shadow-[#3da4e0]/30 hover:bg-[#1b85c8] transition">
            + Buat Pesanan
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white rounded-2xl p-8 border border-slate-100 text-center space-y-3">
            <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                🧺
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">Belum Ada Pesanan</h4>
                <p class="text-xs text-slate-400 mt-1">Anda belum memiliki riwayat pesanan laundry.</p>
            </div>
            <a href="{{ route('pelanggan.orders.create') }}" class="inline-block px-4 py-2 rounded-xl bg-[#3da4e0] text-white text-xs font-bold shadow-md shadow-[#3da4e0]/20">
                Pesan Laundry Sekarang
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($orders as $order)
                <a href="{{ route('pelanggan.orders.show', $order->id_order) }}" class="block bg-white rounded-2xl p-4 border border-slate-100 shadow-sm hover:border-[#3da4e0]/40 transition group">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $order->order_code }}</span>
                            <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#3da4e0] transition mt-0.5">
                                {{ $order->items->first()?->layanan?->name ?? 'Layanan Laundry' }}
                            </h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $order->created_at->format('d M Y, H:i') }} • {{ $order->distance_km }} KM
                            </p>
                        </div>
                        
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold
                            {{ $order->status === 'SELESAI' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                            {{ $order->status === 'MENUNGGU_PEMBAYARAN' ? 'bg-amber-50 text-amber-700 border border-amber-200 animate-pulse' : '' }}
                            {{ $order->status === 'DIBAYAR' ? 'bg-sky-50 text-sky-700 border border-sky-200' : '' }}
                            {{ !in_array($order->status, ['SELESAI', 'MENUNGGU_PEMBAYARAN', 'DIBAYAR']) ? 'bg-slate-100 text-slate-700' : '' }}">
                            {{ $order->status_label }}
                        </span>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Total Tagihan:</span>
                        <span class="font-extrabold text-slate-900">
                            @if($order->invoice)
                                Rp {{ number_format($order->invoice->total_amount, 0, ',', '.') }}
                            @else
                                <span class="text-slate-400 italic">Menunggu Timbang</span>
                            @endif
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pt-2">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
