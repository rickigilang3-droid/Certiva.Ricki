<x-guest-layout>
    <div class="flex flex-col gap-5">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-200 bg-indigo-50 text-indigo-600 dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-400">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>

        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Lupa kata sandi?</h1>
            <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-300">Masukkan email akunmu. Kami akan mengirimkan tautan untuk membuat kata sandi baru.</p>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mt-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-5 flex flex-col gap-5">
        @csrf

        <!-- Email Address -->
        <div class="flex flex-col gap-2">
            <x-input-label for="email" value="Alamat email" class="text-xs font-bold text-slate-700 dark:text-slate-300" />
            <x-text-input id="email" class="block w-full rounded-xl border-slate-300 bg-slate-50 py-3 text-sm dark:border-slate-700 dark:bg-slate-950/60" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" placeholder="nama@bsi.ac.id" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
            Kirim tautan reset
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6l6 6-6 6" /></svg>
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-500 transition hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400">Kembali ke login</a>
        </div>
    </form>
</x-guest-layout>
