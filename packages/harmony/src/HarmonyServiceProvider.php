<?php

namespace Harmony;

use Harmony\Console\MakeHarmonyCommand;
use Illuminate\Support\ServiceProvider;

class HarmonyServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Command
        $this->commands([
            MakeHarmonyCommand::class,
        ]);
    }
}