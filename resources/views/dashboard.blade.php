<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-cyan-300 border border-indigo-200 dark:border-indigo-500/30">
                        Otoritas Institusi Resmi
                    </span>
                    <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">&bull; Universitas Bina Sarana Informatika</span>
                </div>
                <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900 dark:text-white tracking-tight">
                    {{ __('Dashboard Otoritas Kampus') }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                    Pusat Penerbitan Ijazah Digital, Manajemen Kunci Kriptografi RSA-PSS, dan Audit Verifikasi Publik.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('verify.index') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900/80 hover:bg-slate-50 dark:hover:bg-slate-850 border border-slate-300 dark:border-slate-700/80 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-sm transition">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                    <span>Portal Verifikasi Publik ↗</span>
                </a>
                <a href="{{ route('certificates.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white text-xs font-bold shadow-md hover:shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Terbitkan Sertifikat Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Top Row: 4 Metric Cards + Cryptographic Vault Widget -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left 8 Cols: Metric Cards (2x2) -->
                <div class="lg:col-span-8 grid grid-cols-2 sm:grid-cols-2 gap-4">
                    
                    <!-- Metric 1: Total Diterbitkan -->
                    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-indigo-500/50 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Total Diterbitkan</span>
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-500/15 border border-indigo-200 dark:border-indigo-500/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-3xl font-black text-slate-900 dark:text-white font-mono tracking-tight">{{ $stats['total_certificates'] }}</div>
                            <div class="text-xs text-slate-600 dark:text-slate-300 mt-1 font-medium">Dokumen tersimpan di MySQL</div>
                        </div>
                    </div>

                    <!-- Metric 2: Sertifikat Aktif -->
                    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-emerald-500/50 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Sertifikat Sah & Aktif</span>
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono tracking-tight">{{ $stats['active_certificates'] }}</div>
                            <div class="text-xs text-slate-600 dark:text-slate-300 mt-1 font-medium">Tervalidasi secara kriptografis</div>
                        </div>
                    </div>

                    <!-- Metric 3: Dicabut (Revoked) -->
                    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-amber-500/50 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-700 dark:text-amber-400">Dicabut (Revoked)</span>
                            <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-3xl font-black text-amber-600 dark:text-amber-400 font-mono tracking-tight">{{ $stats['revoked_certificates'] }}</div>
                            <div class="text-xs text-slate-600 dark:text-slate-300 mt-1 font-medium">Dibatalkan resmi oleh institusi</div>
                        </div>
                    </div>

                    <!-- Metric 4: Total Verifikasi -->
                    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-cyan-500/50 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-cyan-700 dark:text-cyan-400">Total Scan Verifikasi</span>
                            <div class="w-9 h-9 rounded-xl bg-cyan-50 dark:bg-cyan-500/15 border border-cyan-200 dark:border-cyan-500/30 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-3xl font-black text-cyan-700 dark:text-cyan-300 font-mono tracking-tight">{{ $stats['total_verifications'] }}</div>
                            <div class="text-xs text-slate-600 dark:text-slate-300 mt-1 font-medium">{{ $stats['verified_today'] }} pemeriksaan hari ini</div>
                        </div>
                    </div>

                </div>

                <!-- Right 4 Cols: Cryptographic Key Vault Widget -->
                <div class="lg:col-span-4 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white p-6 rounded-3xl shadow-xl border border-indigo-500/40 flex flex-col justify-between relative overflow-hidden">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-white/10">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-200">Keamanan Kriptografi</h3>
                            </div>
                            <a href="{{ route('crypto-keys.index') }}" class="text-[11px] text-cyan-300 hover:text-white transition font-semibold">
                                Kelola &rarr;
                            </a>
                        </div>

                        <!-- System Cryptographic Attributes Specification Block -->
                        <div class="space-y-2.5 text-xs">
                            <div class="flex justify-between items-center py-1 border-b border-white/5">
                                <span class="text-slate-300">Algorithm</span>
                                <span class="font-mono font-bold text-white">{{ $activeKey->algorithm ?? 'RSA-2048' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-white/5">
                                <span class="text-slate-300">Hash</span>
                                <span class="font-mono font-bold text-white">{{ $activeKey->hash_algorithm ?? 'SHA-256' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-white/5">
                                <span class="text-slate-300">Signature</span>
                                <span class="font-mono font-bold text-cyan-300">{{ $activeKey->signature_scheme ?? 'RSA-PSS' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-white/5">
                                <span class="text-slate-300">Key Status</span>
                                <span class="font-semibold text-emerald-400 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    <span>{{ ucfirst($activeKey->status ?? 'Active') }}</span>
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-white/5">
                                <span class="text-slate-300">Pejabat Otoritas</span>
                                <span class="font-bold text-white">
                                    Ricki Gilang Saputra
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Monospace Thumbprint -->
                    <div class="pt-4 border-t border-white/10 mt-3">
                        <div class="text-[10px] text-slate-300 uppercase tracking-wider mb-1 font-semibold">Key Thumbprint:</div>
                        <div class="font-mono text-[10px] bg-black/50 p-2 rounded-xl border border-white/10 text-cyan-300 truncate">
                            {{ $activeKey->fingerprint ?? 'e455af90e11e45398600344a605a6e9a32ac4b28cdd779a43a719efbff610adb' }}
                        </div>
                    </div>
                </div>

            </div>

            <!-- Recent Certificates Table -->
            <div class="bg-white dark:bg-slate-900/80 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden space-y-0">
                <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Sertifikat & Ijazah Terbaru</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300 font-medium">Daftar dokumen akademik yang telah disegel tanda tangan digital.</p>
                    </div>
                    <a href="{{ route('certificates.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-cyan-300 transition flex items-center gap-1">
                        <span>Lihat Semua Data</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-700 dark:text-slate-300 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-4">Nomor Registrasi</th>
                                <th class="px-6 py-4">Nama Mahasiswa & NIM</th>
                                <th class="px-6 py-4">Gelar & Program Studi</th>
                                <th class="px-6 py-4">Tanggal Terbit</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($recentCertificates as $cert)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-cyan-400">
                                        <a href="{{ route('certificates.show', $cert) }}" class="hover:underline">
                                            {{ $cert->certificate_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $cert->recipient_name }}</div>
                                        <div class="text-slate-600 dark:text-slate-300 font-mono text-xs">{{ $cert->recipient_identifier ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $cert->title }}</div>
                                        <div class="text-slate-600 dark:text-slate-300 text-xs">{{ $cert->department }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                                        {{ \Carbon\Carbon::parse($cert->issued_date)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($cert->status === 'active')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-500/15 dark:text-amber-400 dark:border-amber-500/30">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-amber-400"></span>
                                                Dicabut
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <!-- Tombol Verifikasi Publik -->
                                            <a href="{{ route('verify.show', $cert->certificate_number) }}" 
                                               target="_blank" 
                                               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 text-xs font-semibold shadow-sm transition active:scale-95" 
                                               title="Buka Halaman Verifikasi Publik (Tab Baru)">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>Verifikasi</span>
                                                <svg class="w-3 h-3 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>

                                            <!-- Tombol Kelola -->
                                            <a href="{{ route('certificates.show', $cert) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold shadow-sm transition active:scale-95 group" 
                                               title="Buka Halaman Detail & Audit Kriptografis">
                                                <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Kelola</span>
                                            </a>

                                            <!-- Tombol PDF (Split Action: Unduh & Pratinjau) -->
                                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                                <div class="inline-flex rounded-xl shadow-sm">
                                                    <a href="{{ route('certificates.pdf', $cert) }}" 
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-l-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-bold transition"
                                                       title="Unduh Berkas PDF Resmi (RSA-PSS)">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        <span>PDF</span>
                                                    </a>
                                                    <button type="button" 
                                                            @click="open = !open" 
                                                            @click.outside="open = false" 
                                                            class="px-2 py-1.5 rounded-r-xl bg-indigo-700 hover:bg-indigo-800 text-indigo-100 hover:text-white border-l border-indigo-500/40 text-xs font-bold transition focus:outline-none"
                                                            title="Opsi Dokumen PDF">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                    </button>
                                                </div>

                                                <!-- Dropdown Menu -->
                                                <div x-show="open" 
                                                     x-cloak 
                                                     x-transition 
                                                     class="absolute right-0 mt-1.5 w-48 rounded-xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 z-30 text-left">
                                                    <a href="{{ route('certificates.preview', $cert) }}" 
                                                       target="_blank" 
                                                       class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/80 font-semibold transition">
                                                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Pratinjau di Tab Baru</span>
                                                    </a>
                                                    <a href="{{ route('certificates.pdf', $cert) }}" 
                                                       class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/80 font-semibold transition">
                                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        <span>Unduh File (.pdf)</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-600 dark:text-slate-300 font-medium">
                                        Belum ada sertifikat yang diterbitkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Verification Activity Stream -->
            <div class="bg-white dark:bg-slate-900/80 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Log Aktivitas Verifikasi Publik</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300 font-medium">Audit pemindaian QR Code dan query validasi dari pihak ketiga.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-700 dark:text-slate-300 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-3.5">Waktu Verifikasi</th>
                                <th class="px-6 py-3.5">Nomor Sertifikat</th>
                                <th class="px-6 py-3.5">Nama Mahasiswa</th>
                                <th class="px-6 py-3.5">IP Address</th>
                                <th class="px-6 py-3.5">Hasil Audit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($recentLogs as $log)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="px-6 py-3.5 text-slate-600 dark:text-slate-300 font-mono font-medium">
                                        {{ $log->verified_at ? $log->verified_at->translatedFormat('d M Y H:i:s') : '-' }}
                                    </td>
                                    <td class="px-6 py-3.5 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                        <a href="{{ route('verify.show', $log->certificate_number_queried) }}" target="_blank" class="hover:underline">
                                            {{ $log->certificate_number_queried }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-3.5 font-bold text-slate-900 dark:text-slate-200">
                                        {{ $log->certificate->recipient_name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-3.5 font-mono text-slate-600 dark:text-slate-300 font-medium">
                                        {{ $log->ip_address ?? '127.0.0.1' }}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        @if($log->status === 'authentic' || $log->status === 'verified')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/30">
                                                Sah & Asli
                                            </span>
                                        @elseif($log->status === 'revoked')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-500/15 dark:text-amber-400 dark:border-amber-500/30">
                                                Dicabut
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200 dark:bg-rose-500/15 dark:text-rose-400 dark:border-rose-500/30">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-slate-600 dark:text-slate-300 font-medium">
                                        Belum ada aktivitas verifikasi publik tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
