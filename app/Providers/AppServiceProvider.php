<?php

namespace App\Providers;

use App\Models\ReceivePayment;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('admin.body.header', function ($view) {
            if (auth()->check() && auth()->user()->role === 'admin') {

                $pendingNotifications = ReceivePayment::with([
                    'users',
                    'category',
                ])
                    ->where('review_status', 'pending_review')
                    ->latest()
                    ->get();

                $view->with([
                    'pendingNotifications' => $pendingNotifications,
                    'pendingPaymentCount' => $pendingNotifications->count(),
                ]);
            }
        });
    }
}