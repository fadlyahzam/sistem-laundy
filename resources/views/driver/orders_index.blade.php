@extends('layouts.mobile_driver')

@section('content')
<div class="space-y-4">
    <div>
        <h2 class="text-base font-extrabold text-slate-900">Riwayat Tugas Driver</h2>
        <p class="text-xs text-slate-500">Daftar penjemputan dan pengantaran laundry</p>
    </div>

    @if($assignments->isEmpty())
        <div class="bg-white rounded-2xl p-8 border border-slate-100 text-center space-y-2">
            <div class="text-3xl">📋</div>
            <h4 class="font-bold text-slate-800 text-sm">Belum Ada Tugas</h4>
            <p class="text-xs text-slate-400">Riwayat tugas driver akan dicatat di sini.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($assignments as $task)
                @php $order = $task->order; @endphp
                <a href="{{ route('driver.orders.show', $task->id_assignment) }}" 
                   class="block bg-white rounded-2xl p-4 border border-slate-100 shadow-sm hover:border-[#3da4e0]/30 transition group">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $task->type === 'pickup' ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800' }}">
                                {{ $task->type === 'pickup' ? 'Penjemputan' : 'Pengantaran' }}
                            </span>
                            <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#3da4e0] transition mt-1">
                                {{ $order->customer_name }}
                            </h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $order->order_code }} • {{ $order->distance_km }} KM
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $task->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ ucfirst($task->status) }}
                        </span>
                    </div>

                    <div class="mt-2.5 pt-2.5 border-t border-slate-100 text-[11px] text-slate-600 line-clamp-1">
                        📍 {{ $order->address_text }}
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pt-2">
            {{ $assignments->links() }}
        </div>
    @endif
</div>
@endsection
