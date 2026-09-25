<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-indigo-600 dark:text-cyan-400">Kontrol & Kepatuhan</span>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Audit Aktivitas Admin</h2>
                <p class="mt-1 text-xs text-slate-600 dark:text-slate-400">Riwayat tindakan penting yang mengubah status dan keamanan sistem.</p>
            </div>
            <a href="{{ route('verification-logs.index') }}" class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:border-indigo-400 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:text-indigo-400">
                Lihat log verifikasi
                <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 py-6">
        <form method="GET" action="{{ route('activity-logs.index') }}" class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/80 sm:flex-row">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas atau nama admin..." class="w-full rounded-xl border-slate-300 bg-slate-50 text-sm dark:border-slate-700 dark:bg-slate-950/60 sm:flex-1">
            <select name="action" class="w-full rounded-xl border-slate-300 bg-slate-50 text-sm dark:border-slate-700 dark:bg-slate-950/60 sm:w-56">
                <option value="">Semua tindakan</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>{{ str_replace('.', ' / ', ucfirst($action)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-700">Filter</button>
            @if(request('search') || request('action'))
                <a href="{{ route('activity-logs.index') }}" class="rounded-xl px-3 py-2.5 text-center text-xs font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:hover:bg-slate-800 dark:hover:text-white">Reset</a>
            @endif
        </form>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/80">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-xs">
                    <thead class="border-b border-slate-200 bg-slate-50/80 text-[10px] uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-950/60 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Admin</th>
                            <th class="px-6 py-4">Tindakan</th>
                            <th class="px-6 py-4">Detail</th>
                            <th class="px-6 py-4">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($logs as $log)
                            <tr class="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                <td class="whitespace-nowrap px-6 py-4 font-mono text-slate-600 dark:text-slate-400">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $log->user?->name ?? 'Sistem' }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $log->user?->email ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4"><span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 font-mono text-[10px] font-bold text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">{{ $log->action }}</span></td>
                                <td class="max-w-md px-6 py-4 font-medium text-slate-700 dark:text-slate-300">{{ $log->description }}</td>
                                <td class="whitespace-nowrap px-6 py-4 font-mono text-slate-500 dark:text-slate-400">{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-14 text-center text-slate-500 dark:text-slate-400">Belum ada aktivitas admin yang tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="border-t border-slate-200 p-4 dark:border-slate-800">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
