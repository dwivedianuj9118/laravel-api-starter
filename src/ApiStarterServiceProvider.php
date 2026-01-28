<?php

namespace Dwivedianuj9118\ApiStarter;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Dwivedianuj9118\ApiStarter\Http\Middleware\ForceJsonResponse;
use Dwivedianuj9118\ApiStarter\Commands\InstallApiStarter;

class ApiStarterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/api-starter.php',
            'api-starter'
        );
    }

    public function boot(): void
    {
         $router = $this->app->make(Router::class);

        $router->aliasMiddleware('api.json', ForceJsonResponse::class);
        // Publish config files
        $this->publishes([
            __DIR__ . '/../config' => config_path(),
        ], 'api-starter-config');

        // Load API routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        // Register artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallApiStarter::class,
            ]);
        }
    }
}
