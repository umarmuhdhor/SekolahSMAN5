<?php

declare(strict_types=1);

namespace Database\Seeders\RolesPermissions;

use App\Modules\RolesPermissions\Support\PermissionName;
use App\Modules\RolesPermissions\Support\RoleName;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Menyiapkan role dan permission baseline CMS Sekolah sesuai master plan.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionName::all() as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superAdminRole = Role::findOrCreate(RoleName::SUPER_ADMIN, 'web');
        $guruRole = Role::findOrCreate(RoleName::GURU, 'web');
        $siswaRole = Role::findOrCreate(RoleName::SISWA, 'web');
        $orangTuaRole = Role::findOrCreate(RoleName::ORANG_TUA, 'web');

        $superAdminRole->syncPermissions(PermissionName::all());
        $guruRole->syncPermissions(PermissionName::guruDefaults());
        $siswaRole->syncPermissions([]);
        $orangTuaRole->syncPermissions([]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
