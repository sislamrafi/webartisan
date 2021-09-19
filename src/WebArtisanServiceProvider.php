<?php

namespace Sislamrafi\Webartisan;

use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Sislamrafi\Webartisan\App\Http\Middleware\PreventRequestsDuringMaintenance;

class WebArtisanServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //$kernel = $this->app->make(Kernel::class);
        //$kernel->pushMiddleware(PreventRequestsDuringMaintenance::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
              __DIR__.'/config/app.php' => config_path('webartisan.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/public' => public_path('vendor/sislamrafi/webartisan'),
            ], 'public');
        }
        $this->commandRegister();

        $this->registerRoutes();
        $this->loadViewsFrom(__DIR__.'/resources/views', 'sislamrafi.webartisan');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/app.php', 'webartisan');
    }

    protected function registerRoutes()
    {
        Route::middleware('web')
                ->namespace('Sislamrafi\Webartisan')
                ->group(__DIR__.'/routes/web.php');
        Route::prefix('api')
                ->middleware('api')
                ->namespace('Sislamrafi\Webartisan')
                ->group(__DIR__.'/routes/api.php');
    }

    protected function commandRegister(){
        $this->commands([
            \Sislamrafi\Webartisan\App\Console\Commands\ChangeEnv::class,
        ]);
    }
}
