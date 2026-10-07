<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Masuk - LaundryKu</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-slate-200/80 p-6 sm:p-8 space-y-6">
        
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-tr from-[#3da4e0] to-[#1b85c8] flex items-center justify-center text-white shadow-xl shadow-[#3da4e0]/30">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Laundry<span class="text-[#3da4e0]">Ku</span></h1>
            <p class="text-xs text-slate-500">Aplikasi Laundry Online Antar Jemput (Radius 20 KM)</p>
        </div>

        @if(session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" id="email-input" required value="{{ old('email') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                       placeholder="nama@email.com">
                @error('email')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                <input type="password" name="password" id="password-input" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                       placeholder="••••••••">
                @error('password')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#3da4e0] focus:ring-[#3da4e0]">
                    <span class="text-slate-600">Ingat saya</span>
                </label>
            </div>

            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl font-bold text-sm bg-[#3da4e0] hover:bg-[#1b85c8] text-white shadow-lg shadow-[#3da4e0]/30 transition active:scale-[0.99]">
                Masuk ke Aplikasi
            </button>
        </form>

        <!-- Quick Demo Switcher -->
        <div class="pt-2 border-t border-slate-100">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center mb-2.5">
                ⚡ Akun Demo (Klik untuk Isi Cepat)
            </p>
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <button type="button" onclick="fillCreds('pelanggan@laundryku.com', 'password')"
                        class="p-2 rounded-xl bg-slate-50 hover:bg-[#3da4e0]/10 border border-slate-200 hover:border-[#3da4e0]/30 transition group">
                    <span class="block font-bold text-slate-700 group-hover:text-[#3da4e0]">Pelanggan</span>
                    <span class="text-[10px] text-slate-400">Web Mobile</span>
                </button>
                <button type="button" onclick="fillCreds('driver@laundryku.com', 'password')"
                        class="p-2 rounded-xl bg-slate-50 hover:bg-[#3da4e0]/10 border border-slate-200 hover:border-[#3da4e0]/30 transition group">
                    <span class="block font-bold text-slate-700 group-hover:text-[#3da4e0]">Driver</span>
                    <span class="text-[10px] text-slate-400">Web Mobile</span>
                </button>
                <button type="button" onclick="fillCreds('admin@laundryku.com', 'password')"
                        class="p-2 rounded-xl bg-slate-50 hover:bg-[#3da4e0]/10 border border-slate-200 hover:border-[#3da4e0]/30 transition group">
                    <span class="block font-bold text-slate-700 group-hover:text-[#3da4e0]">Admin</span>
                    <span class="text-[10px] text-slate-400">Responsive</span>
                </button>
            </div>
        </div>

        <div class="text-center text-xs text-slate-500">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="font-bold text-[#3da4e0] hover:underline">Daftar sekarang</a>
        </div>

    </div>

    <script>
        function fillCreds(email, password) {
            document.getElementById('email-input').value = email;
            document.getElementById('password-input').value = password;
        }
    </script>
</body>
</html>
