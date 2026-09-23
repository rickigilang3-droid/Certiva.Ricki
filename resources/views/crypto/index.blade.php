<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-200 leading-tight">
                    {{ __('Keamanan Kriptografi & Manajemen Kunci') }}
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 font-medium">
                    Konfigurasi kunci penandatanganan asimetris RSA-2048, skema RSA-PSS, dan rotasi kunci berkala.
                </p>
            </div>
            
            <form action="{{ route('crypto-keys.rotate') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melakukan rotasi kunci kriptografi baru (RSA-2048)? Kunci lama akan beralih status menjadi Rotated dan tetap dapat digunakan untuk verifikasi sertifikat terdahulu.')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Rotasi Kunci RSA-2048 Baru</span>
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if(session('status'))
                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs sm:text-sm text-emerald-800 dark:text-emerald-200 flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Active Key Hero Card -->
            <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-indigo-900/60 relative overflow-hidden space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600/30 border border-indigo-500/40 flex items-center justify-center text-indigo-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Kunci Penandatangan Utama (Aktif)</span>
                            </div>
                            <h3 class="text-xl font-bold text-white mt-0.5">{{ $activeKey->name }}</h3>
                            <div class="text-xs text-slate-300 font-mono">{{ $activeKey->key_id }}</div>
                        </div>
                    </div>

                    <a href="{{ route('crypto-keys.download-public', $activeKey) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 text-xs font-semibold text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh Kunci Publik (.pem)</span>
                    </a>
                </div>

                <!-- Primary Specification Grid (The exact user specifications) -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div class="bg-black/30 p-4 rounded-2xl border border-white/5">
                        <div class="text-[11px] text-slate-300 uppercase tracking-wider font-semibold">Algorithm</div>
                        <div class="text-lg font-extrabold text-white mt-1 font-mono">{{ $activeKey->algorithm }}</div>
                        <div class="text-[10px] text-slate-300 mt-0.5">Panjang Modulus 2048-bit</div>
                    </div>

                    <div class="bg-black/30 p-4 rounded-2xl border border-white/5">
                        <div class="text-[11px] text-slate-300 uppercase tracking-wider font-semibold">Hash</div>
                        <div class="text-lg font-extrabold text-white mt-1 font-mono">{{ $activeKey->hash_algorithm }}</div>
                        <div class="text-[10px] text-slate-300 mt-0.5">Secure Hash Algorithm 256</div>
                    </div>

                    <div class="bg-black/30 p-4 rounded-2xl border border-white/5">
                        <div class="text-[11px] text-slate-300 uppercase tracking-wider font-semibold">Signature</div>
                        <div class="text-lg font-extrabold text-cyan-300 mt-1 font-mono">{{ $activeKey->signature_scheme }}</div>
                        <div class="text-[10px] text-slate-300 mt-0.5">Probabilistic Signature Scheme</div>
                    </div>

                    <div class="bg-black/30 p-4 rounded-2xl border border-white/5">
                        <div class="text-[11px] text-slate-300 uppercase tracking-wider font-semibold">Key Status</div>
                        <div class="text-lg font-extrabold text-emerald-400 mt-1 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>Active</span>
                        </div>
                        <div class="text-[10px] text-slate-300 mt-0.5">Siap Menandatangani</div>
                    </div>

                    <div class="bg-black/30 p-4 rounded-2xl border border-white/5 col-span-2 sm:col-span-1">
                        <div class="text-[11px] text-slate-300 uppercase tracking-wider font-semibold">Pejabat Otoritas</div>
                        <div class="text-base font-extrabold text-white mt-1">
                            Ricki Gilang Saputra
                        </div>
                        <div class="text-[10px] text-slate-300 mt-0.5">Universitas Bina Sarana Informatika</div>
                    </div>
                </div>

                <!-- Thumbprint and Public Key View -->
                <div class="space-y-3" x-data="{ showKey: false }">
                    <div>
                        <div class="text-[11px] text-slate-300 uppercase tracking-wider mb-1 font-semibold">SHA-256 Public Key Fingerprint (Thumbprint):</div>
                        <div class="p-3 bg-black/40 rounded-xl border border-white/5 font-mono text-xs text-cyan-300 break-all">
                            {{ $activeKey->fingerprint }}
                        </div>
                    </div>

                    <div>
                        <button @click="showKey = !showKey" type="button" class="text-xs text-indigo-300 hover:text-white underline font-medium">
                            <span x-show="!showKey">Tampilkan Kunci Publik (PEM Format) &darr;</span>
                            <span x-show="showKey">Sembunyikan Kunci Publik &uarr;</span>
                        </button>
                        <div x-show="showKey" x-cloak class="mt-2">
                            <pre class="p-4 bg-black/60 rounded-xl border border-white/10 font-mono text-[10px] text-slate-200 overflow-x-auto whitespace-pre">{{ $activeKey->public_key }}</pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key History Table -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/60 shadow-sm overflow-hidden space-y-4">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700/60">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">Daftar & Riwayat Kunci Kriptografi</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 font-medium">Kunci yang pernah digunakan tetap disimpan untuk memvalidasi sertifikat alumni terdahulu.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100/90 dark:bg-slate-900/60 text-slate-700 dark:text-slate-300 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">Key ID</th>
                                <th class="px-6 py-4">Nama Identifikasi Kunci</th>
                                <th class="px-6 py-4">Algoritma / Skema</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Tanggal Rotasi</th>
                                <th class="px-6 py-4">Sertifikat Tertandatangani</th>
                                <th class="px-6 py-4 text-right">Kunci Publik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @foreach($keys as $k)
                                <tr>
                                    <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ $k->key_id }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $k->name }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-slate-700 dark:text-slate-300">
                                        {{ $k->algorithm }} / {{ $k->signature_scheme }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($k->status === 'active')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
                                        @elseif($k->status === 'rotated')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600">
                                                Rotated
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                                                Revoked
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                                        {{ $k->last_rotated_at ? $k->last_rotated_at->translatedFormat('d F Y') : '22 September 2026' }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                        {{ $k->certificates_count }} dokumen
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('crypto-keys.download-public', $k) }}" class="px-2.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold transition">
                                            .pem
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

