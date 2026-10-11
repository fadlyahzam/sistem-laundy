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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
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
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased font-sans" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">
        
        <!-- Mobile Sidebar Backdrop & Drawer -->
        <div x-show="sidebarOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex md:hidden"
             role="dialog" aria-modal="true">
            <div @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
            
            <div class="relative w-64 bg-slate-900 text-white flex flex-col h-screen z-50">
                <!-- 1. Brand Header (Fixed Top) -->
                <div class="p-5 border-b border-slate-800 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#3da4e0]/10 dark:bg-[#3da4e0]/20 border border-[#3da4e0]/30 flex items-center justify-center text-[#3da4e0] text-lg shadow-sm shrink-0">
                            <i class="fa-solid fa-shirt"></i>
                        </div>
                        <div>
                            <span class="font-extrabold text-base tracking-wide text-white">Laundry<span class="text-[#3da4e0] font-extrabold">Ku</span></span>
                            <span class="block text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Admin Portal</span>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white p-1">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- 2. Navigation Menu (Scrollable Area) -->
                <nav class="flex-1 overflow-y-auto p-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                        <i class="fa-solid fa-chart-line w-5"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.orderan') }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('admin.orderan*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                        <i class="fa-solid fa-box-archive w-5"></i> Orderan
                    </a>
                    <a href="{{ route('admin.laporan') }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('admin.laporan') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                        <i class="fa-solid fa-file-invoice-dollar w-5"></i> Laporan
                    </a>
                    <a href="{{ route('admin.produk') }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('admin.produk*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                        <i class="fa-solid fa-tags w-5"></i> Kelola Produk
                    </a>
                    <a href="{{ route('admin.driver') }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('admin.driver*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                        <i class="fa-solid fa-user-gear w-5"></i> Driver
                    </a>
                </nav>

                <!-- 3. Profile & Logout Footer (Pinned Permanently at Bottom) -->
                <div class="shrink-0 p-4 border-t border-slate-800 bg-slate-900 flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0 pr-2">
                        <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-slate-300 shrink-0 text-xs">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <p class="text-[10px] text-slate-400 truncate">Admin Laundry</p>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" title="Logout" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-rose-600/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition">
                            <i class="fa-solid fa-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Left Sidebar Navigation -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col h-screen sticky top-0 shrink-0 hidden md:flex z-40">
            <!-- 1. Brand Header (Fixed Top) -->
            <div class="p-5 border-b border-slate-800 flex items-center gap-3 shrink-0">
                <div class="w-10 h-10 rounded-2xl bg-[#3da4e0]/10 dark:bg-[#3da4e0]/20 border border-[#3da4e0]/30 flex items-center justify-center text-[#3da4e0] text-lg shadow-sm shrink-0">
                    <i class="fa-solid fa-shirt"></i>
                </div>
                <div>
                    <span class="font-extrabold text-base tracking-wide text-white">Laundry<span class="text-[#3da4e0] font-extrabold">Ku</span></span>
                    <span class="block text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Admin Portal</span>
                </div>
            </div>

            <!-- 2. Navigation Menu (Scrollable Area) -->
            <nav class="flex-1 overflow-y-auto p-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                    <i class="fa-solid fa-chart-line w-5"></i> Dashboard
                </a>
                <a href="{{ route('admin.orderan') }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('admin.orderan*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                    <i class="fa-solid fa-box-archive w-5"></i> Orderan
                </a>
                <a href="{{ route('admin.laporan') }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('admin.laporan') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5"></i> Laporan
                </a>
                <a href="{{ route('admin.produk') }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('admin.produk*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                    <i class="fa-solid fa-tags w-5"></i> Kelola Produk
                </a>
                <a href="{{ route('admin.driver') }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-xl transition {{ request()->routeIs('admin.driver*') ? 'bg-[#3da4e0] text-white shadow-md shadow-[#3da4e0]/30 font-bold' : 'text-slate-300 hover:bg-slate-800/90 hover:text-white' }}">
                    <i class="fa-solid fa-user-gear w-5"></i> Driver
                </a>
            </nav>

            <!-- 3. Profile & Logout Footer (Pinned Permanently at Bottom) -->
            <div class="shrink-0 p-4 border-t border-slate-800 bg-slate-900 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0 pr-2">
                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-slate-300 shrink-0 text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-slate-400 truncate">Admin Laundry</p>
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

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 dark:bg-slate-950 overflow-hidden">
            
            <!-- Top Navbar -->
            <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="md:hidden text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white leading-tight">{{ $pageTitle ?? 'Dashboard Outlet' }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">Kelola operasional laundry antar jemput secara realtime</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
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

                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#3da4e0]/10 text-[#3da4e0] border border-[#3da4e0]/20">
                        <span class="w-2 h-2 rounded-full bg-[#3da4e0] animate-pulse"></span>
                        Radius Aktif: 20 KM
                    </span>
                </div>
            </header>

            <!-- Alerts -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mx-6 mt-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mx-6 mt-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-200">&times;</button>
                </div>
            @endif

            <!-- Main Page Content -->
            <main class="flex-1 p-4 sm:p-6 overflow-y-auto bg-slate-50 dark:bg-slate-950">
                @yield('content')
            </main>

        </div>


    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @stack('scripts')
</body>
</html>
