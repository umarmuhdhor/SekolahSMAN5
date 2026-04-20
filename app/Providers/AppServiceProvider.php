<?php

namespace App\Providers;

use App\Models\User;
use App\Modules\Announcements\Models\Announcement;
use App\Modules\Announcements\Observers\AnnouncementAuditObserver;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Models\GalleryItem;
use App\Modules\Galleries\Observers\GalleryAuditObserver;
use App\Modules\Galleries\Observers\GalleryItemAuditObserver;
use App\Modules\MediaLibrary\Models\MediaAsset;
use App\Modules\MediaLibrary\Observers\MediaAssetAuditObserver;
use App\Modules\NavigationMenus\Models\NavigationItem;
use App\Modules\NavigationMenus\Models\NavigationMenu;
use App\Modules\NavigationMenus\Observers\NavigationItemAuditObserver;
use App\Modules\NavigationMenus\Observers\NavigationMenuAuditObserver;
use App\Modules\News\Models\News;
use App\Modules\News\Observers\NewsAuditObserver;
use App\Modules\SchoolProfile\Models\SchoolProfile;
use App\Modules\SchoolProfile\Observers\SchoolProfileAuditObserver;
use App\Modules\ThemeSettings\Models\ThemeSetting;
use App\Modules\ThemeSettings\Observers\ThemeSettingAuditObserver;
use App\Modules\Users\Observers\UserAuditObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserAuditObserver::class);
        MediaAsset::observe(MediaAssetAuditObserver::class);
        News::observe(NewsAuditObserver::class);
        Announcement::observe(AnnouncementAuditObserver::class);
        Gallery::observe(GalleryAuditObserver::class);
        GalleryItem::observe(GalleryItemAuditObserver::class);
        SchoolProfile::observe(SchoolProfileAuditObserver::class);
        ThemeSetting::observe(ThemeSettingAuditObserver::class);
        NavigationMenu::observe(NavigationMenuAuditObserver::class);
        NavigationItem::observe(NavigationItemAuditObserver::class);
    }
}
