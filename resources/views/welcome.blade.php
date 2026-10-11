<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaundryKu - Layanan Laundry Antar-Jemput Cepat & Terpercaya</title>
    
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

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased font-sans selection:bg-[#3da4e0]/20 selection:text-[#3da4e0] min-h-screen flex flex-col">

    <!-- 1. Dynamic Full-Width Responsive Header Navbar -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            
            <!-- Brand Logo & Name -->
            <a href="{{ route('welcome') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-[#3da4e0]/10 dark:bg-[#3da4e0]/20 border border-[#3da4e0]/30 flex items-center justify-center text-[#3da4e0] text-lg shadow-sm shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-shirt"></i>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#3da4e0] block leading-none">Antar Jemput</span>
                    <span class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight block">Laundry<span class="text-[#3da4e0] font-extrabold">Ku</span></span>
                </div>
            </a>

            <!-- Center Trust Pill (Desktop only) -->
            <div class="hidden md:flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/70 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-bold shadow-2xs">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>Outlet Pusat Aktif &bull; Radius 20 KM &bull; 08.00 - 20.00</span>
            </div>

            <!-- Right Actions: Auth or Guest Navigation + Theme Toggle -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                
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

                @guest
                    <!-- GUEST BUTTONS: Masuk & Daftar Akun -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('login') }}" 
                           class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs sm:text-sm hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" 
                           class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl bg-[#3da4e0] hover:bg-[#328bc0] text-white font-bold text-xs sm:text-sm shadow-md shadow-[#3da4e0]/25 transition transform active:scale-95">
                            Daftar Akun
                        </a>
                    </div>
                @else
                    <!-- AUTHENTICATED USER: User Avatar Dropdown (Alpine.js) -->
                    @php
                        $user = auth()->user();
                        $role = $user->role;
                        $roleLabel = match($role) {
                            'admin' => 'Administrator',
                            'driver' => 'Driver',
                            default => 'Pelanggan',
                        };
                        $roleBadgeColor = match($role) {
                            'admin' => 'bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800',
                            'driver' => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
                            default => 'bg-sky-100 text-[#3da4e0] border-sky-200 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800',
                        };
                        $dashboardUrl = match($role) {
                            'admin' => route('admin.dashboard'),
                            'driver' => route('driver.dashboard'),
                            default => route('pelanggan.dashboard'),
                        };
                        $ordersUrl = match($role) {
                            'admin' => route('admin.orderan'),
                            'driver' => route('driver.orders'),
                            default => route('pelanggan.orders'),
                        };
                    @endphp

                    <div x-data="{ userMenuOpen: false }" class="relative">
                        <!-- Dropdown Trigger Button -->
                        <button @click="userMenuOpen = !userMenuOpen" 
                                @click.outside="userMenuOpen = false"
                                type="button" 
                                class="flex items-center gap-2.5 p-1.5 sm:px-3 sm:py-1.5 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 bg-white dark:bg-slate-800 transition shadow-2xs focus:outline-none">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#3da4e0] to-[#1b85c8] text-white font-black text-xs flex items-center justify-center shadow-xs">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="text-left hidden sm:block">
                                <span class="text-xs font-bold text-slate-900 dark:text-white block truncate max-w-[120px]">{{ $user->name }}</span>
                                <span class="text-[10px] font-semibold text-slate-400 block -mt-0.5 capitalize">{{ $roleLabel }}</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-slate-400 text-xs ml-0.5"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="userMenuOpen" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             class="absolute right-0 mt-2 w-72 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 py-2 z-50 overflow-hidden font-sans">
                            
                            <!-- User Details Header -->
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/80">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-extrabold text-slate-900 dark:text-white truncate">{{ $user->name }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $roleBadgeColor }}">
                                        {{ $roleLabel }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $user->email }}</p>
                            </div>

                            <!-- Menu Navigation Links -->
                            <div class="p-1.5 space-y-0.5">
                                <a href="{{ $dashboardUrl }}" 
                                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-[#3da4e0]/10 hover:text-[#3da4e0] transition">
                                    <i class="fa-solid fa-chart-line w-4 text-[#3da4e0]"></i>
                                    <span>Ke Dashboard Saya</span>
                                </a>
                                <a href="{{ $ordersUrl }}" 
                                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-[#3da4e0]/10 hover:text-[#3da4e0] transition">
                                    <i class="fa-solid fa-box-archive w-4 text-[#3da4e0]"></i>
                                    <span>Pesanan Saya</span>
                                </a>
                                @if($role === 'pelanggan')
                                    <a href="{{ route('pelanggan.orders.create') }}" 
                                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-[#3da4e0]/10 hover:text-[#3da4e0] transition">
                                        <i class="fa-solid fa-plus-circle w-4 text-emerald-500"></i>
                                        <span>Buat Pesanan Laundry</span>
                                    </a>
                                @endif
                            </div>

                            <!-- Logout Action Form -->
                            <div class="p-1.5 border-t border-slate-100 dark:border-slate-700">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition text-left">
                                        <i class="fa-solid fa-right-from-bracket w-4 text-rose-500"></i>
                                        <span>Keluar (Logout)</span>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                @endguest
            </div>

        </div>
    </header>

    <!-- 2. Main Landing Page Content (Full-Width Responsive) -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-12 sm:space-y-16">
        
        <!-- HERO SECTION (2-Column Grid on Desktop / Tablet) -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- Left Column: Copywriting & Actions -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-100/80 dark:bg-sky-950/60 text-[#3da4e0] text-xs font-bold border border-sky-200 dark:border-sky-800">
                    <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                    <span>Solusi Praktis &bull; Antar Jemput Radius 20 KM</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                    Cucian Menumpuk? <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#3da4e0] to-[#1b85c8]">
                        Biar LaundryKu Yang Beresin!
                    </span>
                </h1>

                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    Layanan laundry pintar antar-jemput cepat langsung dari smartphone Anda. Tentukan titik jemput di peta GPS, kurir kami langsung meluncur, pakaian ditimbang rapi, dan bayar praktis via QRIS.
                </p>

                <!-- Hero CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 pt-2">
                    @auth
                        <a href="{{ $dashboardUrl }}" 
                           class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-[#3da4e0] hover:bg-[#328bc0] text-white font-bold text-sm shadow-lg shadow-[#3da4e0]/30 transition transform active:scale-95 flex items-center justify-center gap-2.5">
                            <i class="fa-solid fa-gauge"></i>
                            <span>Buka Dashboard ({{ $user->name }})</span>
                        </a>
                        @if($role === 'pelanggan')
                            <a href="{{ route('pelanggan.orders.create') }}" 
                               class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2.5">
                                <i class="fa-solid fa-plus"></i>
                                <span>Pesan Laundry Sekarang</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" 
                           class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-[#3da4e0] hover:bg-[#328bc0] text-white font-bold text-sm shadow-lg shadow-[#3da4e0]/30 transition transform active:scale-95 flex items-center justify-center gap-2.5">
                            <i class="fa-solid fa-sparkles"></i>
                            <span>Pesan Sekarang &bull; Daftar Gratis</span>
                        </a>
                        <a href="{{ route('login') }}" 
                           class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold text-sm hover:bg-slate-100 dark:hover:bg-slate-700 transition flex items-center justify-center gap-2">
                            <span>Sudah Punya Akun? Masuk</span>
                        </a>
                    @endauth
                </div>

                <!-- Trust Statistics Row -->
                <div class="grid grid-cols-3 gap-3 sm:gap-4 pt-4 max-w-lg mx-auto lg:mx-0">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 border border-slate-200/80 dark:border-slate-800 shadow-2xs text-center">
                        <div class="text-sm sm:text-base font-black text-slate-900 dark:text-white flex items-center justify-center gap-1">
                            <i class="fa-solid fa-star text-amber-400 text-xs"></i> 4.9 / 5.0
                        </div>
                        <div class="text-[10px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">Rating Kepuasan</div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 border border-slate-200/80 dark:border-slate-800 shadow-2xs text-center">
                        <div class="text-sm sm:text-base font-black text-slate-900 dark:text-white flex items-center justify-center gap-1">
                            <i class="fa-solid fa-bolt text-[#3da4e0] text-xs"></i> &lt; 30 Menit
                        </div>
                        <div class="text-[10px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">Respon Jemput</div>
                    </div>
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 border border-slate-200/80 dark:border-slate-800 shadow-2xs text-center">
                        <div class="text-sm sm:text-base font-black text-slate-900 dark:text-white flex items-center justify-center gap-1">
                            <i class="fa-solid fa-shield-halved text-emerald-500 text-xs"></i> 100%
                        </div>
                        <div class="text-[10px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">Garansi Higienis</div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Aesthetic Hero Visual Banner with Badges -->
            <div class="lg:col-span-5">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-slate-200/90 dark:border-slate-800 aspect-[4/3] bg-slate-900 group">
                    <img src="https://images.unsplash.com/photo-1545173168-9f1947eebb7f?auto=format&fit=crop&w=1000&q=80" 
                         alt="Layanan Laundry Bersih LaundryKu" 
                         class="w-full h-full object-cover object-center transform group-hover:scale-105 transition duration-700 ease-out brightness-[0.92]">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>

                    <!-- Floating Badge Pill 1: Top Left (Radius 20 KM) -->
                    <div class="absolute top-4 left-4">
                        <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-white/95 dark:bg-slate-900/95 backdrop-blur-md text-slate-800 dark:text-white text-xs font-black shadow-lg border border-white/60 dark:border-slate-700">
                            <span class="w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center text-xs">⚡</span>
                            <span>Jangkauan Luas Radius 20 KM</span>
                        </div>
                    </div>

                    <!-- Floating Badge Pill 2: Bottom Right (Hasil Rapi & Wangi) -->
                    <div class="absolute bottom-4 right-4">
                        <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-white/95 dark:bg-slate-900/95 backdrop-blur-md text-slate-800 dark:text-white text-xs font-black shadow-lg border border-white/60 dark:border-slate-700">
                            <span class="w-6 h-6 rounded-full bg-sky-100 dark:bg-sky-950/60 text-[#3da4e0] flex items-center justify-center text-xs">✨</span>
                            <span>Rapi, Wangi, &amp; Higienis</span>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <!-- SERVICES SHOWCASE SECTION ("Layanan Kita") -->
        <section class="space-y-6">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-xs font-extrabold uppercase tracking-widest text-[#3da4e0]">Katalog Layanan Pilihan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Paket Laundry Lengkap &amp; Terjangkau</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Pilih jenis layanan sesuai kebutuhan harian atau prioritas Anda</p>
            </div>

            <!-- 3 Service Cards Grid (Responsive across devices) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Package 1: Kiloan Reguler -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between space-y-5 group">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-950/60 text-[#3da4e0] flex items-center justify-center text-xl font-bold group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-weight-hanging"></i>
                        </div>
                        <div>
                            <div class="inline-block px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[10px] uppercase mb-2">Hemat Harian</div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white">Cuci Komplit Kiloan</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                Solusi pakaian harian keluarga. Dicuci bersih, dikeringkan optimal, dan disetrika rapi wangi.
                            </p>
                        </div>
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2 text-xs text-slate-600 dark:text-slate-300">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                <span>Pengerjaan reguler 2 hari kerja</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                <span>Deterjen ramah serat &amp; pelembut wangi</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                <span>Packing plastik rapi anti lembap</span>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold block">Mulai dari</span>
                            <span class="text-lg font-black text-slate-900 dark:text-white">Rp 8.000 <span class="text-xs font-semibold text-slate-400">/ Kg</span></span>
                        </div>
                        <a href="{{ auth()->check() ? route('pelanggan.orders.create') : route('register') }}" 
                           class="px-4 py-2 rounded-xl bg-[#3da4e0] hover:bg-[#328bc0] text-white font-bold text-xs shadow-xs transition">
                            Pesan
                        </a>
                    </div>
                </div>

                <!-- Package 2: Express Priority (Featured) -->
                <div class="bg-gradient-to-b from-sky-50/70 to-white dark:from-sky-950/30 dark:to-slate-900 rounded-3xl p-6 border-2 border-[#3da4e0] shadow-md hover:shadow-lg transition duration-300 flex flex-col justify-between space-y-5 relative group">
                    <div class="absolute -top-3 right-6 bg-[#3da4e0] text-white px-3 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm">
                        Terfavorit 🔥
                    </div>
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#3da4e0] text-white flex items-center justify-center text-xl font-bold group-hover:scale-110 transition-transform shadow-md shadow-[#3da4e0]/30">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div>
                            <div class="inline-block px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold text-[10px] uppercase mb-2">Kilat 6 Jam</div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white">Express Kilat Same-Day</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                Butuh pakaian cepat untuk bepergian atau acara penting? Selesai dan langsung diantar hari yang sama.
                            </p>
                        </div>
                        <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800 space-y-2 text-xs text-slate-600 dark:text-slate-300">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                <span>Selesai kilat 6-8 jam di hari yang sama</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                <span>Mesin khusus tanpa dicampur pakaian lain</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                <span>Prioritas penjemputan &amp; pengantaran kurir</span>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold block">Mulai dari</span>
                            <span class="text-lg font-black text-[#3da4e0]">Rp 15.000 <span class="text-xs font-semibold text-slate-400">/ Kg</span></span>
                        </div>
                        <a href="{{ auth()->check() ? route('pelanggan.orders.create') : route('register') }}" 
                           class="px-4 py-2 rounded-xl bg-[#3da4e0] hover:bg-[#328bc0] text-white font-bold text-xs shadow-md shadow-[#3da4e0]/30 transition">
                            Pilih Express
                        </a>
                    </div>
                </div>

                <!-- Package 3: Satuan & Bedding -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between space-y-5 group">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-300 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-mattress-pillow"></i>
                        </div>
                        <div>
                            <div class="inline-block px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[10px] uppercase mb-2">Perawatan Khusus</div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white">Satuan &amp; Bed Cover</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                Khusus bed cover, jas formal, selimut tebal, sprei hotel, dan boneka dengan treatment higienis.
                            </p>
                        </div>
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2 text-xs text-slate-600 dark:text-slate-300">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                <span>Treatment khusus material kain sensitif</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                <span>Ekstra sanitasi anti debu &amp; tungau</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                <span>Termasuk hanger / packing khusus tebal</span>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold block">Mulai dari</span>
                            <span class="text-lg font-black text-slate-900 dark:text-white">Rp 25.000 <span class="text-xs font-semibold text-slate-400">/ Pcs</span></span>
                        </div>
                        <a href="{{ auth()->check() ? route('pelanggan.orders.create') : route('register') }}" 
                           class="px-4 py-2 rounded-xl bg-[#3da4e0] hover:bg-[#328bc0] text-white font-bold text-xs shadow-xs transition">
                            Pesan
                        </a>
                    </div>
                </div>

            </div>
        </section>

        <!-- 4-STEP HOW IT WORKS SECTION -->
        <section class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-8">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <span class="text-xs font-extrabold uppercase tracking-widest text-[#3da4e0]">Alur Pemesanan</span>
                <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">4 Langkah Praktis Tanpa Ribet</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Cukup duduk santai di rumah, kurir kami yang bekerja untuk Anda</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 space-y-3 relative">
                    <span class="w-8 h-8 rounded-xl bg-[#3da4e0] text-white font-black text-sm flex items-center justify-center shadow-md shadow-[#3da4e0]/20">1</span>
                    <h4 class="font-bold text-sm text-slate-900 dark:text-white">Tentukan Lokasi Jemput</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Pilih titik penjemputan di peta interaktif GPS OpenStreetMap. Radius otomatis tervalidasi hingga 20 KM.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 space-y-3 relative">
                    <span class="w-8 h-8 rounded-xl bg-[#3da4e0] text-white font-black text-sm flex items-center justify-center shadow-md shadow-[#3da4e0]/20">2</span>
                    <h4 class="font-bold text-sm text-slate-900 dark:text-white">Kurir Mengambil Cucian</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Driver terverifikasi langsung menuju alamat Anda, mengambil pakaian kotor, dan mengunggah foto serah terima.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 space-y-3 relative">
                    <span class="w-8 h-8 rounded-xl bg-[#3da4e0] text-white font-black text-sm flex items-center justify-center shadow-md shadow-[#3da4e0]/20">3</span>
                    <h4 class="font-bold text-sm text-slate-900 dark:text-white">Penimbangan &amp; Bayar QRIS</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Pakaian ditimbang akurat di outlet pusat, invoice diterbitkan, dan bayar seketika melalui Midtrans QRIS.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 space-y-3 relative">
                    <span class="w-8 h-8 rounded-xl bg-[#3da4e0] text-white font-black text-sm flex items-center justify-center shadow-md shadow-[#3da4e0]/20">4</span>
                    <h4 class="font-bold text-sm text-slate-900 dark:text-white">Diantar Bersih &amp; Rapi</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Pakaian selesai dicuci dan disetrika wangi, lalu diantar kembali langsung ke depan pintu Anda.
                    </p>
                </div>

            </div>
        </section>

    </main>

    <!-- 3. Aesthetic Responsive Footer -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200/80 dark:border-slate-800 mt-16 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-[#3da4e0]/10 dark:bg-[#3da4e0]/20 border border-[#3da4e0]/30 text-[#3da4e0] flex items-center justify-center text-xs font-bold shadow-xs shrink-0">
                    <i class="fa-solid fa-shirt"></i>
                </div>
                <span class="font-extrabold text-sm text-slate-900 dark:text-white">Laundry<span class="text-[#3da4e0] font-extrabold">Ku</span></span>
                <span class="text-xs text-slate-400 dark:text-slate-500">&bull; Sistem Operasional Laundry Antar Jemput Pintar</span>
            </div>
            <p class="text-xs text-slate-400 dark:text-slate-500">
                &copy; {{ date('Y') }} LaundryKu. Hak cipta dilindungi undang-undang.
            </p>
        </div>
    </footer>

    <!-- 4. Mobile Fixed Quick Action Bar (Visible only on Mobile screens) -->
    <div class="fixed bottom-0 left-0 right-0 z-30 px-4 py-3 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 md:hidden shadow-lg">
        @auth
            <a href="{{ $dashboardUrl }}" 
               class="w-full bg-[#3da4e0] hover:bg-[#328bc0] text-white font-bold py-3 rounded-2xl text-xs shadow-md shadow-[#3da4e0]/30 transition text-center flex items-center justify-center gap-2">
                <i class="fa-solid fa-gauge"></i>
                <span>Buka Dashboard ({{ $user->name }})</span>
            </a>
        @else
            <div class="grid grid-cols-2 gap-2.5 w-full">
                <a href="{{ route('login') }}" 
                   class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold py-3 rounded-2xl text-xs transition text-center block">
                    Masuk
                </a>
                <a href="{{ route('register') }}" 
                   class="bg-[#3da4e0] hover:bg-[#328bc0] text-white font-bold py-3 rounded-2xl text-xs shadow-md shadow-[#3da4e0]/30 transition text-center block">
                    Daftar Akun
                </a>
            </div>
        @endauth
    </div>

</body>
</html>
