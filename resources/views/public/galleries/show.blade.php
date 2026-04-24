<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $gallery->title }} - Galeri - {{ $schoolProfile['school_name'] }}</title>

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
                padding: 2rem 0 0.8rem;
            }

            .hero-box {
                background: color-mix(in srgb, #ffffff 90%, transparent);
                border: 1px solid color-mix(in srgb, var(--line-color) 75%, transparent);
                border-radius: 1.2rem;
                padding: 1.3rem;
                box-shadow: 0 20px 38px -34px rgba(15, 23, 42, 0.5);
            }

            .breadcrumb {
                margin: 0;
                font-size: 0.78rem;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                font-weight: 800;
                color: color-mix(in srgb, var(--text-color) 65%, #475569);
            }

            .breadcrumb a {
                color: color-mix(in srgb, var(--primary-color) 78%, #1e293b);
            }

            .hero h1 {
                margin: 0.6rem 0 0.6rem;
                font-family: "Playfair Display", Georgia, serif;
                font-size: clamp(1.82rem, 3.8vw, 2.45rem);
                line-height: 1.25;
            }

            .meta {
                display: flex;
                flex-wrap: wrap;
                gap: 0.36rem 0.6rem;
                margin: 0;
                font-size: 0.82rem;
                color: color-mix(in srgb, var(--text-color) 65%, #64748b);
                font-weight: 700;
            }

            .content {
                padding: 0.3rem 0 2.3rem;
            }

            .description {
                border: 1px solid color-mix(in srgb, var(--line-color) 76%, transparent);
                background: color-mix(in srgb, #ffffff 92%, transparent);
                border-radius: 1rem;
                padding: 1.1rem;
                box-shadow: 0 14px 30px -28px rgba(15, 23, 42, 0.45);
                margin-bottom: 1rem;
            }

            .description p {
                margin: 0;
                font-size: 0.96rem;
                line-height: 1.85;
                color: color-mix(in srgb, var(--text-color) 82%, #334155);
                white-space: pre-line;
            }

            .gallery-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0.82rem;
            }

            .gallery-item {
                border: 1px solid color-mix(in srgb, var(--line-color) 76%, transparent);
                background: color-mix(in srgb, #ffffff 92%, transparent);
                border-radius: 0.86rem;
                overflow: hidden;
                box-shadow: 0 14px 30px -28px rgba(15, 23, 42, 0.45);
            }

            .gallery-item img {
                width: 100%;
                aspect-ratio: 4 / 3;
                object-fit: cover;
                display: block;
                background: color-mix(in srgb, var(--line-color) 45%, #ffffff);
            }

            .gallery-caption {
                margin: 0;
                padding: 0.62rem 0.72rem 0.75rem;
                color: var(--muted-color);
                font-size: 0.82rem;
                line-height: 1.6;
                font-weight: 600;
            }

            .empty-gallery {
                border: 1px dashed color-mix(in srgb, var(--line-color) 78%, transparent);
                border-radius: 1rem;
                background: color-mix(in srgb, #ffffff 90%, transparent);
                padding: 1rem;
                text-align: center;
            }

            .empty-gallery img {
                width: min(460px, 100%);
                aspect-ratio: 16 / 9;
                object-fit: cover;
                border-radius: 0.82rem;
                border: 1px solid color-mix(in srgb, var(--line-color) 76%, transparent);
            }

            .empty-gallery p {
                margin: 0.85rem 0 0;
                color: color-mix(in srgb, var(--text-color) 64%, #64748b);
                font-size: 0.88rem;
                font-weight: 700;
            }

            .actions {
                margin-top: 1.2rem;
            }

            .back-link {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                font-size: 0.87rem;
                font-weight: 800;
                color: color-mix(in srgb, var(--primary-color) 82%, #1e293b);
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

                .gallery-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 640px) {
                .container {
                    width: min(1120px, calc(100% - 1.2rem));
                }

                .hero {
                    padding-top: 1.1rem;
                }

                .hero-box,
                .description {
                    padding: 1rem;
                }

                .gallery-grid {
                    grid-template-columns: 1fr;
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
                    <p class="breadcrumb">
                        <a href="{{ route('public.home') }}">Beranda</a> / <a href="{{ route('public.galleries.index') }}">Galeri</a> / {{ $gallery->slug }}
                    </p>
                    <h1>{{ $gallery->title }}</h1>
                    <p class="meta">
                        <span>{{ optional($gallery->published_at)->translatedFormat('d M Y H:i') ?? '-' }}</span>
                        <span>•</span>
                        <span>{{ $gallery->author?->name ?? 'Tim Sekolah' }}</span>
                        <span>•</span>
                        <span>{{ $galleryItems->count() }} item</span>
                    </p>
                </div>
            </section>

            <section class="content">
                <div class="container">
                    <article class="description">
                        <p>{{ trim((string) ($gallery->description ?: 'Album dokumentasi kegiatan sekolah.')) }}</p>
                    </article>

                    @if ($galleryItems->isEmpty())
                        <article class="empty-gallery">
                            <img src="{{ $fallbackImageUrl }}" alt="Placeholder album {{ $gallery->title }}" loading="lazy">
                            <p>Album ini belum memiliki item media.</p>
                        </article>
                    @else
                        <div class="gallery-grid">
                            @foreach ($galleryItems as $item)
                                <article class="gallery-item">
                                    <img
                                        src="{{ $item['image_url'] }}"
                                        alt="{{ $item['alt_text'] }}"
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='{{ $fallbackImageUrl }}';"
                                    >
                                    @if ($item['caption'] !== '')
                                        <p class="gallery-caption">{{ $item['caption'] }}</p>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    @endif

                    <div class="actions">
                        <a href="{{ route('public.galleries.index') }}" class="back-link">← Kembali ke daftar galeri</a>
                    </div>
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
