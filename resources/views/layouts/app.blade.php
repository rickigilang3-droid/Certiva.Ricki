<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Certiva') }} - Platform Otoritas Sertifikasi Kriptografis</title>

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
    <body class="font-sans antialiased bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 min-h-full transition-colors duration-200 selection:bg-indigo-500 selection:text-white"
          x-data="{ sidebarOpen: false }">

        <!-- Ambient background lighting effects -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
            <div class="absolute -top-40 left-1/4 w-[600px] h-[600px] bg-indigo-500/5 dark:bg-indigo-600/10 rounded-full blur-[140px]"></div>
            <div class="absolute top-1/3 -right-40 w-[500px] h-[500px] bg-blue-500/5 dark:bg-blue-600/8 rounded-full blur-[140px]"></div>
            <div class="absolute -bottom-40 left-1/3 w-[600px] h-[600px] bg-cyan-500/5 dark:bg-cyan-600/8 rounded-full blur-[140px]"></div>
        </div>

        <!-- ================= MOBILE SIDEBAR DRAWER ================= -->
        <div x-show="sidebarOpen" 
             x-cloak
             class="relative z-50 lg:hidden" 
             role="dialog" 
             aria-modal="true">
            
            <!-- Backdrop -->
            <div x-show="sidebarOpen"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>

            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-in-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="relative mr-16 flex w-full max-w-xs flex-1">
                    
                    <!-- Close Button -->
                    <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                        <button type="button" @click="sidebarOpen = false" class="-m-2.5 p-2.5 text-white">
                            <span class="sr-only">Tutup sidebar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Sidebar Content -->
                    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-slate-900 px-6 pb-4 border-r border-slate-200 dark:border-slate-800">
                        @include('layouts.sidebar-nav')
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= DESKTOP STATIC SIDEBAR ================= -->
        <aside class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:w-64 lg:flex-col border-r border-slate-200 dark:border-slate-800/80 bg-white/95 dark:bg-slate-900/90 backdrop-blur-xl">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto px-6 pb-4">
                @include('layouts.sidebar-nav')
            </div>
        </aside>

        <!-- ================= MAIN CONTENT WRAPPER ================= -->
        <div class="lg:pl-64 flex flex-col min-h-screen relative z-10">
            
            <!-- Sticky Topbar Header -->
            <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-x-4 border-b border-slate-200 dark:border-slate-800/80 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl px-4 sm:px-6 lg:px-8">
                <!-- Hamburger for mobile -->
                <button type="button" 
                        @click="sidebarOpen = true" 
                        class="-m-2.5 p-2.5 text-slate-700 dark:text-slate-200 lg:hidden hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                    <span class="sr-only">Buka menu sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Separator on mobile -->
                <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 lg:hidden" aria-hidden="true"></div>

                <!-- Breadcrumb or Context Indicator -->
                <div class="flex flex-1 items-center gap-x-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 font-bold text-slate-700 dark:text-slate-300">
                        @if(Auth::user()->isAdmin())
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Sistem Otoritas Aktif (RSA-PSS)</span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
                            <span>Portal Mahasiswa Terverifikasi</span>
                        @endif
                    </span>
                    <span class="hidden sm:inline text-slate-400">&bull;</span>
                    <span class="hidden sm:inline text-slate-500 dark:text-slate-400 font-medium">Universitas Bina Sarana Informatika</span>
                </div>

                <!-- Right Topbar Controls -->
                <div class="flex items-center gap-x-3">
                    
                    <!-- Quick Link: Public Verify -->
                    <a href="{{ route('verify.index') }}" 
                       target="_blank" 
                       class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-semibold transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Portal Verifikasi ↗</span>
                    </a>

                    <!-- Theme Toggle Button -->
                    <button type="button" 
                            @click="$store.theme.toggle()" 
                            class="p-2 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-800/70 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition" 
                            title="Ganti Tema Terang/Gelap">
                        <svg x-show="$store.theme.isDark" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="!$store.theme.isDark" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <!-- User Dropdown Menu -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 p-1 sm:px-2.5 sm:py-1.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/60 text-slate-800 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold focus:outline-none transition shadow-sm">
                                <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-indigo-600 to-cyan-500 text-white flex items-center justify-center font-black text-xs shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden md:block truncate max-w-[130px]">{{ Auth::user()->name }}</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700/60 text-xs">
                                <div class="font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</div>
                                <div class="text-[11px] truncate text-slate-500 dark:text-slate-300">{{ Auth::user()->email }}</div>
                                <div class="mt-1">
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ Auth::user()->isAdmin() ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-cyan-300' : 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/50 dark:text-cyan-300' }}">
                                        {{ Auth::user()->isAdmin() ? 'Admin Kampus' : 'Mahasiswa' }}
                                    </span>
                                </div>
                            </div>

                            @if(Auth::user()->isMahasiswa() && Auth::user()->identifier)
                                <x-dropdown-link :href="route('student.portfolio', Auth::user()->identifier)" target="_blank" class="text-xs text-indigo-600 dark:text-cyan-400 font-semibold hover:bg-indigo-50 dark:hover:bg-slate-700/50">
                                    {{ __('Lihat Portofolio SKPI ↗') }}
                                </x-dropdown-link>
                            @endif

                            <x-dropdown-link :href="route('profile.edit')" class="text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                {{ __('Pengaturan Akun') }}
                            </x-dropdown-link>

                            <!-- Authentication Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                    {{ __('Keluar (Log Out)') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            <!-- Page Heading (Optional Sub-Header) -->
            @isset($header)
                <div class="border-b border-slate-200 dark:border-slate-800/80 bg-white/70 dark:bg-slate-900/50 backdrop-blur-xl">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <!-- Page Main Content Slot -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Modern Clean Footer -->
            <footer class="border-t border-slate-200 dark:border-slate-800/80 bg-white/70 dark:bg-slate-950/60 backdrop-blur py-5 mt-16 text-center text-xs text-slate-600 dark:text-slate-400">
                <div class="max-w-7xl mx-auto px-4 space-y-1">
                    <div>&copy; {{ date('Y') }} <strong class="text-slate-800 dark:text-slate-200">Certiva</strong> &bull; Platform Otoritas Sertifikasi & Ijazah Digital Kriptografis Resmi.</div>
                    <div class="font-mono text-[10px] text-slate-500 dark:text-slate-500">
                        RSA-2048 &bull; SHA-256 &bull; RSA-PSS Signature &bull; MySQL Enterprise Storage
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
