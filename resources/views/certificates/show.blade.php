<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-200 leading-tight">
                        {{ $certificate->certificate_number }}
                    </h2>
                    @if($certificate->status === 'active')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                            Aktif
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                            Dicabut (Revoked)
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ $certificate->recipient_name }} &bull; {{ $certificate->title }}
                </p>
            </div>

@php
    $certDate = \Carbon\Carbon::parse($certificate->issued_date);
    $linkedInUrl = 'https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME'
        . '&name=' . urlencode($certificate->title)
        . '&organizationName=' . urlencode('Universitas Bina Sarana Informatika')
        . '&issueYear=' . $certDate->year
        . '&issueMonth=' . $certDate->month
        . '&certUrl=' . urlencode(route('verify.show', $certificate->certificate_number))
        . '&certId=' . urlencode($certificate->certificate_number);
@endphp
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ $linkedInUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-[#0A66C2] hover:bg-[#004182] text-white text-xs font-bold transition shadow-sm">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 8.76a1.46 1.46 0 1 0 0-2.92 1.46 1.46 0 0 0 0 2.92m1.39 9.74v-8.37H5.07v8.37h2.78z"/></svg>
                    <span>Add to LinkedIn</span>
                </a>
                <a href="{{ route('verify.show', $certificate->certificate_number) }}" target="_blank" class="px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 transition">
                    Buka Portal Publik ↗
                </a>
                <a href="{{ route('certificates.pdf', $certificate) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Unduh PDF</span>
                </a>
            </div>
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

            <!-- Revocation Notice if Revoked -->
            @if($certificate->status === 'revoked')
                <div class="p-5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="space-y-1 text-xs">
                        <div class="font-bold text-amber-900 dark:text-amber-200 text-sm">Sertifikat Telah Dicabut (Revoked)</div>
                        <div class="text-amber-800 dark:text-amber-300">
                            <strong>Alasan:</strong> {{ $certificate->revocation_reason }}
                        </div>
                        <div class="text-slate-500 text-[11px]">
                            Dicabut pada: {{ $certificate->revoked_at ? $certificate->revoked_at->translatedFormat('d F Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left 2 Cols: Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/60 p-6 sm:p-8 shadow-sm space-y-6">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-700/60 pb-3">
                            Informasi Ijazah & Data Akademik
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                <div class="text-slate-600 dark:text-slate-400 font-semibold">Nama Penerima</div>
                                <div class="text-base font-bold text-slate-900 dark:text-white mt-1">{{ $certificate->recipient_name }}</div>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                <div class="text-slate-600 dark:text-slate-400 font-semibold">NIM / Student ID</div>
                                <div class="text-base font-mono font-bold text-slate-900 dark:text-white mt-1">{{ $certificate->recipient_identifier ?? '-' }}</div>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                <div class="text-slate-600 dark:text-slate-400 font-semibold">Gelar / Bidang Kompetensi</div>
                                <div class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $certificate->title }}</div>
                                <div class="text-slate-600 dark:text-slate-400 mt-0.5">{{ $certificate->department }}</div>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                <div class="text-slate-600 dark:text-slate-400 font-semibold">Institusi Penerbit</div>
                                <div class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $certificate->institution_name }}</div>
                                <div class="text-slate-600 dark:text-slate-400 mt-0.5">Penandatangan: {{ $certificate->signatory_name }}</div>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                <div class="text-slate-600 dark:text-slate-400 font-semibold">Tanggal Terbit Resmi</div>
                                <div class="text-sm font-bold text-slate-900 dark:text-white mt-1">
                                    {{ \Carbon\Carbon::parse($certificate->issued_date)->translatedFormat('d F Y') }}
                                </div>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                <div class="text-slate-600 dark:text-slate-400 font-semibold">Email Penerima</div>
                                <div class="text-sm font-bold text-slate-900 dark:text-white mt-1">{{ $certificate->recipient_email ?? '-' }}</div>
                            </div>
                        </div>

                        @if($certificate->description)
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 italic">
                                "{{ $certificate->description }}"
                            </div>
                        @endif
                    </div>

                    <!-- Verification Logs for this Certificate -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/60 p-6 shadow-sm space-y-4">
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">Riwayat Audit Verifikasi</h4>
                        <div class="overflow-x-auto text-xs">
                            <table class="w-full text-left">
                                <thead class="border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold uppercase text-[11px]">
                                    <tr>
                                        <th class="py-2.5">Waktu</th>
                                        <th class="py-2.5">IP Address</th>
                                        <th class="py-2.5">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                                    @forelse($certificate->verificationLogs as $log)
                                        <tr>
                                            <td class="py-2.5 font-mono text-slate-600 dark:text-slate-400">{{ $log->verified_at->translatedFormat('d M Y H:i:s') }}</td>
                                            <td class="py-2.5 font-mono text-slate-800 dark:text-slate-200 font-medium">{{ $log->ip_address }}</td>
                                            <td class="py-2.5">
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $log->status === 'authentic' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-800' }}">
                                                    {{ ucfirst($log->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-4 text-center text-slate-500 dark:text-slate-400">Belum ada pemindaian tercatat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Col: Cryptographic Integrity & Revoke Action -->
                <div class="space-y-6">
                    
                    <!-- Live Cryptographic Verification Box -->
                    <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-6 shadow-xl border border-indigo-900/60 space-y-4">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $isValidSignature ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-200">Audit Kriptografis</h4>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-indigo-500/30 text-indigo-300 font-mono">RSA-PSS</span>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Validasi Signature:</span>
                                <span class="font-bold {{ $isValidSignature ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $isValidSignature ? 'VALID (MATCH)' : 'INVALID / TAMPERED' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Validasi Hash SHA-256:</span>
                                <span class="font-bold {{ $isHashValid ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $isHashValid ? 'MATCH (INTEGRITY OK)' : 'MISMATCH' }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-1 pt-2 border-t border-white/10 text-[10px]">
                            <span class="text-slate-400 uppercase tracking-wider block">SHA-256 Digest:</span>
                            <div class="font-mono text-[9px] bg-black/40 p-2 rounded break-all text-emerald-300">
                                {{ $certificate->hash_sha256 }}
                            </div>
                        </div>

                        <div class="space-y-1 text-[10px]">
                            <span class="text-slate-400 uppercase tracking-wider block">Kunci Penandatangan:</span>
                            <div class="font-mono text-[9px] bg-black/40 p-2 rounded break-all text-indigo-300">
                                {{ $certificate->cryptoKey->key_id }} ({{ $certificate->cryptoKey->fingerprint }})
                            </div>
                        </div>
                    </div>

                    <!-- Revocation Control -->
                    @if($certificate->status === 'active')
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-rose-200 dark:border-rose-900/50 p-6 shadow-sm space-y-4" x-data="{ showRevokeForm: false }">
                            <div>
                                <h4 class="font-bold text-sm text-rose-600 dark:text-rose-400">Tindakan Pencabutan Ijazah (Revocation)</h4>
                                <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                    Jika terjadi pelanggaran akademik, plagiarisme, atau kesalahan administratif, sertifikat dapat dicabut secara resmi.
                                </p>
                            </div>

                            <button @click="showRevokeForm = !showRevokeForm" type="button" class="w-full py-2.5 rounded-xl border border-rose-300 dark:border-rose-700 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-xs font-semibold transition">
                                Cabut Sertifikat Ini
                            </button>

                            <div x-show="showRevokeForm" x-cloak class="pt-3 border-t border-slate-100 dark:border-slate-700">
                                <form action="{{ route('certificates.revoke', $certificate) }}" method="POST" class="space-y-3" onsubmit="return confirm('Apakah Anda yakin ingin mencabut sertifikat ini secara permanen?')">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1">Alasan Resmi Pencabutan *</label>
                                        <textarea name="revocation_reason" required rows="3" placeholder="cth: Pelanggaran etika akademik atau pembatalan administratif kelulusan..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-rose-500"></textarea>
                                    </div>
                                    <button type="submit" class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow transition">
                                        Konfirmasi Pencabutan Resmi
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>
</x-app-layout>

