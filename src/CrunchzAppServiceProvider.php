<?php

namespace CrunchzApp;

use Illuminate\Support\ServiceProvider;

final class CrunchzAppServiceProvider extends ServiceProvider
{
    private const CONFIG_FILE = 'crunchzapp.php';
    private const CONFIG_TAG = 'crunchzapp-config';

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/' . self::CONFIG_FILE => config_path(self::CONFIG_FILE),
        ], self::CONFIG_TAG);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/' . self::CONFIG_FILE, 'crunchzapp');

        $this->app->singleton(CrunchzApp::class, function ($app) {
            return new CrunchzApp($app['config']['crunchzapp']['token']);
        });
    }
}
