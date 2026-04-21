<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Berita - {{ $schoolProfile['school_name'] }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,700,800|playfair-display:600,700" rel="stylesheet" />

        <style>
            :root {
                --primary-color: {{ $theme['primary_color'] }};
                --secondary-color: {{ $theme['secondary_color'] }};
                --accent-color: {{ $theme['accent_color'] }};
                --surface-color: #ffffff;
                --text-color: #0f172a;
                --muted-color: #475569;
                --line-color: #cbd5e1;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: "Manrope", "Segoe UI", sans-serif;
                color: var(--text-color);
                background:
                    radial-gradient(circle at 8% 4%, color-mix(in srgb, var(--secondary-color) 20%, #ffffff), transparent 40%),
                    radial-gradient(circle at 92% 8%, color-mix(in srgb, var(--accent-color) 22%, #ffffff), transparent 35%),
                    linear-gradient(160deg, #f8fafc 0%, #eef2ff 50%, #f8fafc 100%);
                min-height: 100vh;
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .container {
                width: min(1120px, calc(100% - 2.4rem));
                margin: 0 auto;
            }

            .site-header {
                position: sticky;
                top: 0;
                z-index: 20;
                backdrop-filter: blur(14px);
                background: color-mix(in srgb, #ffffff 86%, transparent);
                border-bottom: 1px solid color-mix(in srgb, var(--line-color) 70%, transparent);
            }

            .header-shell {
                min-height: 78px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1.4rem;
            }

            .brand {
                display: inline-flex;
                align-items: center;
                gap: 0.9rem;
                min-width: 0;
            }

            .brand img {
                width: 2.65rem;
                height: 2.65rem;
                object-fit: contain;
                border-radius: 0.7rem;
                background: var(--surface-color);
                border: 1px solid color-mix(in srgb, var(--line-color) 76%, transparent);
                padding: 0.28rem;
            }

            .brand-title {
                margin: 0;
                font-size: 0.95rem;
                font-weight: 800;
                letter-spacing: 0.02em;
                line-height: 1.35;
            }

            .brand-subtitle {
                display: block;
                margin-top: 0.14rem;
                font-size: 0.74rem;
                color: var(--muted-color);
                font-weight: 600;
            }

            .menu {
                list-style: none;
                display: flex;
                align-items: center;
                gap: 0.45rem;
                padding: 0;
                margin: 0;
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .menu a {
                display: inline-flex;
                align-items: center;
                padding: 0.55rem 0.72rem;
                border-radius: 0.7rem;
                font-size: 0.88rem;
                font-weight: 700;
                color: color-mix(in srgb, var(--text-color) 80%, #334155);
            }

            .menu a:hover {
                background: color-mix(in srgb, var(--primary-color) 10%, #ffffff);
                color: var(--text-color);
            }

            .hero {
                padding: 2.2rem 0 1rem;
            }

            .hero-box {
                background: color-mix(in srgb, #ffffff 90%, transparent);
                border: 1px solid color-mix(in srgb, var(--line-color) 75%, transparent);
                border-radius: 1.2rem;
                padding: 1.4rem;
                box-shadow: 0 20px 38px -34px rgba(15, 23, 42, 0.5);
            }

            .eyebrow {
                margin: 0;
                font-size: 0.75rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-weight: 800;
                color: color-mix(in srgb, var(--text-color) 65%, #475569);
            }

            .hero h1 {
                margin: 0.45rem 0 0.6rem;
                font-family: "Playfair Display", Georgia, serif;
                font-size: clamp(1.8rem, 3.8vw, 2.5rem);
                line-height: 1.2;
            }

            .hero p {
                margin: 0;
                color: var(--muted-color);
                line-height: 1.75;
                max-width: 70ch;
            }

            .content {
                padding: 0.5rem 0 2.2rem;
            }

            .news-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 1rem;
            }

            .news-card {
                border: 1px solid color-mix(in srgb, var(--line-color) 76%, transparent);
                background: color-mix(in srgb, #ffffff 92%, transparent);
                border-radius: 1rem;
                padding: 1rem 1rem 1.1rem;
                box-shadow: 0 14px 30px -28px rgba(15, 23, 42, 0.45);
            }

            .news-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 0.35rem 0.6rem;
                margin-bottom: 0.55rem;
                font-size: 0.76rem;
                color: color-mix(in srgb, var(--text-color) 62%, #64748b);
                font-weight: 700;
            }

            .news-card h2 {
                margin: 0;
                font-size: 1.06rem;
                line-height: 1.45;
                font-weight: 800;
            }

            .news-card h2 a {
                color: color-mix(in srgb, var(--text-color) 85%, #1e293b);
            }

            .news-card h2 a:hover {
                color: color-mix(in srgb, var(--primary-color) 80%, #1e293b);
            }

            .news-summary {
                margin: 0.58rem 0 0.82rem;
                color: var(--muted-color);
                font-size: 0.92rem;
                line-height: 1.7;
            }

            .news-link {
                display: inline-flex;
                align-items: center;
                gap: 0.36rem;
                font-size: 0.86rem;
                font-weight: 800;
                color: color-mix(in srgb, var(--primary-color) 80%, #1e293b);
            }

            .empty-state {
                border: 1px dashed color-mix(in srgb, var(--line-color) 78%, transparent);
                border-radius: 1rem;
                background: color-mix(in srgb, #ffffff 90%, transparent);
                padding: 1.2rem;
                color: color-mix(in srgb, var(--text-color) 62%, #64748b);
                font-weight: 700;
            }

            .pagination {
                margin-top: 1rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.8rem;
                flex-wrap: wrap;
            }

            .pagination-summary {
                margin: 0;
                font-size: 0.8rem;
                color: color-mix(in srgb, var(--text-color) 62%, #64748b);
                font-weight: 700;
            }

            .pagination-links {
                display: inline-flex;
                gap: 0.4rem;
                align-items: center;
            }

            .pagination-links a,
            .pagination-links span {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 2rem;
                height: 2rem;
                border-radius: 0.56rem;
                border: 1px solid color-mix(in srgb, var(--line-color) 76%, transparent);
                background: color-mix(in srgb, #ffffff 92%, transparent);
                font-size: 0.82rem;
                font-weight: 800;
                color: color-mix(in srgb, var(--text-color) 76%, #334155);
            }

            .pagination-links a:hover {
                background: color-mix(in srgb, var(--primary-color) 12%, #ffffff);
                color: var(--text-color);
            }

            .pagination-links .current {
                border-color: color-mix(in srgb, var(--primary-color) 45%, #ffffff);
                background: color-mix(in srgb, var(--primary-color) 14%, #ffffff);
                color: var(--text-color);
            }

            .site-footer {
                border-top: 1px solid color-mix(in srgb, var(--line-color) 72%, transparent);
                background: color-mix(in srgb, #ffffff 86%, transparent);
                padding: 1.1rem 0 2.2rem;
            }

            .footer-shell {
                display: grid;
                grid-template-columns: 1fr auto;
                align-items: center;
                gap: 1rem;
            }

            .footer-shell p {
                margin: 0;
                color: color-mix(in srgb, var(--text-color) 62%, #64748b);
                font-size: 0.82rem;
                font-weight: 700;
            }

            .footer-links {
                list-style: none;
                display: flex;
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 0.45rem;
                margin: 0;
                padding: 0;
            }

            .footer-links a {
                display: inline-flex;
                align-items: center;
                padding: 0.4rem 0.68rem;
                border-radius: 0.54rem;
                font-size: 0.8rem;
                font-weight: 700;
                color: color-mix(in srgb, var(--text-color) 74%, #334155);
            }

            .footer-links a:hover {
                background: color-mix(in srgb, var(--accent-color) 16%, #ffffff);
            }

            @media (max-width: 980px) {
                .header-shell {
                    flex-direction: column;
                    align-items: flex-start;
                    padding: 0.9rem 0;
                }

                .menu,
                .footer-links {
                    justify-content: flex-start;
                }

                .footer-shell {
                    grid-template-columns: 1fr;
                }

                .news-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 640px) {
                .container {
                    width: min(1120px, calc(100% - 1.2rem));
                }

                .hero {
                    padding-top: 1.1rem;
                }

                .hero-box {
                    padding: 1rem;
                }
            }
        </style>
    </head>
    <body>
        <header class="site-header">
            <div class="container header-shell">
                <a href="{{ route('public.home') }}" class="brand">
                    <img src="{{ $theme['logo_url'] }}" alt="Logo {{ $schoolProfile['school_name'] }}">
                    <p class="brand-title">
                        {{ $schoolProfile['school_name'] }}
                        <span class="brand-subtitle">Website Resmi</span>
                    </p>
                </a>

                @if (! empty($headerNavigation['items']))
                    <nav aria-label="Navigasi utama">
                        <ul class="menu">
                            @foreach ($headerNavigation['items'] as $item)
                                <li>
                                    <a
                                        href="{{ $item['url'] }}"
                                        target="{{ $item['target'] }}"
                                        @if ($item['target'] === '_blank' || $item['is_external']) rel="noopener noreferrer" @endif
                                    >
                                        {{ $item['label'] }}
                                    </a>
                                </li>
                            @endforeach
                            <li>
                                <a href="{{ $adminLoginUrl }}">Masuk Admin</a>
                            </li>
                        </ul>
                    </nav>
                @endif
            </div>
        </header>

        <main>
            <section class="hero">
                <div class="container hero-box">
                    <p class="eyebrow">Publikasi Sekolah</p>
                    <h1>Berita Terbaru</h1>
                    <p>Kumpulan informasi terbaru dari kegiatan, prestasi, dan program sekolah.</p>
                </div>
            </section>

            <section class="content">
                <div class="container">
                    @if ($newsItems->isEmpty())
                        <p class="empty-state">Belum ada berita yang dipublikasikan.</p>
                    @else
                        <div class="news-grid">
                            @foreach ($newsItems as $news)
                                <article class="news-card">
                                    <p class="news-meta">
                                        <span>{{ optional($news->published_at)->translatedFormat('d M Y') ?? '-' }}</span>
                                        <span>•</span>
                                        <span>{{ $news->author?->name ?? 'Tim Sekolah' }}</span>
                                    </p>
                                    <h2>
                                        <a href="{{ route('public.news.show', ['slug' => $news->slug]) }}">
                                            {{ $news->title }}
                                        </a>
                                    </h2>
                                    <p class="news-summary">
                                        {{ \Illuminate\Support\Str::limit(trim(strip_tags($news->excerpt ?: $news->content)), 180) }}
                                    </p>
                                    <a class="news-link" href="{{ route('public.news.show', ['slug' => $news->slug]) }}">
                                        Baca Selengkapnya →
                                    </a>
                                </article>
                            @endforeach
                        </div>

                        @if ($newsItems->hasPages())
                            <div class="pagination">
                                <p class="pagination-summary">
                                    Menampilkan {{ $newsItems->firstItem() }}-{{ $newsItems->lastItem() }} dari {{ $newsItems->total() }} berita.
                                </p>
                                <nav class="pagination-links" aria-label="Pagination Berita">
                                    @if ($newsItems->onFirstPage())
                                        <span>«</span>
                                    @else
                                        <a href="{{ $newsItems->previousPageUrl() }}" rel="prev">«</a>
                                    @endif

                                    @foreach ($newsItems->getUrlRange(1, $newsItems->lastPage()) as $page => $url)
                                        @if ($page === $newsItems->currentPage())
                                            <span class="current">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}">{{ $page }}</a>
                                        @endif
                                    @endforeach

                                    @if ($newsItems->hasMorePages())
                                        <a href="{{ $newsItems->nextPageUrl() }}" rel="next">»</a>
                                    @else
                                        <span>»</span>
                                    @endif
                                </nav>
                            </div>
                        @endif
                    @endif
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-shell">
                <p>© {{ now()->year }} {{ $schoolProfile['school_name'] }}. Seluruh hak cipta dilindungi.</p>

                @if (! empty($footerNavigation['items']))
                    <ul class="footer-links">
                        @foreach ($footerNavigation['items'] as $item)
                            <li>
                                <a
                                    href="{{ $item['url'] }}"
                                    target="{{ $item['target'] }}"
                                    @if ($item['target'] === '_blank' || $item['is_external']) rel="noopener noreferrer" @endif
                                >
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </footer>
    </body>
</html>
