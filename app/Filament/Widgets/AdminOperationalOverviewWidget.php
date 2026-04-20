<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\User;
use App\Modules\RolesPermissions\Support\AuthorizationAbility;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminOperationalOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return auth()->user()?->can(AuthorizationAbility::PANEL_DASHBOARD_VIEW) ?? false;
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $totalUsers = User::query()->count();
        $activeUsers = User::query()->where('is_active', true)->count();
        $inactiveUsers = User::query()->where('is_active', false)->count();

        return [
            Stat::make('Total User', (string) $totalUsers)
                ->description('Seluruh akun pada sistem.')
                ->color('primary'),
            Stat::make('User Aktif', (string) $activeUsers)
                ->description('Akun yang dapat login ke panel.')
                ->color('success'),
            Stat::make('User Nonaktif', (string) $inactiveUsers)
                ->description('Akun yang sementara dinonaktifkan.')
                ->color($inactiveUsers > 0 ? 'warning' : 'gray'),
            Stat::make('Role Terdaftar', (string) Role::query()->count())
                ->description('Jumlah role aktif pada sistem.')
                ->color('info'),
            Stat::make('Permission Terdaftar', (string) Permission::query()->count())
                ->description('Jumlah permission baseline.')
                ->color('gray'),
        ];
    }
}
