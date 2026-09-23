<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-200 leading-tight">
                    {{ __('Pangkalan Data Sertifikat Digital') }}
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 font-medium">
                    Daftar ijazah dan sertifikat resmi bertanda tangan digital RSA-2048 / RSA-PSS
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('certificates.import.form') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-300 text-xs font-semibold shadow-sm transition">
                    <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Import CSV</span>
                </a>
                <a href="{{ route('certificates.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Terbitkan Sertifikat Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash notification -->
            @if(session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs sm:text-sm text-emerald-800 dark:text-emerald-200 flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Filters and Search Bar -->
            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700/60 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <form action="{{ route('certificates.index') }}" method="GET" class="flex flex-1 items-center gap-2 w-full">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa, NIM, nomor registrasi, atau judul ijazah..." 
                               class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <select name="status" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 py-2 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Dicabut (Revoked)</option>
                    </select>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm transition">
                        Cari
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('certificates.index') }}" class="px-3 py-2 rounded-xl text-xs text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white font-semibold transition">
                            Reset
                        </a>
                    @endif
                </form>

                <!-- Quick Stats -->
                <div class="flex items-center gap-3 text-xs text-slate-600 dark:text-slate-300 shrink-0 font-medium">
                    <span>Total: <strong class="text-slate-900 dark:text-white font-bold">{{ $stats['total'] }}</strong></span>
                    <span>&bull;</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Aktif: {{ $stats['active'] }}</span>
                    <span>&bull;</span>
                    <span class="text-amber-600 dark:text-amber-400 font-semibold">Dicabut: {{ $stats['revoked'] }}</span>
                </div>
            </div>

            <!-- Certificates Table -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/60 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100/90 dark:bg-slate-900/60 text-slate-700 dark:text-slate-300 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">Nomor Sertifikat</th>
                                <th class="px-6 py-4">Nama Mahasiswa / NIM</th>
                                <th class="px-6 py-4">Gelar & Program Studi</th>
                                <th class="px-6 py-4">Tanggal Terbit</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @forelse($certificates as $cert)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-750/50 transition">
                                    <td class="px-6 py-4 font-mono font-semibold text-indigo-600 dark:text-indigo-400">
                                        <a href="{{ route('certificates.show', $cert) }}" class="hover:underline">
                                            {{ $cert->certificate_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $cert->recipient_name }}</div>
                                        <div class="text-slate-600 dark:text-slate-300 font-mono text-[11px] font-medium">{{ $cert->recipient_identifier ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $cert->title }}</div>
                                        <div class="text-slate-600 dark:text-slate-300 text-[11px]">{{ $cert->department ?? $cert->institution_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                                        {{ \Carbon\Carbon::parse($cert->issued_date)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($cert->status === 'active')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Dicabut
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <!-- Tombol Verifikasi Publik -->
                                            <a href="{{ route('verify.show', $cert->certificate_number) }}" target="_blank" 
                                               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 text-xs font-semibold shadow-sm transition active:scale-95" 
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
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-600 dark:text-slate-400">
                                        Tidak ada data sertifikat yang sesuai kriteria pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($certificates->hasPages())
                    <div class="p-4 border-t border-slate-100 dark:border-slate-700/60">
                        {{ $certificates->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

