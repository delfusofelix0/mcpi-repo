<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);
        DB::prohibitDestructiveCommands(
            app()->isProduction()
        );

        RedirectIfAuthenticated::redirectUsing(function ($request) {
            if (Auth::check()) {
                $user = Auth::user();

                \Log::info('RedirectIfAuthenticated callback triggered', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'roles' => $user->getRoleNames()->toArray()
                ]);

                if ($user->hasRole('Secretary')) {
                    return route('appointment-settings.index');
                } elseif ($user->hasRole('Cashier')) {
                    return route('cashier.dashboard');
                } elseif ($user->hasRole('Accounting')) {
                    return route('accounting.dashboard');
                } elseif ($user->hasRole('Registrar')) {
                    return route('registrar.dashboard');
                }

                return route('dashboard');
            }

            return null;
        });
    }
}

