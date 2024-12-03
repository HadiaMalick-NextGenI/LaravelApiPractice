<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /** @var string $apiNamespace */
    protected $apiNamespace ='App\Http\Controllers\Api';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::group([
                'middleware' => ['api', 'api_version:v1'],
                'namespace'  => "{$this->apiNamespace}\V1",
                'prefix'     => 'api/v1',
            ], function ($router) {
                require base_path('routes/v1/api.php');
            });

            Route::group([
                'middleware' => ['api', 'api_version:v2'],
                'namespace'  => "{$this->apiNamespace}\V2",
                'prefix'     => 'api/v2',
            ], function ($router) {
                require base_path('routes/v2/api.php');
            });
        });
    }
}
