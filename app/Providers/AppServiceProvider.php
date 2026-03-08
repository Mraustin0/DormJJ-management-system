<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Notification;
use App\Models\Bill;
use App\Models\Contract;
use App\Models\MoveoutRequest;

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
        // ─── Tenant views: inject notifications ───────────────────────────────
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

        // ─── Admin navbar: inject real notification data ───────────────────────
        View::composer('partials.navbar', function ($view) {
            if (!Auth::check() || Auth::user()->tenant_role) {
                $view->with([
                    'reviewingBills'  => collect(),
                    'endingContracts' => collect(),
                    'overdueBills'    => collect(),
                    'pendingMoveouts' => collect(),
                    'adminNotifCount' => 0,
                ]);
                return;
            }

            static $adminNotifData = null;
            if ($adminNotifData === null) {
                $reviewingBills  = Bill::with('room')->where('status', 'reviewing')->latest()->take(5)->get();
                $endingContracts = Contract::with('room')->where('status', 'ending')->orderBy('end_date')->take(5)->get();
                $overdueBills    = Bill::with('room')->where('status', 'overdue')->latest()->take(5)->get();
                $pendingMoveouts = MoveoutRequest::with('room')->where('status', 'pending')->latest()->take(5)->get();

                $adminNotifData = [
                    'reviewingBills'  => $reviewingBills,
                    'endingContracts' => $endingContracts,
                    'overdueBills'    => $overdueBills,
                    'pendingMoveouts' => $pendingMoveouts,
                    'adminNotifCount' => $reviewingBills->count() + $endingContracts->count()
                                      + $overdueBills->count() + $pendingMoveouts->count(),
                ];
            }

            $view->with($adminNotifData);
        });
    }
}
