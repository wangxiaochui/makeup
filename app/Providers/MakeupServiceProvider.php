<?php

namespace App\Providers;

use App\Services\Makeup\Manger;
use Illuminate\Support\ServiceProvider;

class MakeupServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('makeup', function ($app) {
            return new Manger($app);
        });
    }
}
