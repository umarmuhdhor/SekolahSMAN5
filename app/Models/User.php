<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Modules\RolesPermissions\Support\AuthorizationAbility;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;
    use Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Menentukan apakah user diizinkan mengakses panel Filament tertentu.
     *
     * Konteks:
     * - Fondasi T007 membatasi akses panel admin hanya untuk role operasional CMS.
     * - Role `siswa` dan `orang_tua` tetap ada sebagai role sistem, tetapi tidak memiliki akses panel admin.
     *
     * Parameter:
     *
     * @param  Panel  $panel  Panel Filament yang sedang diakses.
     *
     * Return:
     * - `true` jika gate `panel.access` mengizinkan akses.
     * - `false` selain kondisi di atas.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() !== 'admin') {
            return false;
        }

        if (! $this->is_active) {
            return false;
        }

        return $this->can(AuthorizationAbility::PANEL_ACCESS);
    }
}
