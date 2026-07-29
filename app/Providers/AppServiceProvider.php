<?php

namespace App\Providers;

use App\Models\ReceivePayment;
use Illuminate\Support\Facades\View;
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
        View::composer(['admin.body.header', 'admin.dashboard'], function ($view) {
            if (auth()->check() && auth()->user()->role === 'admin') {
                $view->with(
                    'pendingPaymentCount',
                    ReceivePayment::where('review_status', 'pending_review')->count()
                );
            }
        });
    }
}
