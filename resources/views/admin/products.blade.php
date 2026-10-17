@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{
    isEditLayananOpen: false,
    editLayananData: {
        id: null,
        name: '',
        service_type: 'kiloan',
        price_per_kg: 0,
        is_active: true,
        action_url: ''
    },
    openEditLayanan(data) {
        this.editLayananData = { ...data };
        this.isEditLayananOpen = true;
    }
}">

    <!-- Header -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-extrabold text-slate-900">Kelola Produk & Layanan</h2>
            <p class="text-xs text-slate-500">Atur paket laundry kiloan dan satuan outlet</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">
                Total: {{ $layananList->count() }} Layanan
            </span>
        </div>
    </div>

    <!-- Forms & Catalog Grid (Format 2 Kolom Klasik) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Tambah Layanan Baru (Kolom Kiri) -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-4 h-fit">
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
        </div>

        <!-- Daftar Layanan Eksisting (Kolom Kanan) -->
        <div class="lg:col-span-2 space-y-3">
            @forelse($layananList as $layanan)
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between hover:border-slate-300 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#3da4e0]/10 text-[#3da4e0] flex items-center justify-center font-bold text-lg shrink-0">
                            {{ $layanan->service_type === 'kiloan' ? '🧺' : '👔' }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-slate-900">{{ $layanan->name }}</h4>
                                @if(!$layanan->is_active)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-500">Nonaktif</span>
                                @endif
                            </div>
                            <span class="text-xs font-semibold text-[#3da4e0]">
                                Rp {{ number_format($layanan->price_per_kg, 0, ',', '.') }}/{{ $layanan->service_type === 'kiloan' ? 'kg' : 'pcs' }}
                            </span>
                        </div>
                    </div>

                    <!-- Menu Aksi Layanan: Edit dan Hapus -->
                    <div class="flex items-center gap-2">
                        <!-- Tombol Edit Layanan -->
                        <button type="button" 
                                @click="openEditLayanan({{ json_encode([
                                    'id' => $layanan->id_layanan,
                                    'name' => $layanan->name,
                                    'service_type' => $layanan->service_type,
                                    'price_per_kg' => (float) $layanan->price_per_kg,
                                    'is_active' => (bool) $layanan->is_active,
                                    'action_url' => route('admin.layanan.update', $layanan->id_layanan),
                                ]) }})"
                                class="px-2.5 py-1.5 rounded-lg bg-sky-50 text-[#1b85c8] hover:bg-sky-100 font-bold text-xs transition flex items-center gap-1 border border-sky-100 shadow-xs"
                                title="Edit Layanan">
                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                            <span>Edit</span>
                        </button>

                        <!-- Tombol Hapus Layanan -->
                        <form action="{{ route('admin.layanan.delete', $layanan->id_layanan) }}" 
                              method="POST" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan \'{{ addslashes($layanan->name) }}\'?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold text-xs transition flex items-center gap-1 border border-rose-100 shadow-xs"
                                    title="Hapus Layanan">
                                <i class="fa-solid fa-trash-can text-[11px]"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 border border-slate-200/80 shadow-sm text-center text-slate-400">
                    Belum ada layanan yang ditambahkan. Gunakan formulir di sebelah kiri untuk menambahkan layanan baru.
                </div>
            @endforelse
        </div>

    </div>

    <!-- Modal Edit Layanan -->
    <div x-show="isEditLayananOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         x-cloak>
        
        <div @click.away="isEditLayananOpen = false"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 transform transition-all">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#3da4e0] flex items-center justify-center">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-900">Edit Layanan</h3>
                </div>
                <button type="button" @click="isEditLayananOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form :action="editLayananData.action_url" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Layanan</label>
                    <input type="text" name="name" x-model="editLayananData.name" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Layanan</label>
                    <select name="service_type" x-model="editLayananData.service_type" required 
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0] bg-white">
                        <option value="kiloan">Kiloan</option>
                        <option value="satuan">Satuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tarif Dasar (Rp)</label>
                    <input type="number" name="price_per_kg" x-model="editLayananData.price_per_kg" required min="0"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="edit_layanan_active" value="1" 
                           :checked="editLayananData.is_active" 
                           class="rounded text-[#3da4e0] focus:ring-[#3da4e0]">
                    <label for="edit_layanan_active" class="text-xs font-semibold text-slate-700">Layanan Aktif (Dapat dipilih pelanggan)</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="isEditLayananOpen = false" 
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white font-bold text-xs transition shadow-md shadow-[#3da4e0]/20">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
