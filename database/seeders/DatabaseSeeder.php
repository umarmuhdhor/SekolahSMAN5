<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $superAdmin = User::query()->firstOrCreate([
            'email' => env('SEED_SUPER_ADMIN_EMAIL', 'superadmin@sekolah.local'),
        ], [
            'name' => env('SEED_SUPER_ADMIN_NAME', 'Super Admin CMS Sekolah'),
            'password' => Hash::make(env('SEED_SUPER_ADMIN_PASSWORD', 'password')),
        ]);

        if (! $superAdmin->hasRole(RoleName::SUPER_ADMIN)) {
            $superAdmin->assignRole(RoleName::SUPER_ADMIN);
        }
    }
}
