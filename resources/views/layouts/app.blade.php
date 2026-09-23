<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Certiva') }} - Otoritas Sertifikat Digital</title>

        <!-- Inline Theme Initialization (prevents flash of unstyled theme) -->
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
    <body class="font-sans antialiased bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 min-h-full transition-colors duration-200 selection:bg-indigo-500 selection:text-white">
        <!-- Ambient background lighting effects (visible in dark mode, subtle in light mode) -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
            <div class="absolute -top-40 left-1/4 w-[600px] h-[600px] bg-indigo-500/5 dark:bg-indigo-600/10 rounded-full blur-[140px]"></div>
            <div class="absolute top-1/3 -right-40 w-[500px] h-[500px] bg-blue-500/5 dark:bg-blue-600/8 rounded-full blur-[140px]"></div>
            <div class="absolute -bottom-40 left-1/3 w-[600px] h-[600px] bg-cyan-500/5 dark:bg-cyan-600/8 rounded-full blur-[140px]"></div>
        </div>

        <div class="min-h-screen relative z-10 flex flex-col justify-between">
            <div>
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="border-b border-slate-200 dark:border-slate-800/80 bg-white/70 dark:bg-slate-900/50 backdrop-blur-xl">
                        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>

            <!-- Footer -->
            <footer class="border-t border-slate-200 dark:border-slate-800/80 bg-white/70 dark:bg-slate-950/60 backdrop-blur py-6 mt-16 text-center text-xs text-slate-600 dark:text-slate-400">
                <div class="max-w-7xl mx-auto px-4 space-y-1">
                    <div>&copy; {{ date('Y') }} <strong class="text-slate-800 dark:text-slate-200">Certiva</strong> &bull; Platform Otoritas Sertifikasi & Ijazah Digital Kriptografis.</div>
                    <div class="font-mono text-[10px] text-slate-500 dark:text-slate-500">
                        RSA-2048 &bull; SHA-256 &bull; RSA-PSS Signature &bull; MySQL Enterprise Storage
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
