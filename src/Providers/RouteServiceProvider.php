<?php

namespace IwslibLaravel\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('web')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
            Route::middleware('api')
                ->prefix('api-from-kintone')
                ->group(base_path('routes/apiFromKintone.php'));

            Route::middleware('web')
                ->group(__DIR__ . '/../routes/web.php');
        });
    }
}
