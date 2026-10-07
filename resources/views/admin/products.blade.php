@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-base font-extrabold text-slate-900">Kelola Produk & Layanan</h2>
            <p class="text-xs text-slate-500">Atur paket laundry kiloan, satuan, dan tarif kategori tambahan</p>
        </div>
    </div>

    <!-- Forms & Catalog Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Tambah Layanan Baru -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">+ Tambah Layanan Baru</h3>
            <form action="{{ route('admin.layanan.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Layanan</label>
                    <input type="text" name="name" required placeholder="Contoh: Cuci Komplit Kilat"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Layanan</label>
                    <select name="service_type" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0] bg-white">
                        <option value="kiloan">Kiloan</option>
                        <option value="satuan">Satuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tarif Dasar (Rp)</label>
                    <input type="number" name="price_per_kg" required min="0" placeholder="Contoh: 10000"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-[#3da4e0] text-white font-bold text-xs hover:bg-[#1b85c8] transition shadow-md shadow-[#3da4e0]/20">
                    Simpan Layanan
                </button>
            </form>

            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">+ Tambah Kategori / Item Khusus</h3>
                <form action="{{ route('admin.kategori.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Layanan Induk</label>
                        <select name="id_layanan" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0] bg-white">
                            @foreach($layananList as $l)
                                <option value="{{ $l->id_layanan }}">{{ $l->name }} ({{ $l->service_type }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kategori / Item</label>
                        <input type="text" name="name" required placeholder="Contoh: Selimut Tebal / Bedcover"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tarif Tambahan (Rp)</label>
                        <input type="number" name="unit_tariff" required min="0" placeholder="Contoh: 15000"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                        Simpan Kategori Khusus
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Layanan & Kategori Eksisting -->
        <div class="lg:col-span-2 space-y-4">
            @foreach($layananList as $layanan)
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#3da4e0]/10 text-[#3da4e0] flex items-center justify-center font-bold text-lg">
                                {{ $layanan->service_type === 'kiloan' ? '🧺' : '👔' }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">{{ $layanan->name }}</h4>
                                <span class="text-xs font-semibold text-[#3da4e0]">
                                    Rp {{ number_format($layanan->price_per_kg, 0, ',', '.') }}/{{ $layanan->service_type === 'kiloan' ? 'kg' : 'pcs' }}
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('admin.layanan.delete', $layanan->id_layanan) }}" method="POST" onsubmit="return confirm('Hapus layanan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-semibold">
                                Hapus
                            </button>
                        </form>
                    </div>

                    <!-- Kategori List -->
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kategori / Item Khusus:</span>
                        @if($layanan->kategori->isEmpty())
                            <p class="text-xs text-slate-400 italic">Belum ada kategori tambahan untuk layanan ini.</p>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($layanan->kategori as $kat)
                                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                        <div>
                                            <span class="font-bold text-slate-800">{{ $kat->name }}</span>
                                            <span class="block text-[10px] text-[#3da4e0]">+ Rp {{ number_format($kat->unit_tariff, 0, ',', '.') }}</span>
                                        </div>
                                        <form action="{{ route('admin.kategori.delete', $kat->id_kategori) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-400 hover:text-rose-600 text-xs font-bold">&times;</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </div>

</div>
@endsection
