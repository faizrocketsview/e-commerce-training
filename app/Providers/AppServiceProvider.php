<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Harmony\Harmony;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
            $this->app->bind('harmony',function(){
                    return new Harmony();
            });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
