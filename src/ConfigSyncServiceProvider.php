<?php

namespace CashExpress\ConfigSync;

use Illuminate\Support\ServiceProvider;

class ConfigSyncServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/config-sync.php' => config_path('config-sync.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ConfigSync::class,
            ]);
        }
    }
}
