<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Login Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Konfigurasi limit percobaan login admin (session-based) untuk panel CMS.
    | Nilai ini digunakan oleh custom login page Filament di T006.
    |
    */

    'login_max_attempts' => env('AUTH_LOGIN_MAX_ATTEMPTS', 5),

    'login_decay_seconds' => env('AUTH_LOGIN_DECAY_SECONDS', 60),

    /*
    |--------------------------------------------------------------------------
    | Auth Audit Log Channel
    |--------------------------------------------------------------------------
    |
    | Channel log operasional tambahan untuk event auth.
    | Audit utama tetap dicatat pada tabel `audit_logs`.
    |
    */

    'audit_log_channel' => env('AUTH_AUDIT_LOG_CHANNEL', 'auth_audit'),
];
