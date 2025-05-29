<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use App\Guards\CookieJWTGuard;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\Contracts\Providers\Auth as JWTAuthProvider;

class AppServiceProvider extends ServiceProvider {
    public function register(): void {
        //
    }

    public function boot(): void {
        Auth::extend('cookie-jwt', function ($app, $name, array $config) {
            return new CookieJWTGuard(
                $app->make(JWT::class),
                $app->make(JWTAuthProvider::class),
                $app->make('request')
            );
        });

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        Paginator::useBootstrapFive();
    }
}
