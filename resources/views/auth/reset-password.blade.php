<x-guest-layout>
    <div class="flex flex-col gap-2">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-600 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a4 4 0 11-7.75 1.4L3 12.65V17h4v-2h2v-2h2.35A4 4 0 0015 7z" />
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Buat kata sandi baru</h1>
        <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-300">Gunakan kata sandi yang kuat untuk menjaga akun Certiva tetap aman.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="mt-6 flex flex-col gap-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="flex flex-col gap-2">
            <x-input-label for="email" value="Alamat email" class="text-xs font-bold text-slate-700 dark:text-slate-300" />
            <x-text-input id="email" class="block w-full rounded-xl border-slate-300 bg-slate-50 py-3 text-sm dark:border-slate-700 dark:bg-slate-950/60" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="flex flex-col gap-2">
            <x-input-label for="password" value="Kata sandi baru" class="text-xs font-bold text-slate-700 dark:text-slate-300" />
            <x-text-input id="password" class="block w-full rounded-xl border-slate-300 bg-slate-50 py-3 text-sm dark:border-slate-700 dark:bg-slate-950/60" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="flex flex-col gap-2">
            <x-input-label for="password_confirmation" value="Konfirmasi kata sandi" class="text-xs font-bold text-slate-700 dark:text-slate-300" />
            <x-text-input id="password_confirmation" class="block w-full rounded-xl border-slate-300 bg-slate-50 py-3 text-sm dark:border-slate-700 dark:bg-slate-950/60" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
            Simpan kata sandi
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12l4 4L19 6" /></svg>
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-500 transition hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400">Kembali ke login</a>
        </div>
    </form>
</x-guest-layout>
