<?php

namespace CrunchzApp;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider for the CrunchzApp SDK.
 *
 * This class is responsible for bootstrapping the SDK in a Laravel application.
 * It registers the configuration and the main CrunchzApp class in the service container.
 */
final class CrunchzAppServiceProvider extends ServiceProvider
{
    /**
     * The name of the configuration file.
     */
    private const CONFIG_FILE = 'crunchzapp.php';

    /**
     * The tag used for publishing the configuration file.
     */
    private const CONFIG_TAG = 'crunchzapp-config';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/' . self::CONFIG_FILE => config_path(self::CONFIG_FILE),
        ], self::CONFIG_TAG);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/' . self::CONFIG_FILE, 'crunchzapp');

        $this->app->singleton(CrunchzApp::class, function ($app) {
            return new CrunchzApp($app['config']['crunchzapp']['token']);
        });
    }
}
