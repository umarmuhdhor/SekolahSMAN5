<x-filament-widgets::widget class="fi-wi-admin-quick-links">
    <x-filament::section
        heading="Quick Links"
        description="Shortcut modul admin ditampilkan berdasarkan permission user."
    >
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($links as $link)
                <article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $link['label'] }}</h3>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ $link['description'] }}</p>
                        </div>
                        <span @class([
                            'rounded-full px-2 py-1 text-[10px] font-semibold uppercase tracking-wide',
                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' => $link['is_ready'],
                            'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' => ! $link['is_ready'],
                        ])>
                            {{ $link['is_ready'] ? 'Tersedia' : 'Segera Hadir' }}
                        </span>
                    </div>

                    @if ($link['is_ready'] && filled($link['url']))
                        <a
                            href="{{ $link['url'] }}"
                            class="mt-4 inline-flex items-center rounded-lg bg-primary-600 px-3 py-2 text-xs font-medium text-white transition hover:bg-primary-500"
                        >
                            Buka Modul
                        </a>
                    @else
                        <button
                            type="button"
                            disabled
                            class="mt-4 inline-flex items-center rounded-lg bg-gray-200 px-3 py-2 text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                        >
                            Belum Tersedia
                        </button>
                    @endif
                </article>
            @empty
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Tidak ada shortcut modul yang tersedia untuk permission akun ini.
                </p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
