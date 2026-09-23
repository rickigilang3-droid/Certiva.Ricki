<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 dark:text-slate-200 leading-tight">
                    {{ __('Penerbitan Sertifikat & Ijazah Digital') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Isi formulir dan sistem akan secara otomatis menandatangani dokumen dengan algoritma RSA-PSS (RSA-2048 & SHA-256).
                </p>
            </div>
            <a href="{{ route('certificates.index') }}" class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        students: {{ \Illuminate\Support\Js::from($students) }},
        selectedStudentId: '',
        certificate_number: '{{ old('certificate_number', $suggestedNumber) }}',
        recipient_name: '{{ old('recipient_name', '') }}',
        recipient_identifier: '{{ old('recipient_identifier', '') }}',
        recipient_email: '{{ old('recipient_email', '') }}',
        title: '{{ old('title', 'Sarjana Komputer (S.Kom) - Rekayasa Perangkat Lunak') }}',
        category: '{{ old('category', 'Ijazah & Sertifikat Kelulusan') }}',
        department: '{{ old('department', 'Fakultas Teknik & Informatika') }}',
        description: '{{ old('description', 'Dengan Pujian (Cum Laude)') }}',
        institution_name: '{{ old('institution_name', 'Universitas Bina Sarana Informatika') }}',
        signatory_name: '{{ old('signatory_name', 'Prof. Dr. Ir. H. Budi Rahardjo, M.Sc.') }}',
        signatory_title: '{{ old('signatory_title', 'Rektor Universitas Bina Sarana Informatika') }}',
        issued_date: '{{ old('issued_date', date('Y-m-d')) }}',
        presets: {
            'Ijazah & Sertifikat Kelulusan': [
                'Dengan Pujian (Cum Laude)',
                'Sangat Memuaskan',
                'Memuaskan',
                'Lulus dengan Predikat Istimewa',
                'Lulus dengan IPK 3.90'
            ],
            'Piagam Penghargaan Akademik': [
                'Juara 1 Kompetisi Karya Ilmiah Nasional',
                'Mahasiswa Berprestasi Utama (Mawapres)',
                'Penghargaan Inovasi Riset & Publikasi Ilmiah Terbaik',
                'Penghargaan Skripsi / Tugas Akhir Terpuji',
                'Apresiasi Pengabdian Masyarakat Unggul'
            ],
            'Sertifikat Kompetensi Profesi': [
                'Dinyatakan KOMPETEN pada Skema Sertifikasi Profesi',
                'Telah Memenuhi Standar Kompetensi Kerja Nasional Indonesia (SKKNI)',
                'Tervalidasi Kompeten dengan Nilai Sangat Baik (Grade A)',
                'Lulus Uji Asesmen Standar BNSP'
            ],
            'Sertifikat Pelatihan & Workshop': [
                'Sebagai Peserta Aktif dengan Kelulusan Terbaik',
                'Sebagai Pemakalah / Pemateri Utama',
                'Sebagai Peserta Pelatihan Teknis Intensif',
                'Menyelesaikan Seluruh Modul Pelatihan dengan Baik'
            ]
        },
        selectedPreset: '',
        onCategoryChange() {
            this.selectedPreset = '';
            const list = this.presets[this.category] || [];
            if (list.length > 0) {
                this.description = list[0];
            }
        },
        onStudentSelected() {
            if (!this.selectedStudentId) return;
            const s = this.students.find(item => item.id == this.selectedStudentId);
            if (s) {
                this.recipient_name = s.name;
                this.recipient_identifier = s.identifier || '';
                this.recipient_email = s.email || '';
            }
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-800 dark:text-rose-200 space-y-1">
                    <div class="font-bold">Terdapat kesalahan pengisian formulir:</div>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Form Column (7 cols) -->
                <div class="lg:col-span-7 bg-white dark:bg-slate-800 p-6 sm:p-8 rounded-2xl border border-slate-200 dark:border-slate-700/60 shadow-sm space-y-6">
                    <div class="border-b border-slate-100 dark:border-slate-700/60 pb-4">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Formulir Data Akademik</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400">Seluruh data yang diisi akan dikompilasi menjadi data kanonikal dan disegel secara kriptografis.</p>
                    </div>

                    <form action="{{ route('certificates.store') }}" method="POST" class="space-y-4 text-xs">
                        @csrf

                        <!-- Row 1: Nomor Registrasi & Kategori -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-800 dark:text-slate-200 mb-1">Nomor Registrasi / Sertifikat *</label>
                                <input type="text" name="certificate_number" x-model="certificate_number" required 
                                       class="w-full font-mono rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-800 dark:text-slate-200 mb-1">Kategori Dokumen *</label>
                                <select name="category" x-model="category" @change="onCategoryChange()" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500 font-semibold">
                                    <option value="Ijazah & Sertifikat Kelulusan">Ijazah & Sertifikat Kelulusan</option>
                                    <option value="Piagam Penghargaan Akademik">Piagam Penghargaan Akademik</option>
                                    <option value="Sertifikat Kompetensi Profesi">Sertifikat Kompetensi Profesi</option>
                                    <option value="Sertifikat Pelatihan & Workshop">Sertifikat Pelatihan & Workshop</option>
                                </select>
                            </div>
                        </div>

                        <!-- Quick Pick Registered Student (Sat-Set Mode) -->
                        <div class="p-4 rounded-2xl bg-gradient-to-r from-indigo-50/80 via-blue-50/50 to-cyan-50/80 dark:from-slate-900 dark:via-indigo-950/40 dark:to-slate-900 border border-indigo-200 dark:border-indigo-500/30 space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="student_select" class="block font-bold text-xs text-indigo-950 dark:text-cyan-300 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-indigo-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>Pilih Mahasiswa Terdaftar (Auto-Fill / Sat Set)</span>
                                </label>
                                <span class="text-[10px] font-semibold text-indigo-600 dark:text-cyan-400 bg-white dark:bg-slate-800 px-2 py-0.5 rounded-full border border-indigo-200 dark:border-indigo-500/30">
                                    {{ $students->count() }} Mahasiswa Terdaftar
                                </span>
                            </div>

                            @if($students->isNotEmpty())
                                <div class="relative">
                                    <select id="student_select" x-model="selectedStudentId" @change="onStudentSelected()" 
                                            class="w-full rounded-xl border border-indigo-300 dark:border-indigo-500/40 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-xs py-2.5 px-3 focus:ring-2 focus:ring-indigo-500 font-medium">
                                        <option value="">-- Klik untuk memilih mahasiswa (Nama, NIM & Email otomatis terisi) --</option>
                                        @foreach($students as $st)
                                            <option value="{{ $st->id }}">
                                                {{ $st->name }} {{ $st->identifier ? '(NIM: ' . $st->identifier . ')' : '' }} — {{ $st->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div x-show="selectedStudentId" x-transition class="flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Data mahasiswa otomatis terisi dan terhubung ke akun portal mahasiswa!</span>
                                </div>
                            @else
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Belum ada akun mahasiswa yang terdaftar di sistem. Anda dapat mengetik data mahasiswa secara manual di bawah, atau mahasiswa dapat mendaftar akun terlebih dahulu di halaman registrasi.
                                </p>
                            @endif
                        </div>

                        <!-- Row 2: Nama Mahasiswa & NIM -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-800 dark:text-slate-200 mb-1">Nama Lengkap Penerima *</label>
                                <input type="text" name="recipient_name" x-model="recipient_name" required placeholder="cth: Ahmad Fauzi, S.Kom."
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500 font-semibold" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-800 dark:text-slate-200 mb-1">NIM / Nomor Induk Mahasiswa</label>
                                <input type="text" name="recipient_identifier" x-model="recipient_identifier" placeholder="cth: 20220801045"
                                       class="w-full font-mono rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500" />
                            </div>
                        </div>

                        <!-- Row 3: Email Penerima -->
                        <div>
                            <label class="block font-semibold text-slate-800 dark:text-slate-200 mb-1">Email Mahasiswa (Opsional)</label>
                            <input type="email" name="recipient_email" x-model="recipient_email" placeholder="mahasiswa@student.certiva.ac.id"
                                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500" />
                        </div>

                        <!-- Row 4: Gelar / Judul Prestasi & Fakultas -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-800 dark:text-slate-200 mb-1">Gelar / Nama Kompetensi *</label>
                                <input type="text" name="title" x-model="title" required placeholder="cth: Sarjana Komputer (S.Kom) - Teknik Informatika"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-800 dark:text-slate-200 mb-1">Fakultas / Departemen</label>
                                <input type="text" name="department" x-model="department" placeholder="cth: Fakultas Ilmu Komputer"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500" />
                            </div>
                        </div>

                        <!-- Row 5: Keterangan / Predikat / Prestasi (Dynamic by Category) -->
                        <div class="space-y-2.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700/80">
                            <div class="flex items-center justify-between">
                                <label class="block font-bold text-slate-800 dark:text-slate-200 text-xs flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    <span x-text="category === 'Piagam Penghargaan Akademik' ? 'Kategori Prestasi / Penghargaan' : (category === 'Sertifikat Kompetensi Profesi' ? 'Keterangan Kompetensi & Rekomendasi' : (category === 'Sertifikat Pelatihan & Workshop' ? 'Keterangan Partisipasi / Kelulusan' : 'Predikat / Keterangan Kelulusan'))"></span>
                                </label>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Pilih opsi atau ketik manual</span>
                            </div>

                            <!-- Preset Dropdown Selector -->
                            <div>
                                <select x-model="selectedPreset" @change="if (selectedPreset) { description = selectedPreset; }"
                                        class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500 font-medium">
                                    <option value="">-- Pilih Opsi Cepat Sesuai Kategori Dokumen --</option>
                                    <template x-for="item in (presets[category] || presets['Ijazah & Sertifikat Kelulusan'])" :key="item">
                                        <option :value="item" x-text="item"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Clickable Quick-Pick Pills -->
                            <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cepat:</span>
                                <template x-for="item in (presets[category] || presets['Ijazah & Sertifikat Kelulusan'])" :key="item">
                                    <button type="button" 
                                            @click="description = item; selectedPreset = item;" 
                                            :class="description === item ? 'bg-indigo-600 text-white border-indigo-600 font-bold' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-indigo-400 font-medium'"
                                            class="text-[10.5px] px-2.5 py-1 rounded-lg border transition shadow-2xs truncate max-w-[280px]" 
                                            x-text="item">
                                    </button>
                                </template>
                            </div>

                            <!-- Editable Input Box -->
                            <div class="pt-1">
                                <input type="text" name="description" x-model="description" 
                                       :placeholder="category === 'Piagam Penghargaan Akademik' ? 'cth: Juara 1 Kompetisi Karya Ilmiah Mahasiswa Nasional' : (category === 'Sertifikat Kompetensi Profesi' ? 'cth: Dinyatakan KOMPETEN pada Skema Sertifikasi' : 'cth: Lulus dengan Predikat Dengan Pujian (Cum Laude)')"
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-xs py-2.5 px-3 focus:ring-2 focus:ring-indigo-500 font-medium" />
                            </div>
                        </div>

                        <!-- Row 6: Institusi, Penandatangan, Jabatan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-800 dark:text-slate-200 mb-1">Nama Institusi *</label>
                                <input type="text" name="institution_name" x-model="institution_name" required
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-800 dark:text-slate-200 mb-1">Nama Penandatangan *</label>
                                <input type="text" name="signatory_name" x-model="signatory_name" required
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-800 dark:text-slate-200 mb-1">Jabatan Penandatangan *</label>
                                <input type="text" name="signatory_title" x-model="signatory_title" required
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500" />
                            </div>
                        </div>

                        <!-- Row 7: Tanggal Terbit -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-800 dark:text-slate-200 mb-1">Tanggal Terbit Resmi *</label>
                                <input type="date" name="issued_date" x-model="issued_date" required
                                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-xs py-2 px-3 focus:ring-2 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-800 dark:text-slate-200 mb-1">Kunci Penandatangan Aktif</label>
                                <div class="p-2 rounded-xl bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-mono text-[11px] truncate border border-slate-200 dark:border-slate-800">
                                    {{ $activeKey->name }} ({{ $activeKey->key_id }})
                                </div>
                            </div>
                        </div>

                        <!-- Security Notice Box -->
                        <div class="p-4 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900 text-[11px] text-indigo-950 dark:text-indigo-200 space-y-1">
                            <div class="font-bold flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-600 dark:bg-indigo-400"></span>
                                <span>Penyegelan Kriptografi Otomatis</span>
                            </div>
                            <p class="text-indigo-900 dark:text-indigo-300">
                                Sistem akan menghitung digest SHA-256 dari seluruh atribut di atas dan menandatanganinya dengan algoritma <strong>RSA-2048 (RSA-PSS)</strong>. QR code verifikasi publik dan file PDF anti-pemalsuan akan langsung di-generate.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4 flex justify-end gap-3">
                            <a href="{{ route('certificates.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Terbitkan & Segel Digital (RSA-PSS)</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Live Preview Column (5 cols) -->
                <div class="lg:col-span-5 space-y-4 sticky top-24">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Pratinjau Langsung (Live Preview)
                        </h4>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-semibold border border-emerald-300 dark:border-emerald-800">Real-time</span>
                    </div>

                    <!-- Mini Visual Certificate Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border-4 border-indigo-900 dark:border-indigo-600 p-6 shadow-xl relative overflow-hidden space-y-4">
                        <div class="border border-amber-500 p-4 rounded-lg relative bg-gradient-to-br from-white via-amber-50/20 to-white dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
                            
                            <!-- Header -->
                            <div class="text-center space-y-1">
                                <div class="w-8 h-8 rounded-full bg-indigo-900 text-amber-400 font-bold text-sm mx-auto flex items-center justify-center border border-amber-400">
                                    C
                                </div>
                                <div class="font-bold text-xs uppercase tracking-wider text-indigo-950 dark:text-indigo-200" x-text="institution_name"></div>
                                <div class="text-[9px] text-slate-600 dark:text-slate-400 uppercase tracking-wider" x-text="department"></div>
                            </div>

                            <!-- Title -->
                            <div class="text-center my-3">
                                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-900 dark:text-slate-100" x-text="category"></div>
                                <div class="w-16 h-0.5 bg-amber-500 mx-auto my-1"></div>
                                <div class="text-[9px] text-slate-600 dark:text-slate-400 italic">Diberikan secara sah kepada:</div>
                            </div>

                            <!-- Recipient -->
                            <div class="text-center my-2">
                                <div class="text-base font-extrabold text-indigo-950 dark:text-indigo-300" x-text="recipient_name"></div>
                                <div class="text-[10px] text-slate-600 dark:text-slate-400 font-mono" x-show="recipient_identifier" x-text="'NIM: ' + recipient_identifier"></div>
                            </div>

                            <!-- Statement -->
                            <div class="text-center text-[10px] text-slate-700 dark:text-slate-300 my-2 leading-tight">
                                <div x-text="category === 'Piagam Penghargaan Akademik' ? 'Atas dedikasi, prestasi luar biasa, dan pencapaian akademik terbaik dalam:' : (category === 'Sertifikat Kompetensi Profesi' ? 'Atas keberhasilan memenuhi standar uji kompetensi pada bidang:' : (category === 'Sertifikat Pelatihan & Workshop' ? 'Atas partisipasi aktif dan keberhasilan menyelesaikan:' : 'Atas kelulusan dan penguasaan kompetensi akademik pada:'))"></div>
                                <div class="font-bold text-slate-900 dark:text-white mt-0.5" x-text="title"></div>
                                <div class="text-[9px] italic text-slate-600 dark:text-slate-400 mt-0.5" x-show="description" x-text="'\'' + description + '\''"></div>
                            </div>

                            <!-- Footer / Signatures -->
                            <div class="flex justify-between items-end pt-3 border-t border-slate-200 dark:border-slate-800 text-[9px]">
                                <div class="space-y-0.5">
                                    <div class="text-[8px] font-mono font-bold text-indigo-600 dark:text-indigo-400">RSA-2048 / RSA-PSS</div>
                                    <div class="text-[8px] text-slate-600 dark:text-slate-400" x-text="'No: ' + certificate_number"></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-slate-600 dark:text-slate-400" x-text="issued_date"></div>
                                    <div class="font-bold text-slate-800 dark:text-slate-200 mt-2" x-text="signatory_name"></div>
                                    <div class="text-[8px] text-slate-600 dark:text-slate-400" x-text="signatory_title"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>

