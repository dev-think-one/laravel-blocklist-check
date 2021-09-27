<?php

namespace LaraBlockList;


use LaraBlockList\Console\Commands\CheckForBlocklistCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/blocklist.php' => config_path('blocklist.php'),
            ], 'config');


            $this->commands([
                CheckForBlocklistCommand::class
            ]);
        }

    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/blocklist.php', 'blocklist');
    }
}
