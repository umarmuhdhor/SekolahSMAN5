<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Modules\NavigationMenus\Actions\ResolvePublicNavigationMenuAction;
use App\Modules\NavigationMenus\Support\NavigationMenuLocation;
use App\Modules\SchoolProfile\Actions\ResolvePublicSchoolProfileAction;
use App\Modules\ThemeSettings\Actions\ResolveActiveThemeSettingsAction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{
    public function __invoke(
        ResolveActiveThemeSettingsAction $resolveActiveThemeSettingsAction,
        ResolvePublicSchoolProfileAction $resolvePublicSchoolProfileAction,
        ResolvePublicNavigationMenuAction $resolvePublicNavigationMenuAction,
    ): View {
        return view('public.home', [
            'theme' => $resolveActiveThemeSettingsAction->execute(),
            'schoolProfile' => $resolvePublicSchoolProfileAction->execute(),
            'headerNavigation' => $resolvePublicNavigationMenuAction->execute(NavigationMenuLocation::HEADER),
            'footerNavigation' => $resolvePublicNavigationMenuAction->execute(NavigationMenuLocation::FOOTER),
            'adminLoginUrl' => Route::has('filament.admin.auth.login')
                ? route('filament.admin.auth.login')
                : url('/admin/login'),
        ]);
    }
}
