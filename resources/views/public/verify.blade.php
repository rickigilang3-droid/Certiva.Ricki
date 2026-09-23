<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certiva | Portal Verifikasi Publik Sertifikat Digital Kriptografis</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

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

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body x-data="{
    activeTab: '{{ $errors->has('pdf_file') ? 'pdf' : 'search' }}',
    scanQrModal: false,
    html5QrCode: null,
    scannerError: '',
    pdfFileName: '',
    pdfFileSize: '',
    pdfDragOver: false,
    handlePdfSelect(e) {
        const file = e.target.files[0];
        if (file) {
            this.pdfFileName = file.name;
            this.pdfFileSize = (file.size / 1024).toFixed(1) + ' KB';
        }
    },
    handlePdfDrop(e) {
        this.pdfDragOver = false;
        const file = e.dataTransfer.files[0];
        if (file) {
            this.$refs.pdfFileInput.files = e.dataTransfer.files;
            this.pdfFileName = file.name;
            this.pdfFileSize = (file.size / 1024).toFixed(1) + ' KB';
        }
    },
    startScanner() {
        this.scannerError = '';
        this.scanQrModal = true;
        this.$nextTick(() => {
            if (!this.html5QrCode) {
                this.html5QrCode = new Html5Qrcode('qr-reader');
            }
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
            this.html5QrCode.start(
                { facingMode: 'environment' },
                config,
                (decodedText) => {
                    this.onScanSuccess(decodedText);
                },
                () => {}
            ).catch(err => {
                this.scannerError = 'Gagal mengakses kamera: ' + (err.message || err);
            });
        });
    },
    stopScanner() {
        if (this.html5QrCode && this.html5QrCode.isScanning) {
            this.html5QrCode.stop().then(() => {
                this.scanQrModal = false;
            }).catch(() => {
                this.scanQrModal = false;
            });
        } else {
            this.scanQrModal = false;
        }
    },
    onScanSuccess(decodedText) {
        this.stopScanner();
        let certNumber = decodedText.trim();
        const match = certNumber.match(/CERT-[A-Za-z0-9_-]+/i);
        if (match) {
            certNumber = match[0];
        } else if (certNumber.includes('/verify/')) {
            const parts = certNumber.split('/verify/');
            certNumber = parts[parts.length - 1].replace(/[^A-Za-z0-9_-]/g, '');
        }
        window.location.href = '{{ url('/verify') }}/' + encodeURIComponent(certNumber);
    }
}" class="min-h-full font-sans antialiased bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-slate-100 flex flex-col selection:bg-indigo-500 selection:text-white transition-colors duration-200">
    <!-- Ambient background lighting effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 left-1/4 w-[600px] h-[600px] bg-indigo-500/5 dark:bg-indigo-600/10 rounded-full blur-[140px]"></div>
        <div class="absolute top-1/3 -right-40 w-[500px] h-[500px] bg-blue-500/5 dark:bg-blue-600/8 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-40 left-1/3 w-[600px] h-[600px] bg-cyan-500/5 dark:bg-cyan-600/8 rounded-full blur-[140px]"></div>
    </div>

    <!-- Header -->
    <header class="border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur sticky top-0 z-30 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3.5 group">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white p-1.5 flex items-center justify-center shadow-md border border-slate-200 dark:border-slate-700/80 group-hover:scale-105 transition shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Certiva Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-black text-xl sm:text-2xl tracking-tight text-slate-900 dark:text-white">Certiva</span>
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-800">RSA-PSS</span>
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-300 font-semibold">Universitas Bina Sarana Informatika</div>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <!-- Theme Toggle Button -->
                <button type="button" @click="$store.theme.toggle()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 shadow-sm transition" title="Ganti Tema Terang/Gelap">
                    <!-- Sun Icon when dark -->
                    <svg x-show="$store.theme.isDark" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Moon Icon when light -->
                    <svg x-show="!$store.theme.isDark" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span class="text-xs font-semibold hidden sm:inline" x-text="$store.theme.isDark ? 'Mode Terang' : 'Mode Gelap'"></span>
                </button>

                <a href="{{ route('verify.index') }}" class="text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-2 rounded-lg transition">
                    Verifikasi
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                        <span>Dashboard Kampus</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-slate-900 hover:bg-slate-800 text-white dark:bg-slate-800 dark:hover:bg-slate-700 shadow-sm transition">
                        <span>Masuk Kampus / Penerbit</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-10 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            <!-- Hero Search Section -->
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-semibold border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Sistem Validasi Kriptografi Real-time</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Verifikasi Keaslian Sertifikat & Ijazah Digital
                </h1>
                <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300">
                    Validasi instan anti-pemalsuan dengan tanda tangan digital <strong class="text-slate-900 dark:text-white">RSA-PSS & SHA-256</strong>. Memastikan nama peserta, tanggal, dan ijazah tidak dapat diedit atau dimanipulasi.
                </p>

                <!-- Error Alerts -->
                @if($errors->any())
                    <div class="max-w-xl mx-auto p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-800 dark:text-rose-200 text-left space-y-1 animate-fade-in-down shadow-sm">
                        <div class="font-bold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Gagal memvalidasi:</span>
                        </div>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Verification Mode Tabs -->
                <div class="inline-flex p-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm gap-1 max-w-md mx-auto">
                    <button type="button" 
                            @click="activeTab = 'search'"
                            :class="activeTab === 'search' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                            class="px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Nomor / Pindai QR</span>
                    </button>
                    <button type="button" 
                            @click="activeTab = 'pdf'"
                            :class="activeTab === 'pdf' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                            class="px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <span>Unggah Berkas PDF</span>
                    </button>
                </div>

                <!-- Tab 1: Search Form & QR Camera Scanner -->
                <div x-show="activeTab === 'search'" x-transition class="space-y-4">
                    <form action="{{ route('verify.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 max-w-xl mx-auto">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Masukkan Nomor Sertifikat (cth: CERT-2026-CAMPUS-001)..." 
                                    required
                                    class="w-full pl-10 pr-4 py-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium placeholder:text-slate-400 dark:placeholder:text-slate-500 transition" />
                        </div>
                        <button type="submit" class="px-6 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                            <span>Verifikasi</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                        <button type="button" @click="startScanner()" class="px-4 py-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200 font-semibold text-xs shadow-sm transition flex items-center justify-center gap-2" title="Pindai QR Code menggunakan kamera perangkat">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span class="hidden sm:inline">Pindai QR</span>
                        </button>
                    </form>
                </div>

                <!-- Tab 2: PDF Drag-and-Drop Form -->
                <div x-show="activeTab === 'pdf'" x-transition class="max-w-xl mx-auto">
                    <form action="{{ route('verify.pdf.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="relative border-2 border-dashed rounded-3xl p-6 text-center transition-all duration-200 cursor-pointer"
                             :class="pdfDragOver ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 hover:border-indigo-400 dark:hover:border-indigo-500/50'"
                             @dragover.prevent="pdfDragOver = true"
                             @dragleave.prevent="pdfDragOver = false"
                             @drop.prevent="handlePdfDrop($event)"
                             @click="$refs.pdfFileInput.click()">
                            
                            <input type="file" name="pdf_file" x-ref="pdfFileInput" @change="handlePdfSelect($event)" accept=".pdf" class="hidden" required />

                            <div class="space-y-2">
                                <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 dark:bg-indigo-500/15 border border-indigo-200 dark:border-indigo-500/30 text-indigo-600 dark:text-cyan-400 flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>

                                <template x-if="!pdfFileName">
                                    <div>
                                        <p class="text-xs font-bold text-slate-900 dark:text-white">
                                            Seret berkas PDF ijazah ke sini, atau <span class="text-indigo-600 dark:text-cyan-400 underline">pilih dari perangkat</span>
                                        </p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Sistem akan otomatis membedah nomor sertifikat & memverifikasi integritasnya</p>
                                    </div>
                                </template>

                                <template x-if="pdfFileName">
                                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl border border-indigo-200 dark:border-indigo-500/40 max-w-sm mx-auto flex items-center gap-2.5 text-left">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px]">
                                            PDF
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-bold text-xs text-slate-900 dark:text-white truncate" x-text="pdfFileName"></div>
                                            <div class="text-[10px] text-slate-500 font-mono" x-text="pdfFileSize"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Verifikasi Dokumen PDF Ini</span>
                        </button>
                    </form>
                </div>

                <!-- Sample demo queries -->
                <div class="flex flex-wrap items-center justify-center gap-2 pt-1 text-xs text-slate-600 dark:text-slate-300">
                    <span class="font-medium">Coba sertifikat contoh:</span>
                    <a href="{{ route('verify.show', 'CERT-2026-CAMPUS-001') }}" class="px-2.5 py-1 rounded-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-indigo-600 dark:text-indigo-300 hover:border-indigo-500 font-mono font-semibold transition">
                        CERT-2026-CAMPUS-001 (Sah)
                    </a>
                </div>
            </div>

            <!-- Verification Results Display -->
            @if(isset($verificationResult))
                @if($verificationResult['status'] === 'not_found')
                    <!-- Not Found -->
                    <div class="max-w-3xl mx-auto rounded-2xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/50 dark:bg-rose-950/20 p-6 sm:p-8 text-center space-y-4 shadow-sm">
                        <div class="w-14 h-14 rounded-full bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-rose-900 dark:text-rose-200">Sertifikat Tidak Ditemukan</h2>
                        <p class="text-sm text-rose-700 dark:text-rose-300 max-w-lg mx-auto">
                            Nomor sertifikat <strong class="font-mono bg-rose-100 dark:bg-rose-900/60 px-2 py-0.5 rounded">{{ $query }}</strong> tidak terdaftar dalam pangkalan data terverifikasi Universitas Bina Sarana Informatika. Mohon periksa kembali nomor sertifikat atau hubungi pihak kampus.
                        </p>
                    </div>

                @elseif($certificate)
                    <!-- Found: Authentic, Revoked, or Tampered -->
                    <div class="max-w-5xl mx-auto space-y-6">
                        
                        <!-- Status Banner -->
                        @if($verificationResult['status'] === 'authentic')
                            <div class="rounded-2xl border border-emerald-300 dark:border-emerald-800 bg-gradient-to-r from-emerald-500/10 via-emerald-500/5 to-transparent p-6 flex flex-col sm:flex-row items-center gap-5 shadow-sm">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-md">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="space-y-1 text-center sm:text-left flex-1">
                                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-600 text-white">
                                            Terverifikasi Sah & Asli
                                        </span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                                            Audit ID: {{ substr(md5($certificate->hash_sha256), 0, 12) }}
                                        </span>
                                    </div>
                                    <h2 class="text-xl font-bold text-emerald-950 dark:text-emerald-100">
                                        Sertifikat Resmi & Sah secara Kriptografis
                                    </h2>
                                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                                        Integritas data terbukti secara matematis. Tanda tangan RSA-PSS cocok dengan kunci publik universitas, dan data tidak pernah dimodifikasi sejak penerbitan.
                                    </p>
                                </div>
                                <div class="shrink-0 flex gap-2">
                                    <a href="{{ route('verify.download', $certificate->certificate_number) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Unduh PDF Asli</span>
                                    </a>
                                </div>
                            </div>

                        @elseif($verificationResult['status'] === 'revoked')
                            <!-- Revoked Certificate Alert -->
                            <div class="rounded-2xl border border-amber-300 dark:border-amber-800 bg-amber-500/10 p-6 flex flex-col sm:flex-row items-start gap-5 shadow-sm">
                                <div class="w-14 h-14 rounded-2xl bg-amber-600 text-white flex items-center justify-center shrink-0 shadow-md">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                </div>
                                <div class="space-y-2 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-600 text-white">
                                            Status: DICABUT (REVOKED)
                                        </span>
                                        <span class="text-xs text-amber-700 dark:text-amber-400">
                                            Dicabut pada: {{ $certificate->revoked_at ? $certificate->revoked_at->translatedFormat('d F Y H:i') : 'N/A' }}
                                        </span>
                                    </div>
                                    <h2 class="text-xl font-bold text-amber-950 dark:text-amber-200">
                                        Sertifikat Ini Telah Resmi Dicabut oleh Institusi
                                    </h2>
                                    <div class="p-3 bg-white/80 dark:bg-slate-900/80 rounded-xl border border-amber-200 dark:border-amber-900/40 text-xs sm:text-sm text-slate-800 dark:text-slate-200">
                                        <strong>Alasan Pencabutan:</strong> {{ $certificate->revocation_reason ?? 'Pembatalan administratif atau ketidaksesuaian berkas akademik.' }}
                                    </div>
                                </div>
                            </div>

                        @else
                            <!-- Tampered Warning -->
                            <div class="rounded-2xl border border-rose-400 dark:border-rose-800 bg-rose-500/10 p-6 flex flex-col sm:flex-row items-start gap-5 shadow-sm">
                                <div class="w-14 h-14 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-md">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div class="space-y-2 flex-1">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-600 text-white">
                                        Peringatan: Integritas Gagal
                                    </span>
                                    <h2 class="text-xl font-bold text-rose-950 dark:text-rose-200">
                                        Sertifikat Terindikasi Dimanipulasi / Dipalsukan
                                    </h2>
                                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                                        Tanda tangan kriptografis RSA-PSS tidak cocok dengan data sertifikat. Dokumen kemungkinan telah diedit secara ilegal (nama penerima, tanggal, atau nilai telah diganti).
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            
                            <!-- Left 2 Cols: Official Certificate Record -->
                            <div class="lg:col-span-2 space-y-6">
                                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-6">
                                    <div class="border-b border-slate-100 dark:border-slate-800 pb-4 flex items-center justify-between">
                                        <div>
                                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Data Resmi Pangkalan Data Kampus</span>
                                            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $certificate->title }}</h3>
                                            <div class="text-xs text-slate-600 dark:text-slate-300 font-medium mt-0.5">{{ $certificate->institution_name }} &bull; {{ $certificate->department }}</div>
                                        </div>
                                        <span class="font-mono text-xs px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            {{ $certificate->certificate_number }}
                                        </span>
                                    </div>

                                    <!-- Recipient Details -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800/60">
                                            <div class="text-xs font-bold text-slate-600 dark:text-slate-300">Nama Lengkap Penerima</div>
                                            <div class="text-lg font-bold text-slate-900 dark:text-white mt-0.5">{{ $certificate->recipient_name }}</div>
                                        </div>
                                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800/60">
                                            <div class="text-xs font-bold text-slate-600 dark:text-slate-300">Nomor Induk Mahasiswa (NIM)</div>
                                            <div class="text-lg font-bold font-mono text-slate-900 dark:text-white mt-0.5">{{ $certificate->recipient_identifier ?? '-' }}</div>
                                        </div>
                                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800/60">
                                            <div class="text-xs font-bold text-slate-600 dark:text-slate-300">Tanggal Terbit Resmi</div>
                                            <div class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">
                                                {{ \Carbon\Carbon::parse($certificate->issued_date)->translatedFormat('d F Y') }}
                                            </div>
                                        </div>
                                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800/60">
                                            <div class="text-xs font-bold text-slate-600 dark:text-slate-300">Penandatangan Resmi</div>
                                            <div class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">{{ $certificate->signatory_name }}</div>
                                            <div class="text-xs text-slate-600 dark:text-slate-400 font-medium">{{ $certificate->signatory_title }}</div>
                                        </div>
                                    </div>

                                    @if($certificate->description)
                                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/60 text-xs text-slate-700 dark:text-slate-200 italic font-medium">
                                            "{{ $certificate->description }}"
                                        </div>
                                    @endif

                                    <!-- Action Links -->
                                    <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                                        <a href="{{ route('verify.download', $certificate->certificate_number) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span>Unduh Dokumen PDF Resmi</span>
                                        </a>

                                        <a href="{{ route('verify.proof', $certificate->certificate_number) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-mono text-slate-700 dark:text-slate-200 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                            <span>Lihat Raw Proof (JSON)</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Col: Cryptographic Security Panel -->
                            <div class="space-y-6">
                                <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-2xl p-6 shadow-xl border border-indigo-900/50 space-y-5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <h4 class="font-bold text-sm uppercase tracking-wider text-white">Keamanan Kriptografi</h4>
                                        </div>
                                        <span class="text-xs px-2 py-0.5 rounded bg-indigo-500/30 text-indigo-300 font-mono">RFC 8017</span>
                                    </div>

                                    <!-- The Required Cryptographic Security Specification Block -->
                                    <div class="space-y-3.5 text-xs">
                                        <div class="flex items-center justify-between py-1.5 border-b border-white/10">
                                            <span class="text-slate-300 font-medium">Algoritma Kunci</span>
                                            <span class="font-mono font-bold text-white">{{ $activeKey->algorithm ?? 'RSA-2048' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between py-1.5 border-b border-white/10">
                                            <span class="text-slate-300 font-medium">Fungsi Hash</span>
                                            <span class="font-mono font-bold text-white">{{ $activeKey->hash_algorithm ?? 'SHA-256' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between py-1.5 border-b border-white/10">
                                            <span class="text-slate-300 font-medium">Skema Tanda Tangan</span>
                                            <span class="font-mono font-bold text-indigo-300">{{ $activeKey->signature_scheme ?? 'RSA-PSS' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between py-1.5 border-b border-white/10">
                                            <span class="text-slate-300 font-medium">Status Kunci</span>
                                            <span class="font-semibold text-emerald-400 flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                                <span>{{ ucfirst($activeKey->status ?? 'Active') }}</span>
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between py-1.5 border-b border-white/10">
                                            <span class="text-slate-300 font-medium">Pejabat Otoritas</span>
                                            <span class="font-bold text-white text-sm">
                                                Ricki Gilang Saputra
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Cryptographic Digest & Fingerprint -->
                                    <div class="space-y-2 pt-2">
                                        <div>
                                            <span class="text-[10px] text-slate-300 uppercase tracking-wider font-semibold block">SHA-256 Digest (Canonical Data):</span>
                                            <div class="font-mono text-[10px] bg-black/40 p-2 rounded border border-white/5 break-all text-emerald-400">
                                                {{ $certificate->hash_sha256 }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-300 uppercase tracking-wider font-semibold block">Key Fingerprint (SHA-256):</span>
                                            <div class="font-mono text-[10px] bg-black/40 p-2 rounded border border-white/5 break-all text-indigo-300">
                                                {{ $activeKey->fingerprint ?? 'e455af90e11e45398600344a605a6e9a32ac4b28cdd779a43a719efbff610adb' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-2 text-[11px] text-slate-300 leading-relaxed font-normal">
                                        Setiap karakter pada ijazah terikat matematis dengan tanda tangan RSA-PSS 2048-bit. Manipulasi nama atau tanggal sekecil apapun akan membatalkan validasi.
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Visual Cryptographic Verification Pipeline Card -->
                        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                                <div>
                                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Arsitektur Keamanan Integritas Data</span>
                                    <h4 class="text-lg font-extrabold text-slate-900 dark:text-white">Alur Pipa Verifikasi Kriptografis (End-to-End Pipeline)</h4>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($verificationResult['isHashValid'] && $verificationResult['isSignatureValid'])
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span>Status: MATCH (VALID)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                            <span>Status: DIFFERENT (TAMPERED)</span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Interactive Pipeline Graphic -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Phase 1: Create Certificate -->
                                <div class="p-5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 space-y-4">
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-[11px] font-bold">1</span>
                                        <span>Fase Penerbitan (Create Certificate)</span>
                                    </div>

                                     <div class="space-y-3 text-xs">
                                        <div class="p-3 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">1. Generate Certificate Data</div>
                                                <div class="text-[11px] text-slate-600 dark:text-slate-300 font-medium">Penyusunan canonical payload deterministik</div>
                                            </div>
                                            <span class="text-emerald-600 dark:text-emerald-400 font-mono text-[11px] font-bold">✓ Canonical</span>
                                        </div>

                                        <div class="flex justify-center text-slate-400">↓</div>

                                        <div class="p-3 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">2. SHA-256 Hashing</div>
                                                <div class="text-[11px] text-slate-600 dark:text-slate-300 font-medium">Kompilasi digest matematis 256-bit</div>
                                            </div>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-mono text-[11px] font-bold">SHA-256</span>
                                        </div>

                                        <div class="flex justify-center text-slate-400">↓</div>

                                        <div class="p-3 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">3. Store Hash & RSA-PSS Sign</div>
                                                <div class="text-[11px] text-slate-600 dark:text-slate-300 font-medium">Disimpan ke database & disegel private key RSA-2048</div>
                                            </div>
                                            <span class="text-emerald-600 dark:text-emerald-400 font-mono text-[11px] font-bold">✓ Sealed</span>
                                        </div>

                                        <div class="flex justify-center text-slate-400">↓</div>

                                        <div class="p-3 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">4. Generate PDF & QR Code</div>
                                                <div class="text-[11px] text-slate-600 dark:text-slate-300 font-medium">Dokumen A4 anti-pemalsuan dengan QR portal</div>
                                            </div>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-mono text-[11px] font-bold">PDF Ready</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Phase 2: Live Verification -->
                                <div class="p-5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 space-y-4">
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                        <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-[11px] font-bold">2</span>
                                        <span>Fase Pembuktian (Live Verification)</span>
                                    </div>

                                    <div class="space-y-3 text-xs">
                                        <div class="p-3 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                            <div class="font-bold text-slate-900 dark:text-white">1. Scan QR / Query No. Registrasi</div>
                                            <div class="text-[11px] text-slate-600 dark:text-slate-300 font-medium">Pencarian rekaman resmi di pangkalan data kampus</div>
                                            <div class="font-mono text-[10px] text-indigo-600 dark:text-indigo-400 mt-1 font-semibold">{{ $certificate->certificate_number }}</div>
                                        </div>

                                        <div class="flex justify-center text-slate-400">↓</div>

                                        <div class="p-3 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                            <div class="font-bold text-slate-900 dark:text-white">2. Recalculate SHA-256 Hash</div>
                                            <div class="text-[11px] text-slate-600 dark:text-slate-300 font-medium">Menghitung ulang digest dari atribut kanonikal saat ini:</div>
                                            <div class="font-mono text-[9px] bg-slate-100 dark:bg-slate-950 p-1.5 rounded mt-1 break-all text-slate-700 dark:text-slate-200">
                                                {{ $verificationResult['recomputedHash'] }}
                                            </div>
                                        </div>

                                        <div class="flex justify-center text-slate-400">↓</div>

                                        <div class="p-3 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                            <div class="font-bold text-slate-900 dark:text-white">3. Compare (Stored Hash vs Recalculated Hash)</div>
                                            <div class="text-[11px] text-slate-600 dark:text-slate-300 font-medium">Validasi hash_equals() & tanda tangan RSA-PSS dengan Kunci Publik</div>
                                        </div>

                                        <div class="flex justify-center text-slate-400">↓</div>

                                        <!-- Decision Branch -->
                                        @if($verificationResult['isHashValid'] && $verificationResult['isSignatureValid'])
                                            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border-2 border-emerald-500 text-emerald-900 dark:text-emerald-200 space-y-1 shadow-sm">
                                                <div class="font-black text-sm flex items-center gap-2">
                                                    <span>MATCH ─────────→</span>
                                                    <span class="text-base">✅ VALID (Asli & Sah)</span>
                                                </div>
                                                <p class="text-[11px] text-emerald-800 dark:text-emerald-300">
                                                    Hash cocok 100% dan tanda tangan RSA-PSS terverifikasi dengan kunci publik kampus. Dokumen terbukti otentik tanpa modifikasi.
                                                </p>
                                            </div>
                                        @else
                                            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 border-2 border-rose-500 text-rose-900 dark:text-rose-200 space-y-1 shadow-sm">
                                                <div class="font-black text-sm flex items-center gap-2">
                                                    <span>DIFFERENT ──→</span>
                                                    <span class="text-base">❌ TAMPERED (Dimanipulasi)</span>
                                                </div>
                                                <p class="text-[11px] text-rose-800 dark:text-rose-300">
                                                    Hasil rekalkulasi hash berbeda atau tanda tangan RSA-PSS tidak cocok. Data telah diubah atau dipalsukan.
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                @endif
            @endif

            <!-- Anti-Fraud Problem & Solution Explanation Section -->
            <div class="mt-16 pt-12 border-t border-slate-200 dark:border-slate-800">
                <div class="text-center max-w-2xl mx-auto space-y-2 mb-10">
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                        Masalah Pemalsuan Sertifikat yang Diselesaikan Certiva
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-300 font-medium">
                        Ketika universitas menerbitkan ribuan sertifikat, verifikasi konvensional rentan terhadap kecurangan. Certiva menutup celah ini secara matematis:
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-lg">
                            ✕
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white">PDF Mudah Diedit</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                            File PDF biasa dapat diubah dengan editor PDF gratis. Certiva menyegel data menggunakan hash SHA-256 dan tanda tangan RSA-PSS sehingga setiap modifikasi otomatis merusak validasi.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-lg">
                            ✕
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white">Nama Peserta Diganti</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                            Oknum sering mengganti nama peserta pada sertifikat orang lain. Di Certiva, nama penerima dan NIM diikat dalam <em>canonical payload</em> resmi kampus.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-lg">
                            ✕
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white">Tanggal Dimanipulasi</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                            Tanggal terbit atau kelulusan tidak bisa dimajukan atau dimundurkan. Tanggal resmi tersimpan dalam blockchain-grade digest yang ditandatangani otoritas kampus.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg">
                            ✓
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white">Verifikasi Instan 24/7</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                            HRD atau rekruter cukup memindai QR Code untuk memeriksa keabsahan secara <em>real-time</em> tanpa perlu mengirim email konfirmasi manual ke kampus.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- In-Browser Camera QR Scanner Modal -->
    <div x-show="scanQrModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4"
         @keydown.escape.window="stopScanner()">
        <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col p-6 space-y-4"
             @click.away="stopScanner()">
            
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600 dark:text-cyan-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Pindai QR Sertifikat</h3>
                        <p class="text-[11px] text-slate-500">Arahkan kamera ke QR code pada dokumen fisik atau ijazah</p>
                    </div>
                </div>
                <button type="button" @click="stopScanner()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Scanner Error Notification -->
            <div x-show="scannerError" class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-700 dark:text-rose-300">
                <span x-text="scannerError"></span>
            </div>

            <!-- Viewport Container for html5-qrcode -->
            <div class="relative rounded-2xl overflow-hidden bg-slate-950 aspect-square flex items-center justify-center border border-slate-700">
                <div id="qr-reader" class="w-full h-full"></div>
            </div>

            <p class="text-[11px] text-center text-slate-500 dark:text-slate-400">
                Deteksi otomatis akan mengarahkan browser ke halaman verifikasi resmi saat QR code terbaca.
            </p>

            <button type="button" @click="stopScanner()" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 transition">
                Tutup Kamera
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur py-8 text-center text-xs text-slate-600 dark:text-slate-400 relative z-10 font-medium">
        <div class="max-w-7xl mx-auto px-4 space-y-2">
            <div>&copy; {{ date('Y') }} <strong>Certiva</strong> &bull; Platform Penerbitan & Verifikasi Kriptografis Sertifikat Digital &bull; Universitas Bina Sarana Informatika.</div>
            <div class="font-mono text-[11px] text-slate-600 dark:text-slate-300">
                Algorithm: RSA-2048 &bull; Hash: SHA-256 &bull; Signature: RSA-PSS &bull; Key Status: Active &bull; Pejabat Otoritas: Ricki Gilang Saputra
            </div>
        </div>
    </footer>
</body>
</html>

