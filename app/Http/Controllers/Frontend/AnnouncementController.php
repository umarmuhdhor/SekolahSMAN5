<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Modules\Announcements\Actions\FindVisibleAnnouncementBySlugAction;
use App\Modules\Announcements\Actions\ListVisibleAnnouncementsAction;
use App\Modules\NavigationMenus\Actions\ResolvePublicNavigationMenuAction;
use App\Modules\NavigationMenus\Support\NavigationMenuLocation;
use App\Modules\SchoolProfile\Actions\ResolvePublicSchoolProfileAction;
use App\Modules\ThemeSettings\Actions\ResolveActiveThemeSettingsAction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

class AnnouncementController extends Controller
{
    public function index(
        ResolveActiveThemeSettingsAction $resolveActiveThemeSettingsAction,
        ResolvePublicSchoolProfileAction $resolvePublicSchoolProfileAction,
        ResolvePublicNavigationMenuAction $resolvePublicNavigationMenuAction,
        ListVisibleAnnouncementsAction $listVisibleAnnouncementsAction,
    ): View {
        return view('public.announcements.index', [
            ...$this->resolvePublicShellData(
                $resolveActiveThemeSettingsAction,
                $resolvePublicSchoolProfileAction,
                $resolvePublicNavigationMenuAction,
            ),
            'announcements' => $listVisibleAnnouncementsAction->execute(),
        ]);
    }

    public function show(
        string $slug,
        ResolveActiveThemeSettingsAction $resolveActiveThemeSettingsAction,
        ResolvePublicSchoolProfileAction $resolvePublicSchoolProfileAction,
        ResolvePublicNavigationMenuAction $resolvePublicNavigationMenuAction,
        FindVisibleAnnouncementBySlugAction $findVisibleAnnouncementBySlugAction,
    ): View {
        $announcement = $findVisibleAnnouncementBySlugAction->execute($slug);

        abort_if($announcement === null, 404);

        return view('public.announcements.show', [
            ...$this->resolvePublicShellData(
                $resolveActiveThemeSettingsAction,
                $resolvePublicSchoolProfileAction,
                $resolvePublicNavigationMenuAction,
            ),
            'announcement' => $announcement,
        ]);
    }

    /**
     * @return array{
     *     theme: array<string, mixed>,
     *     schoolProfile: array<string, mixed>,
     *     headerNavigation: array<string, mixed>,
     *     footerNavigation: array<string, mixed>,
     *     adminLoginUrl: string
     * }
     */
    private function resolvePublicShellData(
        ResolveActiveThemeSettingsAction $resolveActiveThemeSettingsAction,
        ResolvePublicSchoolProfileAction $resolvePublicSchoolProfileAction,
        ResolvePublicNavigationMenuAction $resolvePublicNavigationMenuAction,
    ): array {
        return [
            'theme' => $resolveActiveThemeSettingsAction->execute(),
            'schoolProfile' => $resolvePublicSchoolProfileAction->execute(),
            'headerNavigation' => $resolvePublicNavigationMenuAction->execute(NavigationMenuLocation::HEADER),
            'footerNavigation' => $resolvePublicNavigationMenuAction->execute(NavigationMenuLocation::FOOTER),
            'adminLoginUrl' => Route::has('filament.admin.auth.login')
                ? route('filament.admin.auth.login')
                : url('/admin/login'),
        ];
    }
}
