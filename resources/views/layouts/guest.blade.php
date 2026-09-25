<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Certiva') }} - Keamanan Akun</title>

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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-slate-900 dark:text-slate-100 bg-slate-100 dark:bg-[#0B0F19] transition-colors duration-200 selection:bg-indigo-500 selection:text-white">
        <div class="relative min-h-full overflow-hidden flex items-center justify-center px-4 py-8 sm:px-6">
            <div class="pointer-events-none absolute -top-40 -left-32 h-96 w-96 rounded-full bg-indigo-400/20 blur-3xl dark:bg-indigo-600/20"></div>
            <div class="pointer-events-none absolute -bottom-40 -right-32 h-96 w-96 rounded-full bg-cyan-400/20 blur-3xl dark:bg-cyan-600/15"></div>

            <main class="relative z-10 w-full max-w-md">
                <div class="mb-6 flex items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white p-1 shadow-sm ring-1 ring-slate-200 transition group-hover:scale-105 dark:ring-slate-700">
                            <img src="{{ asset('images/logo.png') }}" alt="Certiva" class="h-full w-full object-contain">
                        </span>
                        <span>
                            <span class="block text-base font-extrabold tracking-tight text-slate-900 dark:text-white">Certiva</span>
                            <span class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400">Universitas Bina Sarana Informatika</span>
                        </span>
                    </a>

                    <button type="button" @click="$store.theme.toggle()" class="rounded-xl border border-slate-300 bg-white p-2.5 text-slate-600 shadow-sm transition hover:border-indigo-400 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:text-indigo-400" title="Ganti tema">
                        <svg x-show="$store.theme.isDark" x-cloak class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="!$store.theme.isDark" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>

                <section class="rounded-3xl border border-slate-200/90 bg-white/90 p-6 shadow-xl backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/90 sm:p-9">
                    {{ $slot }}
                </section>

                <p class="mt-6 text-center text-[11px] font-medium text-slate-500 dark:text-slate-400">
                    Sistem keamanan sertifikat digital Certiva
                </p>
            </main>
        </div>
    </body>
</html>
