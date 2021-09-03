<?php

namespace Sislamrafi\Webartisan;

use Illuminate\Support\ServiceProvider;

class WebArtisanServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {   
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }
}