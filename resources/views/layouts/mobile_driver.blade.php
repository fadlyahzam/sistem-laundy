<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Driver LaundryKu - Antar Jemput' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans">
    <div class="min-h-screen flex justify-center">
        <!-- Mobile Frame Wrapper (max-w-md mx-auto) -->
        <div class="w-full max-w-md bg-white min-h-screen relative flex flex-col shadow-2xl border-x border-slate-200/80 pb-20">
            
            <!-- Driver Top Header -->
            <header class="sticky top-0 z-30 bg-slate-900 text-white px-4 py-3.5 flex items-center justify-between shadow-md">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#3da4e0] flex items-center justify-center text-white shadow">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-[#3da4e0] uppercase tracking-wider">Driver Mode</span>
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        </div>
                        <h1 class="text-sm font-bold text-white truncate max-w-[170px]">{{ auth()->user()->name ?? 'Driver' }}</h1>
                    </div>
                </div>

                <!-- Driver Availability Badge/Dropdown -->
                @auth
                    @php
                        $driver = auth()->user()->driver;
                        $avail = $driver->availability ?? 'available';
                    @endphp
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button" class="flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full border transition
                            {{ $avail === 'available' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : ($avail === 'on_duty' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-slate-700 text-slate-300 border-slate-600') }}">
                            <span class="w-2 h-2 rounded-full {{ $avail === 'available' ? 'bg-emerald-400' : ($avail === 'on_duty' ? 'bg-amber-400' : 'bg-slate-400') }}"></span>
                            <span class="capitalize">{{ $avail === 'on_duty' ? 'On Duty' : $avail }}</span>
                            <svg class="w-3.5 h-3.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-36 bg-slate-800 rounded-xl shadow-xl border border-slate-700 py-1.5 z-50 text-xs">
                            <form action="{{ route('driver.availability') }}" method="POST">
                                @csrf
                                <button type="submit" name="availability" value="available" class="w-full text-left px-3 py-1.5 hover:bg-slate-700 flex items-center gap-2 text-emerald-300">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Available
                                </button>
                                <button type="submit" name="availability" value="on_duty" class="w-full text-left px-3 py-1.5 hover:bg-slate-700 flex items-center gap-2 text-amber-300">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span> On Duty
                                </button>
                                <button type="submit" name="availability" value="offline" class="w-full text-left px-3 py-1.5 hover:bg-slate-700 flex items-center gap-2 text-slate-400">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span> Offline
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
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

            <!-- Bottom Navigation Bar (Driver: Beranda, Pesanan, Profile) -->
            <nav class="fixed bottom-0 left-0 right-0 z-40 bg-slate-900 border-t border-slate-800 shadow-[0_-4px_20px_rgba(0,0,0,0.2)]">
                <div class="max-w-md mx-auto grid grid-cols-3 h-16 items-center px-3">
                    
                    <!-- 1. Beranda -->
                    <a href="{{ route('driver.dashboard') }}" 
                       class="flex flex-col items-center justify-center gap-1 py-1.5 transition {{ request()->routeIs('driver.dashboard') ? 'text-[#3da4e0] font-bold' : 'text-slate-400 hover:text-white font-medium' }}">
                        <div class="relative {{ request()->routeIs('driver.dashboard') ? 'scale-110' : '' }} transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('driver.dashboard') ? '2.3' : '1.8' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <span class="text-[11px] leading-tight">Beranda</span>
                    </a>

                    <!-- 2. Pesanan -->
                    <a href="{{ route('driver.orders') }}" 
                       class="flex flex-col items-center justify-center gap-1 py-1.5 transition {{ request()->routeIs('driver.orders*') ? 'text-[#3da4e0] font-bold' : 'text-slate-400 hover:text-white font-medium' }}">
                        <div class="relative {{ request()->routeIs('driver.orders*') ? 'scale-110' : '' }} transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('driver.orders*') ? '2.3' : '1.8' }}" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <span class="text-[11px] leading-tight">Pesanan</span>
                    </a>

                    <!-- 3. Profile -->
                    <a href="{{ route('driver.profile') }}" 
                       class="flex flex-col items-center justify-center gap-1 py-1.5 transition {{ request()->routeIs('driver.profile*') ? 'text-[#3da4e0] font-bold' : 'text-slate-400 hover:text-white font-medium' }}">
                        <div class="relative {{ request()->routeIs('driver.profile*') ? 'scale-110' : '' }} transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('driver.profile*') ? '2.3' : '1.8' }}" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="text-[11px] leading-tight">Profile</span>
                    </a>

                </div>
            </nav>

        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @stack('scripts')
</body>
</html>
