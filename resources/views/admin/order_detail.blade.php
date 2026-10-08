@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumb & Status Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <a href="{{ route('admin.orders') }}" class="hover:text-[#3da4e0]">Orderan</a>
                <span>/</span>
                <span class="text-slate-700 font-semibold">{{ $order->order_code }}</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">Kelola Pesanan {{ $order->order_code }}</h1>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold
                {{ $order->status === 'SELESAI' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                {{ $order->status === 'MENUNGGU_KONFIRMASI' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                {{ $order->status === 'DIBAYAR' ? 'bg-sky-50 text-sky-700 border border-sky-200' : '' }}
                {{ !in_array($order->status, ['SELESAI', 'MENUNGGU_KONFIRMASI', 'DIBAYAR']) ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}">
                Status Saat Ini: {{ $order->status_label }}
            </span>
        </div>
    </div>

    <!-- 1. Horizontal Status Stepper Tracker -->
    <x-status-stepper :currentStatus="$order->status" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Details & Action Controls -->
        <div class="lg:col-span-2 space-y-6">

            <!-- OPERATIONAL WORKFLOW ACTION BOX -->
            <div class="bg-white rounded-2xl p-6 border-2 border-[#3da4e0]/30 shadow-md space-y-4">
                <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#3da4e0] animate-ping"></span>
                    Panel Kendali Alur Kerja (Workflow Outlet)
                </h3>

                <!-- Workflow Step 1: Assign Pickup Driver (MENUNGGU_KONFIRMASI) -->
                @if($order->status === \App\Models\Order::STATUS_MENUNGGU_KONFIRMASI)
                    <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200/80 space-y-3">
                        <div class="flex items-center gap-2 text-amber-800 text-xs font-bold">
                            <span>1️⃣</span> Tugaskan Driver Penjemputan (Pickup)
                        </div>
                        <p class="text-xs text-amber-700">Pilih driver yang standby untuk menjemput cucian dari alamat pelanggan.</p>
                        <form action="{{ route('admin.orders.assign_driver', $order->id_order) }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                            @csrf
                            <input type="hidden" name="type" value="pickup">
                            <select name="id_driver" required class="flex-1 px-3 py-2 rounded-xl border border-amber-300 text-xs font-semibold bg-white focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                                <option value="">-- Pilih Driver Standby --</option>
                                @foreach($availableDrivers as $d)
                                    <option value="{{ $d->id_driver }}">
                                        {{ $d->user->name }} ({{ $d->vehicle_type }} - {{ $d->plate_number }}) - [{{ ucfirst($d->availability) }}]
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white text-xs font-bold shadow-md shadow-[#3da4e0]/20 transition">
                                Tugaskan Driver Pickup
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Workflow Step 2: Weighing & Issue Invoice (SAMPAI_OUTLET) -->
                @if($order->status === \App\Models\Order::STATUS_SAMPAI_OUTLET || ($order->status === \App\Models\Order::STATUS_MENUNGGU_PEMBAYARAN && !$order->invoice))
                    <div class="p-4 rounded-xl bg-sky-50/70 border border-sky-200/80 space-y-3">
                        <div class="flex items-center gap-2 text-sky-900 text-xs font-bold">
                            <span>2️⃣</span> Penimbangan Cucian & Terbitkan Tagihan (Invoice)
                        </div>
                        <p class="text-xs text-sky-800">Cucian telah tiba di outlet. Masukkan berat riil hasil timbangan untuk menerbitkan tagihan QRIS.</p>
                        <form action="{{ route('admin.orders.issue_invoice', $order->id_order) }}" method="POST" class="flex flex-col sm:flex-row items-center gap-2">
                            @csrf
                            <div class="relative w-full sm:w-48">
                                <input type="number" step="0.1" min="0.1" name="final_quantity" required 
                                       value="{{ $order->items->first()?->quantity ?? 2 }}"
                                       class="w-full px-3 py-2 rounded-xl border border-sky-300 text-xs font-bold bg-white focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                                <span class="absolute right-3 top-2 text-xs text-slate-400 font-semibold">Kg / Pcs</span>
                            </div>
                            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white text-xs font-bold shadow-md shadow-[#3da4e0]/20 transition">
                                Terbitkan Invoice & Minta Pembayaran
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Workflow Step 3: Process Washing (DIBAYAR) -->
                @if($order->status === \App\Models\Order::STATUS_DIBAYAR)
                    <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/80 space-y-3">
                        <div class="flex items-center gap-2 text-emerald-900 text-xs font-bold">
                            <span>3️⃣</span> Pembayaran Lunas! Mulai Pencucian
                        </div>
                        <p class="text-xs text-emerald-800">Pembayaran QRIS telah terverifikasi. Cucian siap dimasukkan ke mesin cuci.</p>
                        <form action="{{ route('admin.orders.update_status', $order->id_order) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="SEDANG_DIPROSES">
                            <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                                🧼 Mulai Proses Pencucian (SEDANG_DIPROSES)
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Workflow Step 4: Finished Packing (SEDANG_DIPROSES) -->
                @if($order->status === \App\Models\Order::STATUS_SEDANG_DIPROSES)
                    <div class="p-4 rounded-xl bg-sky-50/70 border border-sky-200/80 space-y-3">
                        <div class="flex items-center gap-2 text-sky-900 text-xs font-bold">
                            <span>4️⃣</span> Cucian Selesai Dicuci & Disetrika
                        </div>
                        <p class="text-xs text-sky-800">Pakaian telah kering, disetrika rapi, dan dikemas rapi.</p>
                        <form action="{{ route('admin.orders.update_status', $order->id_order) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="SIAP_DIANTAR">
                            <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white text-xs font-bold shadow-md shadow-[#3da4e0]/20 transition">
                                📦 Selesai Packing & Siap Diantar (SIAP_DIANTAR)
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Workflow Step 5: Assign Delivery Driver (SIAP_DIANTAR) -->
                @if($order->status === \App\Models\Order::STATUS_SIAP_DIANTAR)
                    <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200/80 space-y-3">
                        <div class="flex items-center gap-2 text-amber-800 text-xs font-bold">
                            <span>5️⃣</span> Tugaskan Driver Pengantaran (Delivery)
                        </div>
                        <p class="text-xs text-amber-700">Pilih driver untuk mengantar kembali cucian wangi ke alamat pelanggan.</p>
                        <form action="{{ route('admin.orders.assign_driver', $order->id_order) }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                            @csrf
                            <input type="hidden" name="type" value="delivery">
                            <select name="id_driver" required class="flex-1 px-3 py-2 rounded-xl border border-amber-300 text-xs font-semibold bg-white focus:outline-none focus:ring-2 focus:ring-[#3da4e0]">
                                <option value="">-- Pilih Driver Standby --</option>
                                @foreach($availableDrivers as $d)
                                    <option value="{{ $d->id_driver }}">
                                        {{ $d->user->name }} ({{ $d->vehicle_type }} - {{ $d->plate_number }}) - [{{ ucfirst($d->availability) }}]
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-[#3da4e0] hover:bg-[#1b85c8] text-white text-xs font-bold shadow-md shadow-[#3da4e0]/20 transition">
                                Tugaskan Driver Delivery
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Selesaikan Pesanan Manual Trigger (if in delivery) -->
                @if($order->status === \App\Models\Order::STATUS_MENUNGGU_PENGANTARAN)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <p class="text-xs text-slate-600">Pesanan sedang dalam perjalanan oleh driver. Anda juga dapat menyelesaikan secara manual jika pelanggan konfirmasi telah menerima cucian.</p>
                        <form action="{{ route('admin.orders.update_status', $order->id_order) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="SELESAI">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                                ✓ Tandai Pesanan Selesai (SELESAI)
                            </button>
                        </form>
                    </div>
                @endif

                @if($order->status === \App\Models\Order::STATUS_SELESAI)
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                        <span>✓</span> Pesanan ini telah selesai sepenuhnya dan transaksi telah ditutup.
                    </div>
                @endif

            </div>

            <!-- Items & Pricing Table -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900">Rincian Paket & Item Laundry</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-2.5">Layanan</th>
                                <th class="px-4 py-2.5">Kategori Tambahan</th>
                                <th class="px-4 py-2.5">Kuantitas</th>
                                <th class="px-4 py-2.5">Harga Satuan</th>
                                <th class="px-4 py-2.5 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $item->layanan->name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->kategori->name ?? '-' }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ $item->quantity }} {{ $item->layanan->service_type === 'kiloan' ? 'Kg' : 'Pcs' }}</td>
                                    <td class="px-4 py-3 text-slate-600">Rp {{ number_format($item->price_snapshot, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Audit Trail Timeline -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900">Audit Trail (Log Status)</h3>
                <div class="relative pl-6 space-y-4 border-l-2 border-slate-100">
                    @foreach($order->statusLogs as $log)
                        <div class="relative text-xs">
                            <div class="absolute -left-[31px] top-1 w-3 h-3 rounded-full bg-[#3da4e0] ring-4 ring-white"></div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900">{{ \App\Models\Order::STATUSES[$log->to_status] ?? $log->to_status }}</span>
                                <span class="text-[10px] text-slate-400">{{ $log->changed_at->format('d M Y, H:i') }}</span>
                            </div>
                            <p class="text-slate-600 mt-0.5">{{ $log->note }}</p>
                            @if($log->user)
                                <span class="text-[10px] text-slate-400 block mt-0.5">Oleh: {{ $log->user->name }} ({{ ucfirst($log->user->role) }})</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right 1 Col: Customer & Location & Invoice Cards -->
        <div class="space-y-6">

            <!-- Customer Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Pelanggan</h3>
                <div class="space-y-1.5 text-xs">
                    <p class="font-extrabold text-slate-900 text-sm">{{ $order->customer_name }}</p>
                    <p class="text-slate-500 font-mono">{{ $order->customer_phone }}</p>
                    <p class="text-slate-600 font-medium leading-relaxed pt-1">
                        📍 {{ $order->address_text }}
                    </p>
                    <div class="pt-2 flex items-center justify-between">
                        <span class="text-slate-500">Jarak Outlet:</span>
                        <span class="font-bold text-[#3da4e0]">{{ $order->distance_km }} KM</span>
                    </div>
                </div>

                @if($order->customer_phone)
                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $order->customer_phone)) }}" 
                       target="_blank"
                       class="block w-full py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs text-center transition">
                        Hubungi WhatsApp
                    </a>
                @endif
            </div>

            <!-- Invoice & Payment Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tagihan & Midtrans</h3>
                @if($order->invoice)
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">No. Invoice:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $order->invoice->invoice_number }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Total Nominal:</span>
                            <span class="text-base font-black text-slate-900">Rp {{ number_format($order->invoice->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Status Tagihan:</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $order->invoice->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ strtoupper($order->invoice->status) }}
                            </span>
                        </div>
                        @if($order->invoice->paid_at)
                            <div class="text-[11px] text-emerald-600 font-semibold pt-1">
                                ✓ Lunas pada: {{ $order->invoice->paid_at->format('d M Y, H:i') }}
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Invoice belum diterbitkan.</p>
                @endif
            </div>

            <!-- Driver Assignments Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Riwayat Driver Ditugaskan</h3>
                <div class="space-y-2.5 text-xs">
                    @forelse($order->assignments as $asg)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">{{ $asg->driver->user->name ?? 'Driver' }}</span>
                                <span class="text-[10px] uppercase font-bold text-[#3da4e0]">{{ $asg->type }}</span>
                            </div>
                            <p class="text-[11px] text-slate-500">{{ $asg->driver->vehicle_type }} ({{ $asg->driver->plate_number }})</p>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold {{ $asg->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ ucfirst($asg->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs italic">Belum ada penugasan driver.</p>
                    @endforelse
                </div>
            </div>

            <!-- Driver Proof Photos Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                    <span>Bukti Foto Driver</span>
                    <span class="text-[10px] font-semibold text-[#3da4e0]">Pickup & Antar</span>
                </h3>

                @if($order->pickup_photo_url || $order->delivery_photo_url)
                    <div class="space-y-3 text-xs">
                        @if($order->pickup_photo_url)
                            <div class="space-y-1.5 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-700 text-[11px]">Foto Penjemputan</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800">Pickup</span>
                                </div>
                                <a href="{{ $order->pickup_photo_url }}" target="_blank" class="block aspect-video rounded-lg overflow-hidden border border-slate-200 group relative bg-black/5">
                                    <img src="{{ $order->pickup_photo_url }}" alt="Bukti Foto Penjemputan" class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[10px] font-bold">
                                        Perbesar Foto ↗
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if($order->delivery_photo_url)
                            <div class="space-y-1.5 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-700 text-[11px]">Foto Serah Terima</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">Delivery</span>
                                </div>
                                <a href="{{ $order->delivery_photo_url }}" target="_blank" class="block aspect-video rounded-lg overflow-hidden border border-slate-200 group relative bg-black/5">
                                    <img src="{{ $order->delivery_photo_url }}" alt="Bukti Foto Pengantaran" class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[10px] font-bold">
                                        Perbesar Foto ↗
                                    </div>
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center text-xs text-slate-400 italic">
                        Belum ada foto bukti yang diunggah oleh driver.
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
