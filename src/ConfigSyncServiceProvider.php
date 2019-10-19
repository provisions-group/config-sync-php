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

        //this won't be provided it will just be loaded directly from the vendor table
        // $this->publishes([
        //     __DIR__.'/EnvSecureHelper.php' => app_path('/Helpers/EnvSecureHelper.php'),
        // ]);

        $this->mergeConfigFrom(
            __DIR__.'/config/config-sync.php', 'config-sync'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                ConfigSync::class,
                UpdateConfigsForSecureEnv::class
            ]);
        }
    }
}
