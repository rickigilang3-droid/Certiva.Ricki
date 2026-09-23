<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portofolio Kredensial Digital - {{ $studentName }} | Certiva Univ BSI</title>

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

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 min-h-full transition-colors duration-200 selection:bg-indigo-500 selection:text-white"
      x-data="{ 
          copiedToast: false,
          previewUrl: '',
          previewTitle: '',
          showPreviewModal: false,
          copyPortfolioLink() {
              navigator.clipboard.writeText(window.location.href).then(() => {
                  this.copiedToast = true;
                  setTimeout(() => { this.copiedToast = false; }, 2500);
              });
          },
          openPreview(url, title) {
              this.previewUrl = url;
              this.previewTitle = title;
              this.showPreviewModal = true;
          }
      }">

    <!-- Ambient background glows -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 left-1/4 w-[600px] h-[600px] bg-indigo-500/5 dark:bg-indigo-600/10 rounded-full blur-[140px]"></div>
        <div class="absolute top-1/3 -right-40 w-[500px] h-[500px] bg-blue-500/5 dark:bg-blue-600/8 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-40 left-1/3 w-[600px] h-[600px] bg-cyan-500/5 dark:bg-cyan-600/8 rounded-full blur-[140px]"></div>
    </div>

    <!-- Toast Notification -->
    <div x-show="copiedToast" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-6 right-6 z-50 bg-slate-900 text-white dark:bg-white dark:text-slate-900 px-4 py-2.5 rounded-xl shadow-2xl flex items-center gap-2.5 text-xs font-bold border border-slate-700/40">
        <svg class="w-4 h-4 text-emerald-400 dark:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        <span>Tautan Portofolio Kredensial berhasil disalin!</span>
    </div>

    <!-- Header Navigation -->
    <header class="border-b border-slate-200 dark:border-slate-800/80 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 p-1 flex items-center justify-center shadow-md border border-slate-200 dark:border-slate-700/80 group-hover:scale-105 transition shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Certiva Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <div class="font-extrabold text-base tracking-tight text-slate-900 dark:text-white flex items-center gap-1.5">
                        <span>Certiva</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded font-mono font-bold bg-cyan-50 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-500/30">
                            Portofolio SKPI
                        </span>
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold -mt-0.5">
                        Universitas Bina Sarana Informatika
                    </div>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <!-- Theme Toggle -->
                <button type="button" 
                        @click="$store.theme.toggle()" 
                        class="p-2 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-800/70 text-slate-600 dark:text-slate-300 hover:text-indigo-600 transition" 
                        title="Ganti Tema">
                    <svg x-show="$store.theme.isDark" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <svg x-show="!$store.theme.isDark" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                </button>

                <a href="{{ route('verify.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                    <span>Portal Verifikasi ↗</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8 relative z-10">

        <!-- Student Hero Banner Card -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-900 via-slate-900 to-[#0B0F19] text-white p-6 sm:p-8 border border-indigo-500/30 shadow-2xl">
            <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
                <div class="flex items-center gap-5">
                    <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-indigo-500 via-blue-500 to-cyan-400 text-white flex items-center justify-center font-black text-2xl sm:text-3xl shadow-xl shadow-indigo-500/30 shrink-0">
                        {{ strtoupper(substr($studentName, 0, 1)) }}
                    </div>
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Mahasiswa Terverifikasi
                            </span>
                            <span class="text-xs text-slate-400">&bull; Universitas Bina Sarana Informatika</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                            {{ $studentName }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-300 font-mono">
                            @if($studentNim)
                                <span>NIM: <strong class="text-white">{{ $studentNim }}</strong></span>
                                <span>&bull;</span>
                            @endif
                            <span>Status: <strong class="text-emerald-400 font-sans">Kredensial Aktif</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Right Actions & Stat Badge -->
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <div class="px-5 py-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10 text-center">
                        <div class="text-2xl font-black text-cyan-300 font-mono">{{ $certificates->count() }}</div>
                        <div class="text-[10px] uppercase font-bold text-slate-300 tracking-wider">Sertifikat Resmi</div>
                    </div>

                    <button type="button" 
                            @click="copyPortfolioLink()" 
                            class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-gradient-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white text-xs font-bold shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Salin Link Portofolio</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Section Title -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight">
                    Kredensial & Ijazah Terverifikasi (SKPI Digital)
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Seluruh dokumen di bawah ini ditandatangani secara kriptografis menggunakan algoritma RSA-PSS 2048-bit & SHA-256 oleh Universitas BSI.
                </p>
            </div>
            <div class="text-xs font-bold text-slate-500 dark:text-slate-400 font-mono">
                Total: {{ $certificates->count() }} Dokumen
            </div>
        </div>

        <!-- Certificates Grid -->
        @if($certificates->isEmpty())
            <div class="p-12 text-center rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-2">
                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Belum ada sertifikat publik yang aktif untuk mahasiswa ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($certificates as $cert)
                    @php
                        $certDate = \Carbon\Carbon::parse($cert->issued_date);
                        $linkedInUrl = 'https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME'
                            . '&name=' . urlencode($cert->title)
                            . '&organizationName=' . urlencode('Universitas Bina Sarana Informatika')
                            . '&issueYear=' . $certDate->year
                            . '&issueMonth=' . $certDate->month
                            . '&certUrl=' . urlencode(route('verify.show', $cert->certificate_number))
                            . '&certId=' . urlencode($cert->certificate_number);
                    @endphp

                    <div class="rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl hover:border-indigo-400 dark:hover:border-indigo-500/50 transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                        
                        <div class="p-6 space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                    {{ $cert->certificate_number }}
                                </span>

                                <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Sah & Terverifikasi
                                </span>
                            </div>

                            <div class="space-y-1">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-cyan-400">
                                    {{ $cert->category ?? 'Ijazah Kelulusan' }}
                                </div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ $cert->title }}
                                </h3>
                                @if($cert->department)
                                    <p class="text-xs text-slate-600 dark:text-slate-300 font-medium">
                                        {{ $cert->department }}
                                    </p>
                                @endif
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800/60 text-xs space-y-1.5">
                                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                    <span>Penerbit:</span>
                                    <strong class="text-slate-800 dark:text-slate-200">Universitas BSI</strong>
                                </div>
                                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                    <span>Tanggal Terbit:</span>
                                    <strong class="text-slate-800 dark:text-slate-200">{{ $certDate->translatedFormat('d F Y') }}</strong>
                                </div>
                                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                    <span>Penandatangan:</span>
                                    <strong class="text-slate-800 dark:text-slate-200 truncate max-w-[160px]">{{ $cert->signatory_name }}</strong>
                                </div>
                                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400 font-mono text-[10px]">
                                    <span>Segel Kripto:</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">RSA-PSS (2048-bit)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="p-4 bg-slate-50/80 dark:bg-slate-950/50 border-t border-slate-100 dark:border-slate-800/80 space-y-2">
                            <div class="flex items-center gap-2">
                                <!-- Verify Link -->
                                <a href="{{ route('verify.show', $cert->certificate_number) }}" 
                                   target="_blank"
                                   class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-emerald-600 transition shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    <span>Verifikasi Asli</span>
                                </a>

                                <!-- Download PDF -->
                                <a href="{{ route('verify.download', $cert->certificate_number) }}" 
                                   class="inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>PDF</span>
                                </a>
                            </div>

                            <!-- Add to LinkedIn Button -->
                            <a href="{{ $linkedInUrl }}" 
                               target="_blank"
                               class="w-full inline-flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-[#0A66C2] hover:bg-[#004182] text-white text-xs font-bold transition shadow-sm">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                    <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 8.76a1.46 1.46 0 1 0 0-2.92 1.46 1.46 0 0 0 0 2.92m1.39 9.74v-8.37H5.07v8.37h2.78z"/>
                                </svg>
                                <span>Tambah ke LinkedIn</span>
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800/80 bg-white/70 dark:bg-slate-950/60 backdrop-blur py-6 mt-20 text-center text-xs text-slate-600 dark:text-slate-400">
        <div class="max-w-6xl mx-auto px-4 space-y-1">
            <div>&copy; {{ date('Y') }} <strong class="text-slate-800 dark:text-slate-200">Certiva</strong> &bull; Halaman Portofolio Kredensial Resmi Universitas Bina Sarana Informatika.</div>
            <div class="font-mono text-[10px] text-slate-500 dark:text-slate-500">
                Otentikasi Kriptografis RSA-PSS &bull; SHA-256 Hash Digest &bull; QR Code Terverifikasi
            </div>
        </div>
    </footer>

</body>
</html>

