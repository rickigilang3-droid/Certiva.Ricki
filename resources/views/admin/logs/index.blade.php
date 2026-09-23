<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-cyan-300 border border-indigo-200 dark:border-indigo-500/30">
                        Audit Trail Kriptografi
                    </span>
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">&bull; Log Aktivitas Real-Time</span>
                </div>
                <h2 class="font-extrabold text-2xl sm:text-3xl text-slate-900 dark:text-white tracking-tight">
                    {{ __('Audit & Log Pemindaian Verifikasi') }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Pantau seluruh riwayat pengecekan keaslian sertifikat, pemindaian QR code publik, dan aktivitas akses verifikasi.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('verify.index') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-sm transition">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Buka Portal Verifikasi ↗</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        
        <!-- Metrics Row -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Metric 1: Total Scans -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Total Scan & Cek</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $stats['total_scans'] }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Seluruh riwayat audit</div>
                </div>
            </div>

            <!-- Metric 2: Hari Ini -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-cyan-700 dark:text-cyan-400">Pemeriksaan Hari Ini</span>
                    <div class="w-8 h-8 rounded-xl bg-cyan-50 dark:bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-black text-cyan-600 dark:text-cyan-400 font-mono">{{ $stats['scans_today'] }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Aktivitas 24 jam terakhir</div>
                </div>
            </div>

            <!-- Metric 3: Valid -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Verifikasi Sah</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono">{{ $stats['valid_scans'] }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Dokumen terbukti valid</div>
                </div>
            </div>

            <!-- Metric 4: Tidak Ditemukan / Dicabut -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-rose-700 dark:text-rose-400">Gagal / Ditarik</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-black text-rose-600 dark:text-rose-400 font-mono">{{ $stats['revoked_scans'] + $stats['not_found_scans'] }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $stats['revoked_scans'] }} dicabut, {{ $stats['not_found_scans'] }} tidak ada</div>
                </div>
            </div>
        </div>

        <!-- Filter and Search Bar -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('verification-logs.index') }}" class="flex-1 flex flex-col sm:flex-row items-center gap-3 w-full">
                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nomor sertifikat, alamat IP, atau nama mahasiswa..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder:text-slate-400 text-xs font-medium focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition" />
                </div>

                <!-- Status Filter -->
                <select name="status" 
                        onchange="this.form.submit()"
                        class="w-full sm:w-48 py-2.5 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                    <option value="">Semua Status</option>
                    <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>Sah & Valid</option>
                    <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Dicabut (Revoked)</option>
                    <option value="not_found" {{ request('status') === 'not_found' ? 'selected' : '' }}>Tidak Ditemukan</option>
                </select>

                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                    Filter
                </button>

                @if(request('search') || request('status'))
                    <a href="{{ route('verification-logs.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition">
                        Reset
                    </a>
                @endif
            </form>

            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium shrink-0">
                Menampilkan <strong>{{ $logs->count() }}</strong> dari <strong>{{ $logs->total() }}</strong> entri log
            </div>
        </div>

        <!-- Table Card -->
        <div class="rounded-3xl bg-white dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800/80 text-slate-500 dark:text-slate-400 font-mono text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-6 font-bold">Waktu Pemeriksaan</th>
                            <th class="py-3.5 px-6 font-bold">Nomor Dokumen</th>
                            <th class="py-3.5 px-6 font-bold">Penerima / Keterangan</th>
                            <th class="py-3.5 px-6 font-bold">Alamat IP</th>
                            <th class="py-3.5 px-6 font-bold">Perangkat / Browser</th>
                            <th class="py-3.5 px-6 font-bold text-center">Status Audit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($log->verified_at)->translatedFormat('d M Y, H:i') }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">
                                        {{ \Carbon\Carbon::parse($log->verified_at)->diffForHumans() }}
                                    </div>
                                </td>

                                <td class="py-4 px-6 whitespace-nowrap font-mono font-bold">
                                    @if($log->certificate)
                                        <a href="{{ route('certificates.show', $log->certificate) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                            {{ $log->certificate_number_queried }}
                                        </a>
                                    @else
                                        <span class="text-slate-600 dark:text-slate-300">{{ $log->certificate_number_queried }}</span>
                                    @endif
                                </td>

                                <td class="py-4 px-6">
                                    @if($log->certificate)
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $log->certificate->recipient_name }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-xs">{{ $log->certificate->title }}</div>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-400 italic">Tidak terdaftar di database</span>
                                    @endif
                                </td>

                                <td class="py-4 px-6 whitespace-nowrap font-mono text-slate-600 dark:text-slate-300">
                                    {{ $log->ip_address }}
                                </td>

                                <td class="py-4 px-6 text-slate-500 dark:text-slate-400 max-w-xs truncate" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent ?: 'Unknown User-Agent' }}
                                </td>

                                <td class="py-4 px-6 whitespace-nowrap text-center">
                                    @if($log->status === 'valid')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Sah & Valid
                                        </span>
                                    @elseif($log->status === 'revoked')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60">
                                            Dicabut (Revoked)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60">
                                            Tidak Ditemukan
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500 dark:text-slate-400">
                                    Belum ada catatan log verifikasi yang cocok dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-950/30">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
