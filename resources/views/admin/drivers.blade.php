@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-base font-extrabold text-slate-900">Manajemen Driver & Armada</h2>
            <p class="text-xs text-slate-500">Kelola kurir antar jemput pakaian pelanggan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Add Driver Form -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">+ Tambah Driver Baru</h3>
            <form action="{{ route('admin.drivers.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Driver</label>
                    <input type="text" name="name" required placeholder="Contoh: Rian Pratama"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Akun</label>
                    <input type="email" name="email" required placeholder="rian@laundryku.com"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                    <input type="tel" name="phone" required placeholder="08123456789"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Kendaraan</label>
                    <input type="text" name="vehicle_type" required placeholder="Contoh: Motor Honda Vario"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Plat Polisi</label>
                    <input type="text" name="plate_number" required placeholder="Contoh: B 5432 XYZ"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Default</label>
                    <input type="password" name="password" required value="password"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-[#3da4e0] text-white font-bold text-xs hover:bg-[#1b85c8] transition shadow-md shadow-[#3da4e0]/20">
                    Daftarkan Driver
                </button>
            </form>
        </div>

        <!-- Drivers List -->
        <div class="lg:col-span-2 space-y-3">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Daftar Seluruh Driver Outlet</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase font-semibold">
                            <tr>
                                <th class="px-5 py-3">Nama Driver</th>
                                <th class="px-5 py-3">Kendaraan & Plat</th>
                                <th class="px-5 py-3">Ketersediaan</th>
                                <th class="px-5 py-3">Tugas Berjalan</th>
                                <th class="px-5 py-3 text-right">Kontak</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($drivers as $drv)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-5 py-3.5">
                                        <span class="font-bold text-slate-900 block">{{ $drv->user->name }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $drv->user->email }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="font-semibold text-slate-800">{{ $drv->vehicle_type }}</span>
                                        <span class="block text-[11px] font-mono text-[#3da4e0]">{{ $drv->plate_number }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold
                                            {{ $drv->availability === 'available' ? 'bg-emerald-50 text-emerald-700' : ($drv->availability === 'on_duty' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-500') }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $drv->availability === 'available' ? 'bg-emerald-500' : ($drv->availability === 'on_duty' ? 'bg-amber-500' : 'bg-slate-400') }}"></span>
                                            {{ ucfirst(str_replace('_', ' ', $drv->availability)) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="font-bold text-slate-900">{{ $drv->assignments->count() }} Tugas</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        @if($drv->user->phone)
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $drv->user->phone)) }}" 
                                               target="_blank"
                                               class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-[11px] transition">
                                                WhatsApp
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                        Belum ada driver yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
