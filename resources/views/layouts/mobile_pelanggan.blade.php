<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    
    <!-- FOUC Prevention Script -->
    <script>
      if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.classList.add('dark');
      } else {
          document.documentElement.classList.remove('dark');
      }
    </script>

    <!-- Tailwind CDN with darkMode: class -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            colors: { primary: { DEFAULT: '#3da4e0', hover: '#328bc0' } }
          }
        }
      }
    </script>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased font-sans">

    <div class="min-h-screen flex">

        <!-- Desktop / Tablet Left Sidebar (Fixed Viewport, Sticky, Pinned Footer) -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col h-screen sticky top-0 shrink-0 hidden md:flex z-40">
            <!-- 1. Brand Header (Fixed Top) -->
            <div class="p-5 border-b border-slate-800 flex items-center gap-3 shrink-0">
                <div class="w-10 h-10 rounded-2xl bg-[#3da4e0]/10 dark:bg-[#3da4e0]/20 border border-[#3da4e0]/30 flex items-center justify-center text-[#3da4e0] text-lg shadow-sm shrink-0">
                    <i class="fa-solid fa-shirt"></i>
                </div>
                <div>
                    <span class="font-extrabold text-base tracking-wide text-white">Laundry<span class="text-[#3da4e0] font-extrabold">Ku</span></span>
                    <span class="block text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Area Pelanggan</span>
                </div>
            </div>

            <!-- 2. Navigation Menu (Scrollable Area) -->
            <nav class="flex-1 overflow-y-auto p-3 space-y-1 custom-scrollbar">
                <a href="{{ route('pelanggan.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('pelanggan.dashboard') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                    <i class="fa-solid fa-house w-5"></i> Beranda
                </a>
                <a href="{{ route('pelanggan.orders.create') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('pelanggan.orders.create*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                    <i class="fa-solid fa-plus-circle w-5"></i> Buat Pesanan
                </a>
                <a href="{{ route('pelanggan.orders') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('pelanggan.orders*') && !request()->routeIs('pelanggan.orders.create*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                    <i class="fa-solid fa-box-archive w-5"></i> Pesanan Saya
                </a>
                <a href="{{ route('pelanggan.notifications') }}" 
                   class="flex items-center justify-between px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('pelanggan.notifications*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-bell w-5"></i> Notifikasi
                    </div>
                    @auth
                        @php
                            $unreadNotifs = \App\Models\Notification::where('id_user', auth()->id())->where('is_read', false)->count();
                        @endphp
                        @if($unreadNotifs > 0)
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-500 text-white">{{ $unreadNotifs }}</span>
                        @endif
                    @endauth
                </a>
                <a href="{{ route('pelanggan.profile') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('pelanggan.profile*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                    <i class="fa-solid fa-user w-5"></i> Profil Saya
                </a>
            </nav>

            <!-- 3. Profile & Logout Footer (Pinned Permanently at Bottom) -->
            <div class="shrink-0 p-4 border-t border-slate-800 bg-slate-900 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0 pr-2">
                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-slate-300 shrink-0 text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Pelanggan' }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email ?? 'pelanggan@laundryku.com' }}</p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" title="Logout" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-rose-600/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition">
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper (Adaptive: full-width on md+, centered frame on mobile) -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-100 dark:bg-slate-950 min-h-screen">
            
            <!-- Top Header Bar -->
            <header class="sticky top-0 z-30 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 px-4 py-3.5 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    @hasSection('header_back')
                        @yield('header_back')
                    @else
                        <div class="w-9 h-9 rounded-xl bg-[#3da4e0]/10 dark:bg-[#3da4e0]/20 border border-[#3da4e0]/30 flex items-center justify-center text-[#3da4e0] text-sm shadow-xs md:hidden shrink-0">
                            <i class="fa-solid fa-shirt"></i>
                        </div>
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#3da4e0] block md:hidden">Laundry<span class="font-extrabold">Ku</span></span>
                            <h1 class="text-base font-bold text-slate-900 dark:text-white leading-tight">{{ $pageTitle ?? 'Layanan Laundry Antar Jemput' }}</h1>
                            <p class="text-xs text-slate-500 dark:text-slate-400 hidden md:block">Layanan laundry profesional terpercaya langsung dari ponsel Anda</p>
                        </div>
                    @endif
                </div>

                <!-- Right Actions / Theme Toggle, Notification & Profile -->
                <div class="flex items-center gap-2">
                    <!-- Fixed Capsule Pill Dark/Light Mode Switcher -->
                    <div x-data="{ 
                            darkMode: localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
                            toggleTheme() {
                                this.darkMode = !this.darkMode;
                                if (this.darkMode) {
                                    document.documentElement.classList.add('dark');
                                    localStorage.setItem('color-theme', 'dark');
                                } else {
                                    document.documentElement.classList.remove('dark');
                                    localStorage.setItem('color-theme', 'light');
                                }
                            }
                         }" 
                         class="flex items-center">
                        
                        <button @click="toggleTheme()" 
                                type="button"
                                class="relative inline-flex h-8 w-16 shrink-0 cursor-pointer rounded-full p-0.5 transition-colors duration-300 ease-in-out focus:outline-none bg-slate-200 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 shadow-inner overflow-hidden"
                                role="switch" 
                                :aria-checked="darkMode">
                            <span class="sr-only">Toggle Mode</span>
                            
                            <!-- Sliding Thumb Pill (Constrained inside capsule) -->
                            <span :class="darkMode ? 'translate-x-8 bg-slate-900 border border-slate-700/80 shadow-md' : 'translate-x-0 bg-white border border-slate-200/50 shadow-md'"
                                  class="pointer-events-none inline-block h-7 w-7 transform rounded-full ring-0 transition duration-300 ease-in-out flex items-center justify-center text-xs">
                                <i x-show="!darkMode" class="fa-solid fa-sun text-amber-400 leading-none"></i>
                                <i x-show="darkMode" class="fa-solid fa-moon text-sky-300 leading-none" x-cloak></i>
                            </span>
                        </button>
                    </div>

                    @auth
                        @php
                            $unreadNotifs = \App\Models\Notification::where('id_user', auth()->id())->where('is_read', false)->count();
                        @endphp
                        <a href="{{ route('pelanggan.notifications') }}" class="relative p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-[#3da4e0] dark:hover:text-[#3da4e0] hover:bg-slate-50 dark:hover:bg-slate-800 transition" title="Notifikasi">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if($unreadNotifs > 0)
                                <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-white animate-pulse">
                                    {{ $unreadNotifs > 9 ? '9+' : $unreadNotifs }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('pelanggan.profile') }}" class="hidden md:flex items-center gap-2 pl-2 border-l border-slate-200 dark:border-slate-700">
                            <div class="w-8 h-8 rounded-full bg-[#3da4e0]/10 text-[#3da4e0] border border-[#3da4e0]/30 font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                            </div>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-[#3da4e0] text-white hover:bg-[#1b85c8] transition shadow-sm">
                            Masuk
                        </a>
                    @endauth
                </div>
            </header>

            <!-- Flash Notifications -->
            <div class="max-w-4xl w-full mx-auto px-4">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mt-3 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-start gap-2.5 shadow-sm transition">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1 font-medium">{{ session('success') }}</div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" class="mt-3 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm flex items-start gap-2.5 shadow-sm transition">
                        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1 font-medium">{{ session('error') }}</div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-300">&times;</button>
                    </div>
                @endif
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 px-4 py-4 md:py-6 max-w-4xl w-full mx-auto pb-24 md:pb-8">
                @yield('content')
            </main>

            <!-- Bottom Navigation Bar (Pelanggan: Beranda, Pesanan, Profil) - Visible on Mobile Only -->
            <nav class="fixed bottom-0 left-0 right-0 w-full bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex justify-around py-2.5 z-50 shadow-lg md:hidden">
                <!-- 1. Beranda -->
                <a href="{{ route('pelanggan.dashboard') }}" 
                   class="flex flex-col items-center justify-center gap-1 flex-1 py-0.5 transition {{ request()->routeIs('pelanggan.dashboard') ? 'text-[#3da4e0] font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium' }}">
                    <div class="relative {{ request()->routeIs('pelanggan.dashboard') ? 'scale-110' : '' }} transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('pelanggan.dashboard') ? '2.3' : '1.8' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span class="text-[11px] leading-tight">Beranda</span>
                </a>

                <!-- 2. Pesanan -->
                <a href="{{ route('pelanggan.orders') }}" 
                   class="flex flex-col items-center justify-center gap-1 flex-1 py-0.5 transition {{ request()->routeIs('pelanggan.orders*') ? 'text-[#3da4e0] font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium' }}">
                    <div class="relative {{ request()->routeIs('pelanggan.orders*') ? 'scale-110' : '' }} transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ request()->routeIs('pelanggan.orders*') ? '2.3' : '1.8' }}" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <span class="text-[11px] leading-tight">Pesanan</span>
                </a>

                <!-- 3. Profil -->
                <a href="{{ route('pelanggan.profile') }}" 
                   class="flex flex-col items-center justify-center gap-1 flex-1 py-0.5 transition {{ request()->routeIs('pelanggan.profile*') ? 'text-[#3da4e0] font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium' }}">
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
