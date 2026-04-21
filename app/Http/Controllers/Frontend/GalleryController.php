<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Modules\Galleries\Actions\FindPublishedGalleryBySlugAction;
use App\Modules\Galleries\Actions\ListPublishedGalleriesAction;
use App\Modules\Galleries\Actions\ResolvePublicGalleryMediaUrlAction;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Models\GalleryItem;
use App\Modules\NavigationMenus\Actions\ResolvePublicNavigationMenuAction;
use App\Modules\NavigationMenus\Support\NavigationMenuLocation;
use App\Modules\SchoolProfile\Actions\ResolvePublicSchoolProfileAction;
use App\Modules\ThemeSettings\Actions\ResolveActiveThemeSettingsAction;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;

class GalleryController extends Controller
{
    public function index(
        ResolveActiveThemeSettingsAction $resolveActiveThemeSettingsAction,
        ResolvePublicSchoolProfileAction $resolvePublicSchoolProfileAction,
        ResolvePublicNavigationMenuAction $resolvePublicNavigationMenuAction,
        ListPublishedGalleriesAction $listPublishedGalleriesAction,
        ResolvePublicGalleryMediaUrlAction $resolvePublicGalleryMediaUrlAction,
    ): View {
        /** @var LengthAwarePaginator<int, Gallery> $galleries */
        $galleries = $listPublishedGalleriesAction->execute()
            ->through(function (Gallery $gallery) use ($resolvePublicGalleryMediaUrlAction): array {
                $coverMedia = $resolvePublicGalleryMediaUrlAction->execute($gallery->items->first()?->mediaAsset);

                return [
                    'title' => (string) $gallery->title,
                    'slug' => (string) $gallery->slug,
                    'description' => (string) ($gallery->description ?? ''),
                    'published_at' => $gallery->published_at,
                    'author_name' => $gallery->author?->name ?? 'Tim Sekolah',
                    'items_count' => $gallery->items->count(),
                    'cover_url' => $coverMedia['url'],
                    'cover_alt' => $gallery->items->first()?->alt_text ?: $gallery->title,
                ];
            });

        return view('public.galleries.index', [
            ...$this->resolvePublicShellData(
                $resolveActiveThemeSettingsAction,
                $resolvePublicSchoolProfileAction,
                $resolvePublicNavigationMenuAction,
            ),
            'galleries' => $galleries,
            'fallbackImageUrl' => $resolvePublicGalleryMediaUrlAction->fallbackUrl(),
        ]);
    }

    public function show(
        string $slug,
        ResolveActiveThemeSettingsAction $resolveActiveThemeSettingsAction,
        ResolvePublicSchoolProfileAction $resolvePublicSchoolProfileAction,
        ResolvePublicNavigationMenuAction $resolvePublicNavigationMenuAction,
        FindPublishedGalleryBySlugAction $findPublishedGalleryBySlugAction,
        ResolvePublicGalleryMediaUrlAction $resolvePublicGalleryMediaUrlAction,
    ): View {
        $gallery = $findPublishedGalleryBySlugAction->execute($slug);

        abort_if($gallery === null, 404);

        $galleryItems = $gallery->items
            ->map(function (GalleryItem $item) use ($gallery, $resolvePublicGalleryMediaUrlAction): array {
                $media = $resolvePublicGalleryMediaUrlAction->execute($item->mediaAsset);

                return [
                    'id' => (int) $item->id,
                    'image_url' => $media['url'],
                    'caption' => (string) ($item->caption ?? ''),
                    'alt_text' => (string) ($item->alt_text ?? $gallery->title),
                ];
            })
            ->values();

        return view('public.galleries.show', [
            ...$this->resolvePublicShellData(
                $resolveActiveThemeSettingsAction,
                $resolvePublicSchoolProfileAction,
                $resolvePublicNavigationMenuAction,
            ),
            'gallery' => $gallery,
            'galleryItems' => $galleryItems,
            'fallbackImageUrl' => $resolvePublicGalleryMediaUrlAction->fallbackUrl(),
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
