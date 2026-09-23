<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-indigo-50 dark:bg-indigo-500/20 text-indigo-700 dark:text-cyan-300 border border-indigo-200 dark:border-indigo-500/30">
                        Penerbitan Otomatis
                    </span>
                    <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">&bull; Otoritas Administrator</span>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 dark:text-white tracking-tight mt-1">
                    {{ __('Import Massal Sertifikat via CSV') }}
                </h2>
                <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                    Unggah data mahasiswa sekaligus. Sistem akan otomatis menyusun payload kanonikal, menandatangani dengan RSA-PSS, dan membuat dokumen PDF serta QR code untuk setiap baris.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('certificates.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-750 transition shadow-sm">
                    &larr; Kembali ke Daftar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        dragOver: false,
        fileName: '',
        fileSize: '',
        handleFileSelect(e) {
            const file = e.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
            }
        },
        handleDrop(e) {
            this.dragOver = false;
            const file = e.dataTransfer.files[0];
            if (file) {
                this.$refs.fileInput.files = e.dataTransfer.files;
                this.fileName = file.name;
                this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
            }
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-800 dark:text-rose-200 space-y-1 animate-fade-in-down">
                    <div class="font-bold flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Gagal memproses berkas CSV:</span>
                    </div>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Upload Form & Dropzone (7 Cols) -->
                <div class="lg:col-span-7 bg-white dark:bg-slate-900/85 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                    <div>
                        <h3 class="font-extrabold text-lg text-slate-900 dark:text-white">Unggah Berkas CSV</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Pastikan format CSV menggunakan pemisah koma (,) atau titik koma (;) dengan baris tajuk yang sesuai.</p>
                    </div>

                    <form action="{{ route('certificates.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Dropzone -->
                        <div class="relative border-2 border-dashed rounded-3xl p-8 text-center transition-all duration-200 cursor-pointer"
                             :class="dragOver ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-950/40 hover:border-indigo-400 dark:hover:border-indigo-500/50'"
                             @dragover.prevent="dragOver = true"
                             @dragleave.prevent="dragOver = false"
                             @drop.prevent="handleDrop($event)"
                             @click="$refs.fileInput.click()">
                            
                            <input type="file" name="csv_file" x-ref="fileInput" @change="handleFileSelect($event)" accept=".csv,.txt" class="hidden" required />

                            <div class="space-y-3">
                                <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-50 dark:bg-indigo-500/15 border border-indigo-200 dark:border-indigo-500/30 text-indigo-600 dark:text-cyan-400 flex items-center justify-center">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>

                                <template x-if="!fileName">
                                    <div class="space-y-1">
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">
                                            Tarik dan lepaskan berkas CSV Anda di sini, atau <span class="text-indigo-600 dark:text-cyan-400 underline">pilih berkas</span>
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Mendukung format .csv atau .txt (Maksimal 10MB)</p>
                                    </div>
                                </template>

                                <template x-if="fileName">
                                    <div class="p-3 bg-white dark:bg-slate-800 rounded-2xl border border-indigo-200 dark:border-indigo-500/40 max-w-sm mx-auto flex items-center gap-3 text-left">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/20 text-emerald-600 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-bold text-xs text-slate-900 dark:text-white truncate" x-text="fileName"></div>
                                            <div class="text-[11px] text-slate-500 font-mono" x-text="fileSize"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/20 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Proses Import & Tanda Tangan Kriptografis Massal</span>
                        </button>
                    </form>
                </div>

                <!-- Right Column: Template & Guides (5 Cols) -->
                <div class="lg:col-span-5 space-y-6">
                    
                    <!-- Template Download Card -->
                    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900/85 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-900 dark:text-white">Unduh Template Contoh</h4>
                                <p class="text-xs text-slate-500">Gunakan format kolom yang sudah terstandarisasi.</p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            Unduh berkas contoh CSV dengan header kolom yang valid dan data percontohan siap pakai.
                        </p>

                        <a href="{{ route('certificates.import.template') }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-bold text-slate-800 dark:text-slate-200 transition">
                            <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Template CSV (.csv)</span>
                        </a>
                    </div>

                    <!-- Column Spec Table -->
                    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900/85 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">Spesifikasi Kolom CSV</h4>
                        <div class="overflow-x-auto text-[11px]">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500">
                                        <th class="py-1.5 font-bold">Kolom</th>
                                        <th class="py-1.5 font-bold">Sifat</th>
                                        <th class="py-1.5 font-bold">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono text-slate-700 dark:text-slate-300">
                                    <tr>
                                        <td class="py-1.5 font-bold text-indigo-600 dark:text-cyan-400">recipient_name</td>
                                        <td class="py-1.5 text-rose-500 font-bold">Wajib</td>
                                        <td class="py-1.5 font-sans">Nama lengkap mahasiswa</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 font-bold text-indigo-600 dark:text-cyan-400">title</td>
                                        <td class="py-1.5 text-rose-500 font-bold">Wajib</td>
                                        <td class="py-1.5 font-sans">Program studi / gelar lulusan</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 font-bold">recipient_identifier</td>
                                        <td class="py-1.5 text-slate-400">Opsional</td>
                                        <td class="py-1.5 font-sans">NIM mahasiswa</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 font-bold">recipient_email</td>
                                        <td class="py-1.5 text-slate-400">Opsional</td>
                                        <td class="py-1.5 font-sans">Email resmi penerima</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 font-bold">certificate_number</td>
                                        <td class="py-1.5 text-slate-400">Otomatis</td>
                                        <td class="py-1.5 font-sans">Dibuat otomatis jika kosong</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 font-bold">department</td>
                                        <td class="py-1.5 text-slate-400">Opsional</td>
                                        <td class="py-1.5 font-sans">Fakultas / jurusan</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 font-bold">issued_date</td>
                                        <td class="py-1.5 text-slate-400">Opsional</td>
                                        <td class="py-1.5 font-sans">Format: YYYY-MM-DD</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>

