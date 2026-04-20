<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Modules\RolesPermissions\Support\AuthorizationAbility;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function canAccess(): bool
    {
        return auth()->user()?->can(AuthorizationAbility::PANEL_DASHBOARD_VIEW) ?? false;
    }
}
