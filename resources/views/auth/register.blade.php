<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Akun Pelanggan - LaundryKu</title>
    
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased font-sans min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 space-y-6 relative">
        
        <!-- Capsule Pill Dark/Light Mode Switcher (Top-Right) -->
        <div class="absolute top-5 right-5">
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
        </div>

        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <a href="{{ route('welcome') }}" class="inline-block group">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-[#3da4e0]/10 dark:bg-[#3da4e0]/20 border border-[#3da4e0]/30 flex items-center justify-center text-[#3da4e0] text-2xl shadow-sm group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-shirt"></i>
                </div>
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Laundry<span class="text-[#3da4e0] font-extrabold">Ku</span></h1>
            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Daftar Akun Baru Pelanggan</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Nikmati kemudahan laundry antar-jemput di depan pintu Anda</p>
        </div>

        <!-- Register Form -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap</label>
                <input type="text" name="name" required value="{{ old('name') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition placeholder-slate-400 dark:placeholder-slate-500"
                       placeholder="Contoh: Siti Rahmawati">
                @error('name')
                    <p class="text-xs text-rose-500 dark:text-rose-400 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email</label>
                <input type="email" name="email" required value="{{ old('email') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition placeholder-slate-400 dark:placeholder-slate-500"
                       placeholder="nama@email.com">
                @error('email')
                    <p class="text-xs text-rose-500 dark:text-rose-400 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp / HP</label>
                <input type="tel" name="phone" required value="{{ old('phone') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition placeholder-slate-400 dark:placeholder-slate-500"
                       placeholder="081234567890">
                @error('phone')
                    <p class="text-xs text-rose-500 dark:text-rose-400 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi</label>
                    <input type="password" name="password" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition placeholder-slate-400 dark:placeholder-slate-500"
                           placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Ulangi Sandi</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition placeholder-slate-400 dark:placeholder-slate-500"
                           placeholder="••••••••">
                </div>
            </div>
            @error('password')
                <p class="text-xs text-rose-500 dark:text-rose-400 flex items-center gap-1">
                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                </p>
            @enderror

            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl font-bold text-sm bg-[#3da4e0] hover:bg-[#1b85c8] text-white shadow-lg shadow-[#3da4e0]/30 transition active:scale-[0.99] flex items-center justify-center gap-2">
                <span>Daftar Sekarang</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <div class="text-center space-y-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="font-bold text-[#3da4e0] hover:underline">Masuk disini</a>
            </p>
            <div>
                <a href="{{ route('welcome') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>

    </div>

</body>
</html>
