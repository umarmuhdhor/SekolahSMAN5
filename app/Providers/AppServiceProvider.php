<?php

namespace App\Providers;

use App\Models\User;
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
    }
}
