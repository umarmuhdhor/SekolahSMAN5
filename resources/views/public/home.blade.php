<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $schoolProfile['school_name'] }}</title>

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
                    radial-gradient(circle at 10% 5%, color-mix(in srgb, var(--primary-color) 24%, #ffffff), transparent 38%),
                    radial-gradient(circle at 92% 8%, color-mix(in srgb, var(--accent-color) 24%, #ffffff), transparent 35%),
                    linear-gradient(160deg, #f8fafc 0%, #eef2ff 45%, #f8fafc 100%);
                min-height: 100vh;
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .container {
                width: min(1120px, calc(100% - 2.5rem));
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
                gap: 1.5rem;
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

            .menu-item {
                position: relative;
            }

            .menu-link {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.58rem 0.78rem;
                border-radius: 0.72rem;
                font-size: 0.89rem;
                font-weight: 700;
                color: color-mix(in srgb, var(--text-color) 80%, #334155);
                transition: 0.2s ease;
            }

            .menu-link:hover {
                background: color-mix(in srgb, var(--primary-color) 10%, #ffffff);
                color: var(--text-color);
            }

            .menu-item:hover .submenu {
                opacity: 1;
                transform: translateY(0);
                pointer-events: auto;
            }

            .submenu {
                list-style: none;
                position: absolute;
                right: 0;
                top: calc(100% + 0.25rem);
                min-width: 11.8rem;
                margin: 0;
                padding: 0.48rem;
                border-radius: 0.82rem;
                border: 1px solid color-mix(in srgb, var(--line-color) 75%, transparent);
                background: #ffffff;
                box-shadow: 0 18px 34px -24px rgba(15, 23, 42, 0.5);
                opacity: 0;
                transform: translateY(0.35rem);
                pointer-events: none;
                transition: 0.16s ease-out;
            }

            .submenu a {
                display: block;
                padding: 0.5rem 0.6rem;
                border-radius: 0.52rem;
                font-size: 0.83rem;
                font-weight: 700;
                color: color-mix(in srgb, var(--text-color) 72%, #334155);
            }

            .submenu a:hover {
                background: color-mix(in srgb, var(--secondary-color) 12%, #ffffff);
                color: var(--text-color);
            }

            .hero {
                padding: 4.2rem 0 1.2rem;
            }

            .hero-shell {
                background: color-mix(in srgb, #ffffff 90%, transparent);
                border: 1px solid color-mix(in srgb, var(--line-color) 74%, transparent);
                border-radius: 1.4rem;
                box-shadow: 0 26px 42px -38px rgba(15, 23, 42, 0.55);
                overflow: hidden;
            }

            .hero-grid {
                display: grid;
                grid-template-columns: 1.24fr 1fr;
                gap: 1.5rem;
                padding: 2rem;
            }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                background: color-mix(in srgb, var(--accent-color) 16%, #ffffff);
                color: color-mix(in srgb, var(--text-color) 84%, #334155);
                padding: 0.32rem 0.72rem;
                border-radius: 999px;
                font-size: 0.73rem;
                letter-spacing: 0.08em;
                font-weight: 800;
                text-transform: uppercase;
            }

            .hero h1 {
                margin: 1rem 0 0.8rem;
                font-family: "Playfair Display", Georgia, serif;
                font-weight: 700;
                font-size: clamp(1.9rem, 4.2vw, 2.9rem);
                line-height: 1.16;
                letter-spacing: 0.01em;
            }

            .hero p {
                margin: 0;
                color: var(--muted-color);
                font-size: 1.01rem;
                line-height: 1.75;
                max-width: 60ch;
            }

            .hero-actions {
                margin-top: 1.35rem;
                display: flex;
                gap: 0.7rem;
                flex-wrap: wrap;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 0.7rem;
                border: 1px solid transparent;
                padding: 0.72rem 1rem;
                font-weight: 800;
                letter-spacing: 0.01em;
                font-size: 0.88rem;
            }

            .btn-primary {
                background: linear-gradient(130deg, var(--primary-color), color-mix(in srgb, var(--primary-color) 68%, #0f172a));
                color: #ffffff;
            }

            .btn-secondary {
                background: color-mix(in srgb, var(--secondary-color) 14%, #ffffff);
                border-color: color-mix(in srgb, var(--secondary-color) 35%, #ffffff);
                color: color-mix(in srgb, var(--text-color) 76%, #0f172a);
            }

            .profile-card {
                background: linear-gradient(
                    160deg,
                    color-mix(in srgb, var(--secondary-color) 15%, #ffffff) 0%,
                    color-mix(in srgb, var(--primary-color) 9%, #ffffff) 100%
                );
                border: 1px solid color-mix(in srgb, var(--line-color) 72%, transparent);
                border-radius: 1rem;
                padding: 1.2rem;
            }

            .profile-card h2 {
                margin: 0 0 0.7rem;
                font-size: 0.96rem;
                letter-spacing: 0.07em;
                text-transform: uppercase;
                color: color-mix(in srgb, var(--text-color) 78%, #334155);
            }

            .meta-list {
                display: grid;
                gap: 0.7rem;
            }

            .meta-row dt {
                margin: 0;
                font-size: 0.75rem;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                color: color-mix(in srgb, var(--text-color) 62%, #64748b);
                font-weight: 800;
            }

            .meta-row dd {
                margin: 0.2rem 0 0;
                font-size: 0.95rem;
                line-height: 1.55;
                color: color-mix(in srgb, var(--text-color) 84%, #334155);
                font-weight: 600;
            }

            .meta-row a {
                color: color-mix(in srgb, var(--primary-color) 75%, #1e293b);
            }

            .social-strip {
                margin-top: 1rem;
                display: flex;
                gap: 0.56rem;
                flex-wrap: wrap;
            }

            .social-strip a {
                border: 1px solid color-mix(in srgb, var(--line-color) 80%, transparent);
                background: #ffffff;
                border-radius: 999px;
                padding: 0.42rem 0.72rem;
                font-size: 0.78rem;
                font-weight: 800;
                color: color-mix(in srgb, var(--text-color) 80%, #334155);
            }

            .site-footer {
                margin-top: 2.2rem;
                border-top: 1px solid color-mix(in srgb, var(--line-color) 72%, transparent);
                background: color-mix(in srgb, #ffffff 86%, transparent);
                padding: 1.2rem 0 2.2rem;
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
                padding: 0.42rem 0.7rem;
                border-radius: 0.58rem;
                font-size: 0.8rem;
                font-weight: 700;
                color: color-mix(in srgb, var(--text-color) 74%, #334155);
            }

            .footer-links a:hover {
                background: color-mix(in srgb, var(--accent-color) 16%, #ffffff);
            }

            .empty-state {
                margin: 0;
                color: color-mix(in srgb, var(--text-color) 62%, #64748b);
                font-size: 0.8rem;
                font-weight: 700;
            }

            @media (max-width: 980px) {
                .hero-grid {
                    grid-template-columns: 1fr;
                    padding: 1.4rem;
                }

                .menu {
                    justify-content: flex-start;
                }

                .header-shell {
                    flex-direction: column;
                    align-items: flex-start;
                    padding: 0.9rem 0;
                }

                .submenu {
                    position: static;
                    opacity: 1;
                    transform: none;
                    pointer-events: auto;
                    margin-top: 0.35rem;
                    padding: 0.3rem;
                    background: color-mix(in srgb, #ffffff 86%, transparent);
                    box-shadow: none;
                }

                .footer-shell {
                    grid-template-columns: 1fr;
                }

                .footer-links {
                    justify-content: flex-start;
                }
            }

            @media (max-width: 640px) {
                body {
                    background:
                        radial-gradient(circle at 12% 5%, color-mix(in srgb, var(--primary-color) 20%, #ffffff), transparent 36%),
                        linear-gradient(160deg, #f8fafc 0%, #eef2ff 65%, #f8fafc 100%);
                }

                .container {
                    width: min(1120px, calc(100% - 1.25rem));
                }

                .hero {
                    padding-top: 1.2rem;
                }

                .hero h1 {
                    font-size: clamp(1.5rem, 8vw, 2rem);
                }

                .hero p {
                    font-size: 0.94rem;
                    line-height: 1.68;
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
                                <li class="menu-item">
                                    <a
                                        href="{{ $item['url'] }}"
                                        class="menu-link"
                                        target="{{ $item['target'] }}"
                                        @if ($item['target'] === '_blank' || $item['is_external']) rel="noopener noreferrer" @endif
                                    >
                                        {{ $item['label'] }}
                                    </a>

                                    @if (! empty($item['children']))
                                        <ul class="submenu">
                                            @foreach ($item['children'] as $child)
                                                <li>
                                                    <a
                                                        href="{{ $child['url'] }}"
                                                        target="{{ $child['target'] }}"
                                                        @if ($child['target'] === '_blank' || $child['is_external']) rel="noopener noreferrer" @endif
                                                    >
                                                        {{ $child['label'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @else
                    <p class="empty-state">Navigasi publik belum diatur.</p>
                @endif
            </div>
        </header>

        <main class="hero">
            <div class="container hero-shell">
                <div class="hero-grid">
                    <section>
                        <span class="eyebrow">Profil Sekolah</span>
                        <h1>{{ $schoolProfile['school_name'] }}</h1>
                        <p>{{ $schoolProfile['description'] }}</p>

                        <div class="hero-actions">
                            @if ($schoolProfile['email'] !== null)
                                <a class="btn btn-primary" href="mailto:{{ $schoolProfile['email'] }}">
                                    Hubungi via Email
                                </a>
                            @endif

                            <a class="btn btn-secondary" href="{{ $adminLoginUrl }}">Masuk Panel Admin</a>

                            @if ($schoolProfile['website'] !== null)
                                <a
                                    class="btn btn-secondary"
                                    href="{{ $schoolProfile['website'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Kunjungi Situs Utama
                                </a>
                            @endif
                        </div>
                    </section>

                    <aside class="profile-card" aria-label="Kontak Sekolah">
                        <h2>Kontak Utama</h2>
                        <dl class="meta-list">
                            <div class="meta-row">
                                <dt>Alamat</dt>
                                <dd>{{ $schoolProfile['address'] }}</dd>
                            </div>

                            @if ($schoolProfile['email'] !== null)
                                <div class="meta-row">
                                    <dt>Email</dt>
                                    <dd>
                                        <a href="mailto:{{ $schoolProfile['email'] }}">{{ $schoolProfile['email'] }}</a>
                                    </dd>
                                </div>
                            @endif

                            @if ($schoolProfile['phone'] !== null)
                                <div class="meta-row">
                                    <dt>Telepon</dt>
                                    <dd>{{ $schoolProfile['phone'] }}</dd>
                                </div>
                            @endif
                        </dl>

                        <div class="social-strip">
                            @if ($schoolProfile['facebook_url'] !== null)
                                <a href="{{ $schoolProfile['facebook_url'] }}" target="_blank" rel="noopener noreferrer">Facebook</a>
                            @endif
                            @if ($schoolProfile['instagram_url'] !== null)
                                <a href="{{ $schoolProfile['instagram_url'] }}" target="_blank" rel="noopener noreferrer">Instagram</a>
                            @endif
                            @if ($schoolProfile['youtube_url'] !== null)
                                <a href="{{ $schoolProfile['youtube_url'] }}" target="_blank" rel="noopener noreferrer">YouTube</a>
                            @endif
                        </div>
                    </aside>
                </div>
            </div>
        </main>

        <footer class="site-footer">
            <div class="container footer-shell">
                <p>
                    © {{ now()->year }} {{ $schoolProfile['school_name'] }}. Seluruh hak cipta dilindungi.
                </p>

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
