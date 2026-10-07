<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Akun Pelanggan - LaundryKu</title>
    
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Daftar Akun Baru</h1>
            <p class="text-xs text-slate-500">Nikmati kemudahan laundry antar-jemput di depan pintu Anda</p>
        </div>

        <!-- Register Form -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" required value="{{ old('name') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                       placeholder="Contoh: Siti Rahmawati">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" required value="{{ old('email') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                       placeholder="nama@email.com">
                @error('email')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                <input type="tel" name="phone" required value="{{ old('phone') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                       placeholder="081234567890">
                @error('phone')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                    <input type="password" name="password" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                           placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ulangi Sandi</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#3da4e0] focus:border-transparent transition"
                           placeholder="••••••••">
                </div>
            </div>
            @error('password')
                <p class="text-xs text-rose-500">{{ $message }}</p>
            @enderror

            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl font-bold text-sm bg-[#3da4e0] hover:bg-[#1b85c8] text-white shadow-lg shadow-[#3da4e0]/30 transition active:scale-[0.99]">
                Daftar Sekarang
            </button>
        </form>

        <div class="text-center text-xs text-slate-500 pt-2">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="font-bold text-[#3da4e0] hover:underline">Masuk disini</a>
        </div>

    </div>

</body>
</html>
