@extends('layouts.mobile_pelanggan')

@section('content')
<div class="space-y-4">
    
    <!-- User Card -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm text-center space-y-3">
        <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-tr from-[#3da4e0] to-[#1b85c8] text-white flex items-center justify-center text-2xl font-bold shadow-lg shadow-[#3da4e0]/30">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h2 class="text-base font-extrabold text-slate-900">{{ $user->name }}</h2>
            <p class="text-xs text-slate-400">{{ $user->email }} • {{ $user->phone }}</p>
        </div>
        <div class="pt-2 grid grid-cols-2 gap-3 border-t border-slate-100">
            <div class="p-2.5 rounded-xl bg-slate-50">
                <span class="text-lg font-black text-[#3da4e0]">{{ $totalOrders }}</span>
                <span class="block text-[10px] text-slate-400">Total Pesanan</span>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50">
                <span class="text-lg font-black text-emerald-600">{{ $completedOrders }}</span>
                <span class="block text-[10px] text-slate-400">Pesanan Selesai</span>
            </div>
        </div>
    </div>

    <!-- Menu Links -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm divide-y divide-slate-100">
        <a href="{{ route('pelanggan.orders') }}" class="p-4 flex items-center justify-between text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
            <div class="flex items-center gap-3">
                <span class="text-base">📦</span>
                <span>Riwayat Pesanan Saya</span>
            </div>
            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        <a href="{{ route('pelanggan.notifications') }}" class="p-4 flex items-center justify-between text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
            <div class="flex items-center gap-3">
                <span class="text-base">🔔</span>
                <span>Notifikasi</span>
            </div>
            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>

    <!-- Logout Button -->
    <div class="pt-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl border border-rose-200 text-rose-600 font-bold text-xs hover:bg-rose-50 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar dari Akun
            </button>
        </form>
    </div>

</div>
@endsection
