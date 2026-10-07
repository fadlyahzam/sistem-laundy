@extends('layouts.mobile_pelanggan')

@section('header_back')
    <a href="{{ route('pelanggan.dashboard') }}" class="p-1.5 -ml-1 text-slate-600 hover:text-[#3da4e0] transition">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div>
        <h1 class="text-sm font-bold text-slate-900 leading-tight">Notifikasi</h1>
    </div>
@endsection

@section('content')
<div class="space-y-3">
    @if($notifications->isEmpty())
        <div class="bg-white rounded-2xl p-8 border border-slate-100 text-center space-y-2">
            <div class="text-3xl">🔔</div>
            <h4 class="font-bold text-slate-800 text-sm">Belum Ada Notifikasi</h4>
            <p class="text-xs text-slate-400">Pembaruan mengenai pesanan Anda akan muncul di sini.</p>
        </div>
    @else
        <div class="space-y-2.5">
            @foreach($notifications as $notif)
                <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-1">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-[#3da4e0]"></span>
                            {{ $notif->title }}
                        </h4>
                        <span class="text-[10px] text-slate-400">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $notif->message }}</p>
                    @if($notif->id_order)
                        <div class="pt-1.5 text-right">
                            <a href="{{ route('pelanggan.orders.show', $notif->id_order) }}" class="text-[11px] font-bold text-[#3da4e0] hover:underline">
                                Buka Pesanan &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
