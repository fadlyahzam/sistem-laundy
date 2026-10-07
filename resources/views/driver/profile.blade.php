@extends('layouts.mobile_driver')

@section('content')
<div class="space-y-4">
    <!-- Driver Info Card -->
    <div class="bg-slate-900 rounded-3xl p-6 text-white text-center space-y-3 shadow-xl">
        <div class="w-20 h-20 mx-auto rounded-full bg-[#3da4e0] text-white flex items-center justify-center text-3xl font-black shadow-lg shadow-[#3da4e0]/30">
            🛵
        </div>
        <div>
            <h2 class="text-lg font-extrabold">{{ $user->name }}</h2>
            <p class="text-xs text-slate-400">{{ $user->email }} • {{ $user->phone }}</p>
        </div>
        
        <div class="pt-3 border-t border-slate-800 grid grid-cols-2 gap-3 text-left">
            <div class="p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/60">
                <span class="text-[10px] text-slate-400 block uppercase">Kendaraan</span>
                <span class="text-xs font-bold text-white">{{ $driver->vehicle_type }}</span>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/60">
                <span class="text-[10px] text-slate-400 block uppercase">No. Polisi</span>
                <span class="text-xs font-bold text-[#3da4e0] font-mono">{{ $driver->plate_number }}</span>
            </div>
        </div>
    </div>

    <!-- Status Info -->
    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-800">Total Tugas Selesai</span>
            <p class="text-[11px] text-slate-400">Total pengantaran & penjemputan</p>
        </div>
        <span class="text-xl font-black text-[#3da4e0]">{{ $totalCompleted }}</span>
    </div>

    <!-- Logout -->
    <div class="pt-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl border border-rose-200 text-rose-600 font-bold text-xs hover:bg-rose-50 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar dari Akun Driver
            </button>
        </form>
    </div>
</div>
@endsection
