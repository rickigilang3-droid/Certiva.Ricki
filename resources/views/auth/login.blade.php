<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk Akun Admin - Certiva</title>

    <!-- Inline Theme Script (instant theme detection) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('certiva_theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 dark:text-slate-100 bg-slate-100 dark:bg-[#0B0F19] transition-colors duration-200 selection:bg-indigo-500 selection:text-white" 
      x-data="{ 
          email: '{{ old('email', '') }}', 
          password: '', 
          showPassword: false
      }">

    <!-- Ambient Glowing Orbs in Background (Animated) -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-32 -left-32 w-[600px] h-[600px] bg-indigo-500/10 dark:bg-indigo-600/20 rounded-full blur-[140px] animate-float-slow"></div>
        <div class="absolute top-1/2 -right-32 w-[550px] h-[550px] bg-cyan-500/10 dark:bg-cyan-600/15 rounded-full blur-[140px] animate-float-reverse"></div>
        <div class="absolute -bottom-32 left-1/3 w-[500px] h-[500px] bg-blue-500/10 dark:bg-blue-600/15 rounded-full blur-[140px] animate-float"></div>
    </div>

    <div class="min-h-full relative z-10 flex flex-col lg:flex-row">
        
        <!-- Left Column: Branding, Context & Cryptographic Security (Desktop only) -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-indigo-50/70 via-slate-100/90 to-blue-50/70 dark:from-slate-950 dark:via-[#0E1528] dark:to-[#090D18] p-12 xl:p-16 flex-col justify-between border-r border-slate-200 dark:border-slate-800/80 text-slate-900 dark:text-white transition-colors duration-200">
            
            <!-- Top Brand -->
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3.5 group">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white p-1.5 flex items-center justify-center shadow-md border border-slate-200 dark:border-slate-700/80 group-hover:scale-110 group-hover:rotate-1 group-hover:shadow-indigo-500/20 transition-all duration-300 shrink-0">
                        <img src="{{ asset('images/logo.png') }}" alt="Certiva Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="font-extrabold text-2xl tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Certiva</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 font-mono border border-indigo-200 dark:border-indigo-500/30 font-bold animate-pulse-glow hover:scale-105 transition-transform">RSA-PSS</span>
                        </div>
                        <p class="text-xs text-slate-800 dark:text-slate-100 font-bold mt-0.5">Universitas Bina Sarana Informatika</p>
                        <p class="text-[11px] text-slate-600 dark:text-slate-300 font-medium">Cryptographic Certificate Authority</p>
                    </div>
                </a>
            </div>

            <!-- Middle Value Proposition -->
            <div class="space-y-8 my-auto py-10 max-w-lg">
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 text-xs font-semibold border border-emerald-500/20 dark:border-emerald-500/25 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                        <span>Sistem Keamanan Ijazah & Sertifikat Digital</span>
                    </div>
                    <h1 class="text-3xl xl:text-4xl font-extrabold leading-tight text-slate-900 dark:text-transparent dark:bg-clip-text dark:bg-gradient-to-r dark:from-white dark:via-slate-100 dark:to-slate-300">
                        Penerbitan Ijazah Kampus Anti-Pemalsuan Mutlak
                    </h1>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                        Mengamankan ribuan sertifikat kelulusan dengan tanda tangan digital asimetris. Perubahan nama, tanggal, maupun berkas PDF sekecil apapun akan langsung membatalkan validasi.
                    </p>
                </div>

                <!-- 2 Feature Highlight Cards -->
                <div class="space-y-3.5">
                    <div class="group flex items-start gap-4 p-4 rounded-2xl bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 backdrop-blur hover:border-indigo-400 dark:hover:border-indigo-500/50 hover:bg-white dark:hover:bg-slate-900 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/20 border border-indigo-200 dark:border-indigo-500/40 text-indigo-600 dark:text-indigo-300 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-500/30 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <div class="font-bold text-sm text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors">Tanda Tangan Digital RSA-2048 & RSA-PSS</div>
                            <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">Menggunakan skema tanda tangan probabilitistik RFC 8017 dengan MGF1 & hash SHA-256 yang mustahil dipalsukan.</div>
                        </div>
                    </div>

                    <div class="group flex items-start gap-4 p-4 rounded-2xl bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 backdrop-blur hover:border-cyan-400 dark:hover:border-cyan-500/50 hover:bg-white dark:hover:bg-slate-900 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-500/20 border border-cyan-200 dark:border-cyan-500/40 text-cyan-600 dark:text-cyan-300 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-cyan-100 dark:group-hover:bg-cyan-500/30 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <div class="font-bold text-sm text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-300 transition-colors">Verifikasi Publik Instan 24/7</div>
                            <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">Pindai Simple QR Code pada PDF untuk mencocokkan data langsung dengan pangkalan data institusi kampus.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Cryptographic Security Badge -->
            <div class="pt-6 border-t border-slate-200 dark:border-slate-800">
                <div class="p-4 rounded-2xl bg-white/95 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 font-mono flex flex-wrap items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span>Algorithm: <strong class="text-slate-900 dark:text-white">RSA-2048</strong></span>
                        <span>&bull;</span>
                        <span>Hash: <strong class="text-slate-900 dark:text-white">SHA-256</strong></span>
                        <span>&bull;</span>
                        <span>Scheme: <strong class="text-indigo-600 dark:text-cyan-300 font-bold">RSA-PSS</strong></span>
                    </div>
                    <div class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-slate-600 dark:text-slate-300 font-medium">Pejabat Otoritas:</span>
                        <span class="text-slate-900 dark:text-white font-extrabold font-sans">Ricki Gilang Saputra</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Login Form Container (Supports Light and Dark Mode) -->
        <div class="w-full lg:w-1/2 flex flex-col justify-between p-6 sm:p-12 lg:p-16 bg-slate-50 dark:bg-[#0B0F19] transition-colors duration-200">
            
            <!-- Top Navigation & Theme Toggle -->
            <div class="flex items-center justify-between animate-fade-in-down">
                <!-- Mobile Logo -->
                <a href="{{ route('home') }}" class="lg:hidden flex items-center gap-3 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 bg-white p-1 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 shrink-0 flex items-center justify-center group-hover:scale-105 group-hover:rotate-1 transition-all duration-300">
                        <img src="{{ asset('images/logo.png') }}" alt="Certiva Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="font-extrabold text-base text-slate-900 dark:text-white">Certiva</div>
                        <div class="text-[11px] text-slate-700 dark:text-slate-300 font-semibold">Universitas Bina Sarana Informatika</div>
                    </div>
                </a>

                <div class="flex items-center gap-3 ml-auto">
                    <!-- Theme Switcher Button -->
                    <button type="button" @click="$store.theme.toggle()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:scale-105 active:scale-95 shadow-sm transition-all duration-200">
                        <!-- Sun Icon when dark -->
                        <svg x-show="$store.theme.isDark" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon Icon when light -->
                        <svg x-show="!$store.theme.isDark" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <span x-text="$store.theme.isDark ? 'Mode Terang' : 'Mode Gelap'"></span>
                    </button>

                    <!-- Daftar Akun Button -->
                    <a href="{{ route('register') }}" class="text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:scale-105 active:scale-95 flex items-center gap-1.5 transition-all duration-200 px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <span>Daftar Akun</span>
                    </a>

                    <a href="{{ route('verify.index') }}" class="hidden sm:flex text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:scale-105 active:scale-95 items-center gap-1.5 transition-all duration-200 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-transparent hover:bg-white dark:hover:bg-slate-800/50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Portal Verifikasi</span>
                    </a>
                </div>
            </div>

            <!-- Login Card -->
            <div class="max-w-md w-full mx-auto my-auto py-8 animate-fade-in-up">
                <div class="bg-white dark:bg-slate-900/85 backdrop-blur-xl border border-slate-200/90 dark:border-slate-800 rounded-3xl p-7 sm:p-9 shadow-xl hover:shadow-2xl dark:shadow-black/50 dark:hover:shadow-indigo-950/30 hover:border-indigo-300 dark:hover:border-indigo-500/30 hover:-translate-y-0.5 transition-all duration-500 space-y-6">
                    
                    <!-- Header -->
                    <div class="space-y-1 text-center sm:text-left">
                        <div class="inline-block p-2.5 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 mb-2 transition-all duration-300 hover:scale-110 hover:rotate-6 hover:shadow-md cursor-default">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Masuk Akun Admin
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                            Akses dewan akademik, rektorat, dan staf administrator penerbit ijazah.
                        </p>
                    </div>

                    <!-- Session Status / Errors -->
                    @if (session('status'))
                        <div class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-400 text-xs flex items-center gap-2 animate-fade-in-down">
                            <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-400 text-xs space-y-1 animate-fade-in-down">
                            <div class="font-bold flex items-center gap-1.5">
                                <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Gagal autentikasi:</span>
                            </div>
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <!-- Email Input -->
                        <div class="space-y-1.5">
                            <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Alamat Email Resmi
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 group-focus-within:scale-110 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                                </div>
                                <input id="email" type="email" name="email" x-model="email" required autofocus autocomplete="username"
                                       placeholder="admin@bsi.ac.id atau nama@certiva.local"
                                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 text-xs font-medium focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 focus:-translate-y-0.5 focus:shadow-md transition-all duration-200" />
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Kata Sandi
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 hover:underline transition-colors">
                                        Lupa kata sandi?
                                    </a>
                                @endif
                            </div>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 group-focus-within:scale-110 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" x-model="password" required autocomplete="current-password"
                                       placeholder="••••••••"
                                       class="w-full pl-10 pr-10 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950/60 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 text-xs focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 focus:-translate-y-0.5 focus:shadow-md transition-all duration-200 font-mono" />
                                
                                <!-- Toggle Password Visibility -->
                                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:scale-110 active:scale-95 transition-all duration-150">
                                    <svg class="w-4 h-4" x-show="!showPassword" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg class="w-4 h-4" x-show="showPassword" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between pt-1">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                                <input id="remember_me" type="checkbox" name="remember" 
                                       class="rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4 transition-transform group-hover:scale-105">
                                <span class="ms-2 text-xs font-medium text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Ingat sesi di perangkat ini</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="group relative overflow-hidden w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/25 hover:shadow-indigo-600/40 hover:scale-[1.01] active:scale-[0.99] transition-all duration-300 flex items-center justify-center gap-2 cursor-pointer">
                            <div class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/20 to-transparent pointer-events-none"></div>
                            <span>Masuk ke Dashboard Admin</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>

                        <!-- Register Link -->
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 text-center">
                            <p class="text-xs text-slate-600 dark:text-slate-400">
                                Belum memiliki akun admin?
                                <a href="{{ route('register') }}" class="group inline-flex items-center gap-1 font-bold text-indigo-600 dark:text-cyan-400 hover:text-indigo-700 dark:hover:text-cyan-300 hover:underline transition-all duration-200 ml-1">
                                    <span>Daftar Akun Baru</span>
                                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs text-slate-600 dark:text-slate-400 py-2 font-medium">
                &copy; {{ date('Y') }} Certiva Cryptographic System &bull; Universitas Bina Sarana Informatika
            </div>
        </div>

    </div>

</body>
</html>
