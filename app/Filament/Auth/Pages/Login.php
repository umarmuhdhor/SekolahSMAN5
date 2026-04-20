<?php

declare(strict_types=1);

namespace App\Filament\Auth\Pages;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    /**
     * Menyesuaikan throttling autentikasi admin berdasarkan konfigurasi proyek.
     *
     * Konteks:
     * - Halaman login Filament secara default memanggil rateLimit(5) di metode authenticate.
     * - CMS Sekolah membutuhkan batas throttling yang eksplisit dan mudah dikonfigurasi.
     *
     * Parameter:
     *
     * @param  mixed  $maxAttempts  Batas percobaan dari pemanggil; akan dioverride untuk authenticate.
     * @param  mixed  $decaySeconds  Periode decay rate limiter dalam detik.
     * @param  mixed  $method  Nama metode yang dirate-limit.
     * @param  mixed  $component  Nama komponen Livewire.
     *
     * Output:
     * - Mewariskan perilaku rate limiting dari trait Filament dengan nilai konfigurasi khusus.
     *
     * Validasi penting:
     * - Nilai percobaan minimal dipaksa >= 1.
     * - Nilai decay minimal dipaksa >= 1 detik.
     *
     * Error cases:
     * - Jika limit terlampaui, exception TooManyRequestsException tetap dilempar oleh parent.
     *
     * Side effects:
     * - Menulis hit rate limiter untuk metode authenticate.
     *
     * Dependency:
     * - config/auth_session.php
     */
    protected function rateLimit($maxAttempts, $decaySeconds = 60, $method = null, $component = null)
    {
        $resolvedMethod = $method ?? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? null;

        if ($resolvedMethod === 'authenticate') {
            $maxAttempts = max((int) config('auth_session.login_max_attempts', 5), 1);
            $decaySeconds = max((int) config('auth_session.login_decay_seconds', 60), 1);
        }

        return parent::rateLimit($maxAttempts, $decaySeconds, $method, $component);
    }
}
