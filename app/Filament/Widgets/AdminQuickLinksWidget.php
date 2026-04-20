<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Modules\Dashboard\Actions\GetDashboardQuickLinksAction;
use App\Modules\RolesPermissions\Support\AuthorizationAbility;
use Filament\Widgets\Widget;

class AdminQuickLinksWidget extends Widget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.admin-quick-links-widget';

    public static function canView(): bool
    {
        return auth()->user()?->can(AuthorizationAbility::PANEL_DASHBOARD_VIEW) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = auth()->user();

        if (! $user) {
            return ['links' => []];
        }

        return [
            'links' => app(GetDashboardQuickLinksAction::class)->execute($user),
        ];
    }
}
