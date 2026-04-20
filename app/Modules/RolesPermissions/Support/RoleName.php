<?php

declare(strict_types=1);

namespace App\Modules\RolesPermissions\Support;

final class RoleName
{
    public const SUPER_ADMIN = 'super_admin';

    public const GURU = 'guru';

    public const SISWA = 'siswa';

    public const ORANG_TUA = 'orang_tua';

    /**
     * Mengembalikan daftar role wajib CMS Sekolah sesuai master plan.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::SUPER_ADMIN,
            self::GURU,
            self::SISWA,
            self::ORANG_TUA,
        ];
    }

    /**
     * Mengembalikan role yang diizinkan mengakses panel admin CMS.
     *
     * @return array<int, string>
     */
    public static function adminPanelRoles(): array
    {
        return [
            self::SUPER_ADMIN,
            self::GURU,
        ];
    }
}
