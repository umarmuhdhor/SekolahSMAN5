# Auth Session Foundation (T006)

Dokumen ini menjelaskan baseline autentikasi session-based untuk panel admin CMS Sekolah.

## Cakupan T006
- Session-based authentication untuk admin panel Filament.
- Login throttling yang dapat dikonfigurasi.
- Listener event auth untuk pencatatan audit baseline.

## Komponen Utama
- Custom login page Filament:
  - `app/Filament/Auth/Pages/Login.php`
- Action pencatatan audit auth:
  - `app/Modules/Auth/Actions/RecordAuthEventAction.php`
- Listener auth events:
  - `app/Listeners/Auth/RecordLoginEvent.php`
  - `app/Listeners/Auth/RecordLogoutEvent.php`
  - `app/Listeners/Auth/RecordFailedLoginEvent.php`
- Event provider:
  - `app/Providers/EventServiceProvider.php`

## Konfigurasi
- `config/auth_session.php`
  - `login_max_attempts`
  - `login_decay_seconds`
  - `audit_log_channel`

## Variabel Environment Baru
- `AUTH_LOGIN_MAX_ATTEMPTS`
- `AUTH_LOGIN_DECAY_SECONDS`
- `AUTH_AUDIT_LOG_CHANNEL`
- `SESSION_SECURE_COOKIE`
- `SESSION_HTTP_ONLY`
- `SESSION_SAME_SITE`

## Catatan Auditability
- Pada T006, event auth dicatat sebagai baseline.
- Sejak T009, event auth utama juga tercatat pada tabel audit terstruktur `audit_logs`.
- Channel `auth_audit` tetap tersedia sebagai log operasional tambahan.

## Acceptance Mapping
- Login/logout session admin: aktif melalui Filament panel.
- Failed login handling: aktif + throttling login.
- Auth event baseline: login, failed_login, logout tercatat secara terstruktur.
