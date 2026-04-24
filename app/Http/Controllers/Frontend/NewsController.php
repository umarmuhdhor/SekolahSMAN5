<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Modules\NavigationMenus\Actions\ResolvePublicNavigationMenuAction;
use App\Modules\NavigationMenus\Support\NavigationMenuLocation;
use App\Modules\News\Actions\FindPublishedNewsBySlugAction;
use App\Modules\News\Actions\ListPublishedNewsAction;
use App\Modules\SchoolProfile\Actions\ResolvePublicSchoolProfileAction;
use App\Modules\ThemeSettings\Actions\ResolveActiveThemeSettingsAction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

class NewsController extends Controller
{
    public function index(
        ResolveActiveThemeSettingsAction $resolveActiveThemeSettingsAction,
        ResolvePublicSchoolProfileAction $resolvePublicSchoolProfileAction,
        ResolvePublicNavigationMenuAction $resolvePublicNavigationMenuAction,
        ListPublishedNewsAction $listPublishedNewsAction,
    ): View {
        return view('public.news.index', [
            ...$this->resolvePublicShellData(
                $resolveActiveThemeSettingsAction,
                $resolvePublicSchoolProfileAction,
                $resolvePublicNavigationMenuAction,
            ),
            'newsItems' => $listPublishedNewsAction->execute(),
        ]);
    }

    public function show(
        string $slug,
        ResolveActiveThemeSettingsAction $resolveActiveThemeSettingsAction,
        ResolvePublicSchoolProfileAction $resolvePublicSchoolProfileAction,
        ResolvePublicNavigationMenuAction $resolvePublicNavigationMenuAction,
        FindPublishedNewsBySlugAction $findPublishedNewsBySlugAction,
    ): View {
        $news = $findPublishedNewsBySlugAction->execute($slug);

        abort_if($news === null, 404);

        return view('public.news.show', [
            ...$this->resolvePublicShellData(
                $resolveActiveThemeSettingsAction,
                $resolvePublicSchoolProfileAction,
                $resolvePublicNavigationMenuAction,
            ),
            'news' => $news,
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
