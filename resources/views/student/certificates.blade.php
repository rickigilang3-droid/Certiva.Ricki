<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-cyan-50 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-500/30">
                        Portal Mandiri Mahasiswa
                    </span>
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">&bull; Universitas Bina Sarana Informatika</span>
                </div>
                <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900 dark:text-white tracking-tight">
                    {{ __('Pusat Sertifikat & Ijazah Saya') }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Akses, unduh, dan bagikan sertifikat akademik resmi Anda yang telah ditandatangani secara kriptografis (RSA-PSS).
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('verify.index') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-sm transition">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Portal Verifikasi Publik ↗</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="{ 
        previewUrl: '', 
        previewTitle: '', 
        previewCertNumber: '',
        previewRecipient: '',
        previewCategory: '',
        previewDate: '',
        previewSignatory: '',
        previewDownloadUrl: '',
        previewLinkedInUrl: '',
        previewVerifyUrl: '',
        showPreviewModal: false,
        activePreviewTab: 'visual',
        copiedToast: false,
        openPreview(cert) {
            this.previewTitle = cert.title;
            this.previewCertNumber = cert.certificate_number;
            this.previewRecipient = cert.recipient_name;
            this.previewCategory = cert.category || 'Ijazah Kelulusan';
            this.previewDate = cert.issued_date_formatted;
            this.previewSignatory = cert.signatory_name;
            this.previewUrl = cert.preview_url;
            this.previewDownloadUrl = cert.download_url;
            this.previewLinkedInUrl = cert.linkedin_url;
            this.previewVerifyUrl = cert.verify_url;
            this.activePreviewTab = 'visual';
            this.showPreviewModal = true;
        },
        copyLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                this.copiedToast = true;
                setTimeout(() => { this.copiedToast = false; }, 2500);
            });
        }
    }">

        <!-- Toast Notification for Copied Link -->
        <div x-show="copiedToast" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="fixed top-20 right-6 z-50 bg-slate-900 text-white dark:bg-white dark:text-slate-900 px-4 py-2.5 rounded-xl shadow-2xl flex items-center gap-2.5 text-xs font-bold border border-slate-700/40">
            <svg class="w-4 h-4 text-emerald-400 dark:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>Tautan berhasil disalin ke clipboard!</span>
        </div>

        <!-- Student Profile & Stat Banner -->
        <div class="p-6 rounded-3xl bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 dark:from-slate-900/90 dark:via-[#0E1528] dark:to-slate-900/90 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-cyan-500 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-indigo-500/20 shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $user->name }}</h3>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-cyan-100 text-cyan-800 dark:bg-cyan-950/60 dark:text-cyan-300">
                            Mahasiswa
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600 dark:text-slate-400 mt-1 font-mono">
                        @if($user->identifier)
                            <span>NIM: <strong class="text-slate-900 dark:text-white">{{ $user->identifier }}</strong></span>
                            <span>&bull;</span>
                        @endif
                        <span>Email: <strong class="text-slate-900 dark:text-white font-sans">{{ $user->email }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Stats summary badges -->
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="flex-1 md:flex-initial px-4 py-2.5 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 text-center">
                    <div class="text-xl font-black text-slate-900 dark:text-white font-mono">{{ $stats['total'] }}</div>
                    <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase">Sertifikat</div>
                </div>
                <div class="flex-1 md:flex-initial px-4 py-2.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-center">
                    <div class="text-xl font-black text-emerald-700 dark:text-emerald-400 font-mono">{{ $stats['active'] }}</div>
                    <div class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase">Sah & Aktif</div>
                </div>
                @if($stats['revoked'] > 0)
                    <div class="flex-1 md:flex-initial px-4 py-2.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-center">
                        <div class="text-xl font-black text-rose-700 dark:text-rose-400 font-mono">{{ $stats['revoked'] }}</div>
                        <div class="text-[10px] font-bold text-rose-700 dark:text-rose-400 uppercase">Dicabut</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- NEW: Public Portfolio / SKPI Link Banner -->
        @php
            $portfolioUrl = route('student.portfolio', $user->identifier ?: $user->id);
        @endphp
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-cyan-600/10 via-indigo-600/10 to-blue-600/10 border border-cyan-200 dark:border-cyan-500/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-cyan-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-cyan-600/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Halaman Portofolio Kredensial Digital Anda (SKPI)</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-900/50 dark:text-cyan-300 font-bold">Publik</span>
                    </div>
                    <div class="text-[11px] text-slate-600 dark:text-slate-400 font-mono mt-0.5 truncate max-w-md sm:max-w-xl">
                        {{ $portfolioUrl }}
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="button" 
                        @click="copyLink('{{ $portfolioUrl }}')"
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span>Salin Link</span>
                </button>
                <a href="{{ $portfolioUrl }}" 
                   target="_blank"
                   class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold transition shadow-sm shadow-cyan-600/20">
                    <span>Lihat Portofolio ↗</span>
                </a>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('student.certificates') }}" class="w-full sm:max-w-md relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nomor sertifikat, prodi, atau judul..."
                       class="w-full pl-10 pr-20 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder:text-slate-400 text-xs font-medium focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 shadow-sm transition" />
                @if(request('search'))
                    <a href="{{ route('student.certificates') }}" class="absolute inset-y-0 right-12 flex items-center text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 pr-2">
                        Reset
                    </a>
                @endif
                <button type="submit" class="absolute inset-y-1 right-1 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition">
                    Cari
                </button>
            </form>

            <div class="text-xs text-slate-600 dark:text-slate-400 font-medium">
                Menampilkan <strong>{{ $certificates->count() }}</strong> dari <strong>{{ $certificates->total() }}</strong> sertifikat
            </div>
        </div>

        <!-- Certificates Grid -->
        @if($certificates->isEmpty())
            <div class="p-12 text-center rounded-3xl bg-white dark:bg-slate-900/80 border border-dashed border-slate-300 dark:border-slate-800 space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 mx-auto flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="space-y-1 max-w-md mx-auto">
                    <h4 class="text-base font-bold text-slate-900 dark:text-white">Belum Ada Sertifikat Ditemukan</h4>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                        @if(request('search'))
                            Tidak ada sertifikat yang cocok dengan kata kunci pencarian "<strong>{{ request('search') }}</strong>".
                        @else
                            Belum ada sertifikat resmi yang diterbitkan untuk email <strong>{{ $user->email }}</strong>
                            @if($user->identifier) atau NIM <strong>{{ $user->identifier }}</strong>@endif.
                            Sertifikat akan muncul secara otomatis setelah dewan akademik kampus menerbitkan dokumen Anda.
                        @endif
                    </p>
                </div>
                @if(request('search'))
                    <a href="{{ route('student.certificates') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-200 transition">
                        <span>Tampilkan Semua Sertifikat</span>
                    </a>
                @endif
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

                        $certPayloadJson = json_encode([
                            'id' => $cert->id,
                            'title' => $cert->title,
                            'certificate_number' => $cert->certificate_number,
                            'recipient_name' => $cert->recipient_name,
                            'category' => $cert->category ?? 'Ijazah Kelulusan',
                            'issued_date_formatted' => $certDate->translatedFormat('d F Y'),
                            'signatory_name' => $cert->signatory_name,
                            'preview_url' => route('student.certificates.preview', $cert),
                            'download_url' => route('student.certificates.pdf', $cert),
                            'verify_url' => route('verify.show', $cert->certificate_number),
                            'linkedin_url' => $linkedInUrl,
                        ]);
                    @endphp

                    <div class="group relative rounded-3xl bg-white dark:bg-slate-900/85 border border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500/50 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden">
                        
                        <!-- Card Header -->
                        <div class="p-6 space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                    {{ $cert->certificate_number }}
                                </span>
                                
                                @if($cert->status === 'active')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Sah & Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60">
                                        Dicabut (Revoked)
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-1">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-cyan-400">
                                    {{ $cert->category ?? 'Ijazah Kelulusan' }}
                                </div>
                                <h4 class="text-base font-extrabold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2">
                                    {{ $cert->title }}
                                </h4>
                                @if($cert->department)
                                    <p class="text-xs text-slate-600 dark:text-slate-400 font-medium">
                                        {{ $cert->department }}
                                    </p>
                                @endif
                            </div>

                            <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800/60 text-xs space-y-1.5">
                                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                    <span>Tanggal Terbit:</span>
                                    <strong class="text-slate-800 dark:text-slate-200">{{ $certDate->translatedFormat('d F Y') }}</strong>
                                </div>
                                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                    <span>Penandatangan:</span>
                                    <strong class="text-slate-800 dark:text-slate-200 truncate max-w-[170px]" title="{{ $cert->signatory_name }}">
                                        {{ $cert->signatory_name }}
                                    </strong>
                                </div>
                                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400 font-mono text-[10px]">
                                    <span>Segel Kripto:</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">RSA-PSS (2048-bit)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Actions -->
                        <div class="p-4 bg-slate-50/70 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800/80 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5">
                                    <!-- Preview Button (Instant Modal) -->
                                    <button type="button" 
                                            @click="openPreview({{ $certPayloadJson }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:text-indigo-600 transition shadow-sm"
                                            title="Lihat Pratinjau Dokumen">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat</span>
                                    </button>

                                    <!-- Copy Link Button -->
                                    <button type="button" 
                                            @click="copyLink('{{ route('verify.show', $cert->certificate_number) }}')"
                                            class="p-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 transition shadow-sm"
                                            title="Salin Tautan Verifikasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>

                                    <!-- Open Verify Page -->
                                    <a href="{{ route('verify.show', $cert->certificate_number) }}" target="_blank"
                                       class="p-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 transition shadow-sm"
                                       title="Buka Portal Verifikasi">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>

                                <!-- Download PDF Button -->
                                <a href="{{ route('student.certificates.pdf', $cert) }}" 
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh PDF</span>
                                </a>
                            </div>

                            <!-- Add to LinkedIn Button -->
                            <a href="{{ $linkedInUrl }}" 
                               target="_blank"
                               class="w-full inline-flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-[#0A66C2] hover:bg-[#004182] text-white text-xs font-bold transition shadow-sm">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                    <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 8.76a1.46 1.46 0 1 0 0-2.92 1.46 1.46 0 0 0 0 2.92m1.39 9.74v-8.37H5.07v8.37h2.78z"/>
                                </svg>
                                <span>Tambah ke Profil LinkedIn</span>
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-4">
                {{ $certificates->links() }}
            </div>
        @endif

    </div>

    <!-- Enhanced Certificate Preview Modal (Instant Visual + Optional PDF Stream) -->
    <div x-show="showPreviewModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4"
         @keydown.escape.window="showPreviewModal = false">
        
        <div class="relative w-full max-w-4xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col max-h-[90vh]"
             @click.away="showPreviewModal = false">
            
            <!-- Modal Header -->
            <div class="p-4 sm:px-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950/50">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-cyan-300 flex items-center justify-center font-bold text-xs shrink-0">
                        DOC
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate" x-text="previewTitle"></h3>
                        <p class="text-[11px] text-slate-500 font-mono" x-text="previewCertNumber"></p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Tab Selector -->
                    <div class="flex items-center p-1 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs">
                        <button type="button" 
                                @click="activePreviewTab = 'visual'"
                                :class="activePreviewTab === 'visual' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-medium'"
                                class="px-2.5 py-1 rounded-lg transition">
                            Tampilan Kartu
                        </button>
                        <button type="button" 
                                @click="activePreviewTab = 'pdf'"
                                :class="activePreviewTab === 'pdf' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-medium'"
                                class="px-2.5 py-1 rounded-lg transition">
                            PDF Asli
                        </button>
                    </div>

                    <a :href="previewUrl" target="_blank" class="hidden sm:inline-flex px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Buka Tab Baru ↗
                    </a>
                    <button type="button" @click="showPreviewModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-100 dark:bg-[#070A12]">
                
                <!-- TAB 1: Instant Visual Certificate Card -->
                <div x-show="activePreviewTab === 'visual'" class="max-w-2xl mx-auto">
                    <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-900 border-4 border-[#0f2b48] dark:border-indigo-500/40 shadow-xl space-y-6 relative overflow-hidden">
                        
                        <!-- Top University Header -->
                        <div class="text-center space-y-1 border-b border-slate-200 dark:border-slate-800 pb-4">
                            <div class="w-12 h-12 rounded-xl bg-white p-1 mx-auto shadow border border-slate-200">
                                <img src="{{ asset('images/logo.png') }}" alt="Univ Logo" class="w-full h-full object-contain">
                            </div>
                            <h4 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white pt-2">
                                UNIVERSITAS BINA SARANA INFORMATIKA
                            </h4>
                            <p class="text-[10px] text-slate-500 font-serif italic">
                                Sertifikat & Ijazah Digital Resmi Terotentikasi
                            </p>
                        </div>

                        <!-- Certificate Body Details -->
                        <div class="text-center space-y-3 py-2">
                            <span class="text-[11px] font-bold uppercase tracking-widest text-indigo-600 dark:text-cyan-400 font-mono" x-text="previewCategory"></span>
                            <div class="text-xs text-slate-500 font-serif">Diberikan secara sah kepada:</div>
                            <h3 class="text-2xl font-black text-slate-900 dark:text-white" x-text="previewRecipient"></h3>
                            <div class="text-xs text-slate-500 font-serif">Atas kelulusan dan kompetensi pada:</div>
                            <div class="text-base font-extrabold text-indigo-700 dark:text-indigo-400" x-text="previewTitle"></div>
                        </div>

                        <!-- Signatory & QR Stamp -->
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                            <div class="space-y-1">
                                <div class="text-[10px] text-slate-500">Tanggal Terbit:</div>
                                <div class="font-bold text-slate-800 dark:text-slate-200" x-text="previewDate"></div>
                                <div class="text-[10px] font-mono text-emerald-600 font-bold">RSA-PSS / SHA-256 Valid</div>
                            </div>
                            <div class="text-right space-y-1">
                                <div class="text-[10px] text-slate-500">Tertanda Resmi:</div>
                                <div class="font-bold text-slate-800 dark:text-slate-200 underline" x-text="previewSignatory"></div>
                                <div class="text-[10px] text-slate-500">Rektor / Otoritas BSI</div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- TAB 2: Embedded PDF Stream -->
                <div x-show="activePreviewTab === 'pdf'" class="w-full h-[60vh] bg-slate-200 dark:bg-slate-950 rounded-2xl overflow-hidden">
                    <iframe :src="previewUrl" class="w-full h-full border-0"></iframe>
                </div>

            </div>

            <!-- Modal Footer Action Toolbar -->
            <div class="p-4 border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 bg-slate-50 dark:bg-slate-950/70">
                <div class="flex items-center gap-2">
                    <a :href="previewLinkedInUrl" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-[#0A66C2] hover:bg-[#004182] text-white text-xs font-bold transition shadow-sm">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 8.76a1.46 1.46 0 1 0 0-2.92 1.46 1.46 0 0 0 0 2.92m1.39 9.74v-8.37H5.07v8.37h2.78z"/></svg>
                        <span>Tambah ke LinkedIn</span>
                    </a>
                    <a :href="previewVerifyUrl" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 transition shadow-sm">
                        <span>Cek Keaslian ↗</span>
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <a :href="previewDownloadUrl" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh File PDF</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
