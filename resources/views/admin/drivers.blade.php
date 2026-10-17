@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{
    isEditOpen: false,
    editData: {
        id: null,
        name: '',
        email: '',
        phone: '',
        vehicle_type: '',
        plate_number: '',
        availability: 'available',
        action_url: ''
    },
    openEdit(data) {
        this.editData = { ...data };
        this.isEditOpen = true;
    }
}">

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
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Daftar Seluruh Driver Outlet</h3>
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">Total: {{ $drivers->count() }} Driver</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase font-semibold">
                            <tr>
                                <th class="px-5 py-3">Nama Driver</th>
                                <th class="px-5 py-3">Kendaraan & Plat</th>
                                <th class="px-5 py-3">Ketersediaan</th>
                                <th class="px-5 py-3">Tugas Berjalan</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
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
                                        <span class="block text-[11px] font-mono text-[#3da4e0] font-bold">{{ $drv->plate_number }}</span>
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
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($drv->user->phone)
                                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $drv->user->phone)) }}" 
                                                   target="_blank"
                                                   class="p-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-[11px] transition"
                                                   title="Hubungi WhatsApp">
                                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                                </a>
                                            @endif

                                            <!-- Tombol Edit Driver -->
                                            <button type="button" 
                                                    @click="openEdit({{ json_encode([
                                                        'id' => $drv->id_driver,
                                                        'name' => $drv->user->name,
                                                        'email' => $drv->user->email,
                                                        'phone' => $drv->user->phone,
                                                        'vehicle_type' => $drv->vehicle_type,
                                                        'plate_number' => $drv->plate_number,
                                                        'availability' => $drv->availability,
                                                        'action_url' => route('admin.drivers.update', $drv->id_driver),
                                                    ]) }})"
                                                    class="px-2.5 py-1.5 rounded-lg bg-sky-50 text-[#1b85c8] hover:bg-sky-100 font-bold text-[11px] transition flex items-center gap-1 border border-sky-100 shadow-xs"
                                                    title="Edit Driver">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                <span>Edit</span>
                                            </button>

                                            <!-- Tombol Hapus Driver -->
                                            <form action="{{ route('admin.drivers.delete', $drv->id_driver) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus driver \'{{ addslashes($drv->user->name) }}\'?')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold text-[11px] transition flex items-center gap-1 border border-rose-100 shadow-xs"
                                                        title="Hapus Driver">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        </div>
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

    <!-- Modal Edit Driver (Alpine.js) -->
    <div x-show="isEditOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         x-cloak>
        
        <div @click.away="isEditOpen = false"
             class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 transform transition-all">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#3da4e0] flex items-center justify-center">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-900">Edit Data Driver</h3>
                </div>
                <button type="button" @click="isEditOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form :action="editData.action_url" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Driver</label>
                    <input type="text" name="name" x-model="editData.name" required
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" x-model="editData.email" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">WhatsApp</label>
                        <input type="tel" name="phone" x-model="editData.phone" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Kendaraan</label>
                        <input type="text" name="vehicle_type" x-model="editData.vehicle_type" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Plat Nomor</label>
                        <input type="text" name="plate_number" x-model="editData.plate_number" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Ketersediaan</label>
                    <select name="availability" x-model="editData.availability" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0] bg-white">
                        <option value="available">Tersedia (Ready Pickup / Delivery)</option>
                        <option value="on_duty">Sedang Bertugas (On Duty)</option>
                        <option value="off">Nonaktif / Istirahat (Off)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ubah Kata Sandi (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="isEditOpen = false" 
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
