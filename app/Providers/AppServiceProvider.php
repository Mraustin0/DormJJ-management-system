<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Notification;

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
        // Inject notifications into all tenant views (query runs once per request via static cache)
        View::composer('tenant.*', function ($view) {
            static $notifications = null;

            if ($notifications === null) {
                $notifications = Auth::check()
                    ? Notification::forUser(Auth::id())->latest()->take(20)->get()
                    : collect();
            }

            $view->with('notifications', $notifications);
            $view->with('unreadCount', $notifications->where('is_read', false)->count());
        });
    }
}
