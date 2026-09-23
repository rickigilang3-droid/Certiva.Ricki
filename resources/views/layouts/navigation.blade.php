<nav x-data="{ open: false }" class="bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800/80 sticky top-0 z-40 transition-colors duration-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-18">
            <div class="flex items-center gap-6">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::user()->isMahasiswa() ? route('student.certificates') : route('dashboard') }}" class="flex items-center gap-3.5 group">
                        <div class="w-12 h-12 sm:w-13 sm:h-13 rounded-2xl bg-white p-1.5 flex items-center justify-center shadow-md border border-slate-200 dark:border-slate-700/80 group-hover:scale-105 transition shrink-0">
                            <img src="{{ asset('images/logo.png') }}" alt="Certiva Logo" class="w-full h-full object-contain">
                        </div>
                        <div class="hidden sm:block">
                            <div class="font-extrabold text-lg tracking-tight text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span>Certiva</span>
                                <span class="text-[9px] px-1.5 py-0.5 rounded {{ Auth::user()->isMahasiswa() ? 'bg-cyan-50 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 border-cyan-200 dark:border-cyan-500/30' : 'bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-500/30' }} font-mono border font-bold">
                                    {{ Auth::user()->isMahasiswa() ? 'Mahasiswa' : 'Otoritas' }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-300 font-semibold -mt-0.5">Universitas Bina Sarana Informatika</div>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:flex items-center">
                    @if(Auth::user()->isMahasiswa())
                        <a href="{{ route('student.certificates') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('student.certificates*') ? 'bg-indigo-50 dark:bg-indigo-600/15 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30 shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                            {{ __('Sertifikat Saya') }}
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-600/15 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30 shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                            {{ __('Dashboard') }}
                        </a>
                        <a href="{{ route('certificates.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('certificates.index', 'certificates.show', 'certificates.create') ? 'bg-indigo-50 dark:bg-indigo-600/15 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30 shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                            {{ __('Sertifikat Digital') }}
                        </a>
                        <a href="{{ route('certificates.import.form') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('certificates.import*') ? 'bg-indigo-50 dark:bg-indigo-600/15 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30 shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                            {{ __('Import CSV') }}
                        </a>
                        <a href="{{ route('crypto-keys.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('crypto-keys.*') ? 'bg-indigo-50 dark:bg-indigo-600/15 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30 shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                            {{ __('Keamanan Kriptografi') }}
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right Nav Links & User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:gap-3">
                
                <!-- Theme Toggle Button (Light / Dark) -->
                <button type="button" @click="$store.theme.toggle()" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-800/70 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition" title="Ganti Tema Terang/Gelap">
                    <!-- Sun icon when in dark mode -->
                    <svg x-show="$store.theme.isDark" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Moon icon when in light mode -->
                    <svg x-show="!$store.theme.isDark" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Public Verification Portal link -->
                <a href="{{ route('verify.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-semibold transition">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                    <span>Portal Verifikasi ↗</span>
                </a>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/60 text-slate-800 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold focus:outline-none transition shadow-sm">
                            <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>{{ Auth::user()->name }}</div>
                            <svg class="w-3.5 h-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700/60 text-xs">
                            <div class="font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</div>
                            <div class="text-[11px] truncate text-slate-500 dark:text-slate-300">{{ Auth::user()->email }}</div>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')" class="text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            {{ __('Pengaturan Akun') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
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

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden gap-2">
                <button type="button" @click="$store.theme.toggle()" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    <svg x-show="$store.theme.isDark" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <svg x-show="!$store.theme.isDark" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                </button>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 pt-2 pb-4 space-y-1">
        @if(Auth::user()->isMahasiswa())
            <x-responsive-nav-link :href="route('student.certificates')" :active="request()->routeIs('student.certificates*')">
                {{ __('Sertifikat Saya') }}
            </x-responsive-nav-link>
        @else
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('certificates.index')" :active="request()->routeIs('certificates.index', 'certificates.show', 'certificates.create')">
                {{ __('Sertifikat Digital') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('certificates.import.form')" :active="request()->routeIs('certificates.import*')">
                {{ __('Import CSV') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('crypto-keys.index')" :active="request()->routeIs('crypto-keys.*')">
                {{ __('Keamanan Kriptografi') }}
            </x-responsive-nav-link>
        @endif
        <x-responsive-nav-link :href="route('verify.index')" target="_blank" class="text-emerald-600 dark:text-emerald-400">
            {{ __('Portal Verifikasi ↗') }}
        </x-responsive-nav-link>

        <div class="pt-4 pb-1 border-t border-slate-200 dark:border-slate-800">
            <div class="px-2">
                <div class="font-bold text-sm text-slate-900 dark:text-white">{{ Auth::user()->name }}</div>
                <div class="text-xs text-slate-600 dark:text-slate-300 font-medium">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Pengaturan Akun') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="text-rose-600 dark:text-rose-400">
                        {{ __('Keluar (Log Out)') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
