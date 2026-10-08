<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LaundryKu - Laundry Antar Jemput Online' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Leaflet CSS for Map picker -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans">
    <div class="min-h-screen flex justify-center">
        <!-- Mobile Frame Wrapper (max-w-md mx-auto) -->
        <div class="w-full max-w-md bg-white min-h-screen relative flex flex-col shadow-2xl border-x border-slate-200/80 pb-20">
            
            <!-- Top Header -->
            <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-100 px-4 py-3.5 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    @hasSection('header_back')
                        @yield('header_back')
                    @else
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#3da4e0] to-[#1b85c8] flex items-center justify-center text-white shadow-md shadow-[#3da4e0]/25">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#3da4e0]">LaundryKu</span>
                            <h1 class="text-base font-bold text-slate-900 leading-tight">{{ $pageTitle ?? 'Layanan Laundry' }}</h1>
                        </div>
                    @endif
                </div>

                <!-- Right Actions / Notification & Profile -->
                <div class="flex items-center gap-2">
                    @auth
                        @php
                            $unreadNotifs = \App\Models\Notification::where('id_user', auth()->id())->where('is_read', false)->count();
                        @endphp
                        <a href="{{ route('pelanggan.notifications') }}" class="relative p-2 rounded-xl text-slate-500 hover:text-[#3da4e0] hover:bg-slate-50 transition">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if($unreadNotifs > 0)
                                <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-white animate-pulse">
                                    {{ $unreadNotifs > 9 ? '9+' : $unreadNotifs }}
                                </span>
                            @endif
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-[#3da4e0] text-white hover:bg-[#1b85c8] transition shadow-sm">
                            Masuk
                        </a>
                    @endauth
                </div>
            </header>

            <!-- Flash Notifications -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mx-4 mt-3 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-2.5 shadow-sm transition">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1 font-medium">{{ session('success') }}</div>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mx-4 mt-3 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-2.5 shadow-sm transition">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1 font-medium">{{ session('error') }}</div>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-700">&times;</button>
                </div>
            @endif

            <!-- Main Content Area -->
            <main class="flex-1 px-4 py-3">
                @yield('content')
            </main>

            <!-- Bottom Navigation Bar (Pelanggan: Beranda, Pesanan, Profil) -->
            <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-white border-t border-slate-200 flex justify-around py-2.5 z-50 shadow-lg">
                <!-- 1. Beranda -->
                <a href="{{ route('pelanggan.dashboard') }}" 
                   class="flex flex-col items-center justify-center gap-1 flex-1 py-0.5 transition {{ request()->routeIs('pelanggan.dashboard') ? 'text-[#3da4e0] font-bold' : 'text-slate-500 hover:text-slate-800 font-medium' }}">
                    <div class="relative {{ request()->routeIs('pelanggan.dashboard') ? 'scale-110' : '' }} transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('pelanggan.dashboard') ? '2.3' : '1.8' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span class="text-[11px] leading-tight">Beranda</span>
                </a>

                <!-- 2. Pesanan -->
                <a href="{{ route('pelanggan.orders') }}" 
                   class="flex flex-col items-center justify-center gap-1 flex-1 py-0.5 transition {{ request()->routeIs('pelanggan.orders*') ? 'text-[#3da4e0] font-bold' : 'text-slate-500 hover:text-slate-800 font-medium' }}">
                    <div class="relative {{ request()->routeIs('pelanggan.orders*') ? 'scale-110' : '' }} transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('pelanggan.orders*') ? '2.3' : '1.8' }}" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <span class="text-[11px] leading-tight">Pesanan</span>
                </a>

                <!-- 3. Profil -->
                <a href="{{ route('pelanggan.profile') }}" 
                   class="flex flex-col items-center justify-center gap-1 flex-1 py-0.5 transition {{ request()->routeIs('pelanggan.profile*') ? 'text-[#3da4e0] font-bold' : 'text-slate-500 hover:text-slate-800 font-medium' }}">
                    <div class="relative {{ request()->routeIs('pelanggan.profile*') ? 'scale-110' : '' }} transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('pelanggan.profile*') ? '2.3' : '1.8' }}" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="text-[11px] leading-tight">Profil</span>
                </a>
            </nav>

        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @stack('scripts')
</body>
</html>
