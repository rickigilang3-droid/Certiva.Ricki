<!-- Certiva Brand Logo Header -->
<div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-200 dark:border-slate-800/80 -mx-6 px-6">
    <a href="{{ Auth::user()->isMahasiswa() ? route('student.certificates') : route('dashboard') }}" class="flex items-center gap-3 group">
        <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 p-1 flex items-center justify-center shadow-md border border-slate-200 dark:border-slate-700/80 group-hover:scale-105 transition shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="Certiva Logo" class="w-full h-full object-contain">
        </div>
        <div>
            <div class="font-extrabold text-base tracking-tight text-slate-900 dark:text-white flex items-center gap-1.5">
                <span>Certiva</span>
                <span class="text-[9px] px-1.5 py-0.5 rounded font-mono font-bold {{ Auth::user()->isMahasiswa() ? 'bg-cyan-50 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-500/30' : 'bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30' }}">
                    {{ Auth::user()->isMahasiswa() ? 'Mahasiswa' : 'Otoritas' }}
                </span>
            </div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold -mt-0.5 truncate max-w-[140px]">
                Univ. BSI
            </div>
        </div>
    </a>
</div>

<!-- Navigation Links -->
<nav class="flex flex-1 flex-col justify-between pt-2">
    <div class="space-y-6">

        @if(Auth::user()->isAdmin())
            <!-- Admin Section 1: Manajemen Dokumen -->
            <div>
                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-400 px-3 mb-2 font-mono">
                    Manajemen Sertifikat
                </div>
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Daftar Sertifikat -->
                    <a href="{{ route('certificates.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('certificates.index', 'certificates.show') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('certificates.index', 'certificates.show') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Daftar Sertifikat</span>
                    </a>

                    <!-- Terbitkan Sertifikat -->
                    <a href="{{ route('certificates.create') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('certificates.create') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('certificates.create') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Terbitkan Baru</span>
                    </a>

                    <!-- Import Masal CSV -->
                    <a href="{{ route('certificates.import.form') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('certificates.import*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('certificates.import*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Import Masal CSV</span>
                    </a>
                </div>
            </div>

            <!-- Admin Section 2: Audit & Keamanan -->
            <div>
                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-400 px-3 mb-2 font-mono">
                    Keamanan & Audit
                </div>
                <div class="space-y-1">
                    <!-- Audit & Log Verifikasi -->
                    <a href="{{ route('verification-logs.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('verification-logs.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('verification-logs.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span>Audit & Log Scan</span>
                    </a>

                    <!-- Kunci Kriptografi RSA -->
                    <a href="{{ route('crypto-keys.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('crypto-keys.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('crypto-keys.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <span>Kunci RSA-2048</span>
                    </a>
                </div>
            </div>

        @else
            <!-- Mahasiswa Section: Layanan Mandiri -->
            <div>
                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-400 px-3 mb-2 font-mono">
                    Layanan Mandiri
                </div>
                <div class="space-y-1">
                    <!-- Sertifikat Saya -->
                    <a href="{{ route('student.certificates') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('student.certificates*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('student.certificates*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Sertifikat Saya</span>
                    </a>

                    <!-- Portofolio SKPI Publik -->
                    @if(Auth::user()->identifier)
                        <a href="{{ route('student.portfolio', Auth::user()->identifier) }}" 
                           target="_blank"
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-slate-800/60 hover:text-indigo-600 transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-cyan-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <span>Portofolio SKPI</span>
                            </div>
                            <span class="text-[10px] text-cyan-600 font-mono">↗</span>
                        </a>
                    @endif

                    <!-- Pengaturan Akun -->
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('profile.edit') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Pengaturan Akun</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- General External Section -->
        <div>
            <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-400 px-3 mb-2 font-mono">
                Verifikasi Publik
            </div>
            <div class="space-y-1">
                <a href="{{ route('verify.index') }}" 
                   target="_blank"
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-400 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Scan / Cek Dokumen</span>
                    </div>
                    <span class="text-[10px] font-mono text-emerald-500">↗</span>
                </a>
            </div>
        </div>

    </div>

    <!-- Sidebar Bottom: Theme Switcher & User Profile Card -->
    <div class="pt-6 border-t border-slate-200 dark:border-slate-800/80 -mx-6 px-6 space-y-3">
        
        <!-- Theme Toggle Bar -->
        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-100 dark:bg-slate-800/60 text-xs">
            <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Mode Tampilan</span>
            <button type="button" 
                    @click="$store.theme.toggle()" 
                    class="p-1.5 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 shadow-sm hover:scale-105 transition"
                    title="Ganti Tema Terang/Gelap">
                <svg x-show="$store.theme.isDark" x-cloak class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg x-show="!$store.theme.isDark" class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>
        </div>

        <!-- User Quick Card -->
        <div class="flex items-center justify-between p-2.5 rounded-2xl bg-white dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700/80 shadow-sm">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-500 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-bold text-slate-900 dark:text-white truncate">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono truncate">
                        {{ Auth::user()->identifier ?: Auth::user()->email }}
                    </div>
                </div>
            </div>

            <!-- Logout Form Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        title="Keluar (Log Out)"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>

    </div>
</nav>
