<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Dashboard - LaundryKu' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-cloak 
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden transition-opacity"></div>

        <!-- Left Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shadow-2xl lg:shadow-none">
            
            <!-- Sidebar Header / Brand -->
            <div class="h-16 px-6 flex items-center justify-between border-b border-slate-800 bg-slate-950/40">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#3da4e0] to-[#1b85c8] flex items-center justify-center text-white shadow-lg shadow-[#3da4e0]/20">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold tracking-tight text-white flex items-center gap-1.5">
                            Laundry<span class="text-[#3da4e0]">Ku</span>
                        </div>
                        <span class="text-[10px] font-semibold tracking-wider text-slate-400 uppercase">Outlet Pusat</span>
                    </div>
                </a>

                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-6 space-y-1.5 overflow-y-auto">
                
                <!-- 1. Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Orderan -->
                <a href="{{ route('admin.orders') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.orders*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Orderan</span>
                    </div>
                    @php
                        $pendingOrdersCount = \App\Models\Order::whereIn('status', ['MENUNGGU_KONFIRMASI', 'MENUNGGU_PEMBAYARAN'])->count();
                    @endphp
                    @if($pendingOrdersCount > 0)
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ request()->routeIs('admin.orders*') ? 'bg-white text-[#3da4e0]' : 'bg-[#3da4e0] text-white' }}">
                            {{ $pendingOrdersCount }}
                        </span>
                    @endif
                </a>

                <!-- 3. Laporan -->
                <a href="{{ route('admin.reports') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.reports*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Laporan</span>
                </a>

                <!-- 4. Kelola Produk -->
                <a href="{{ route('admin.products') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.products*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span>Kelola Produk</span>
                </a>

                <!-- 5. Driver -->
                <a href="{{ route('admin.drivers') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('admin.drivers*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Driver</span>
                </a>

            </nav>

            <!-- User profile & Logout -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/30">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white text-sm">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-semibold text-white truncate max-w-[110px]">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <p class="text-[11px] text-slate-400">Admin Laundry</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 leading-tight">{{ $pageTitle ?? 'Dashboard Outlet' }}</h2>
                        <p class="text-xs text-slate-500 hidden sm:block">Kelola operasional laundry antar jemput secara realtime</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#3da4e0]/10 text-[#3da4e0] border border-[#3da4e0]/20">
                        <span class="w-2 h-2 rounded-full bg-[#3da4e0] animate-pulse"></span>
                        Radius Aktif: 20 KM
                    </span>
                </div>
            </header>

            <!-- Alerts -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mx-6 mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-600 hover:text-emerald-800">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mx-6 mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-rose-600 hover:text-rose-800">&times;</button>
                </div>
            @endif

            <!-- Main Page Content -->
            <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
                @yield('content')
            </main>

        </div>

    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @stack('scripts')
</body>
</html>
