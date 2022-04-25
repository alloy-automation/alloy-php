<?php

namespace Alloy\Client;

use Illuminate\Support\ServiceProvider;

class AlloyServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/alloyConfig.php' => config_path('alloyConfig.php')
        ]);
    }

    public function register()
    {
        $this->app->singleton(Quickmetrics::class, function() {
            return new Quickmetrics(config('alloyConfig.key'));
        });
    }

}