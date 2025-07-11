<?php

namespace CrunchzApp;

use Illuminate\Support\ServiceProvider;

/**
 * CrunchzApp Laravel Service Provider
 * 
 * This service provider handles the registration and configuration
 * of the CrunchzApp SDK within Laravel applications.
 * 
 * @package CrunchzApp
 * @version 1.0.0
 */
final class CrunchzAppServiceProvider extends ServiceProvider
{
    /**
     * Configuration file name
     */
    private const CONFIG_FILE = 'crunchzapp.php';
    
    /**
     * Configuration publish tag
     */
    private const CONFIG_TAG = 'crunchzapp-config';
    
    /**
     * Bootstrap any package services
     * 
     * @return void
     */
    public function boot(): void
    {
        $this->publishConfiguration();
        $this->mergeConfiguration();
    }

    /**
     * Register any package services
     * 
     * @return void
     */
    public function register(): void
    {
        $this->registerCrunchzAppSingleton();
    }
    
    /**
     * Publish the configuration file
     * 
     * @return void
     */
    private function publishConfiguration(): void
    {
        $this->publishes([
            $this->getConfigPath() => config_path(self::CONFIG_FILE)
        ], self::CONFIG_TAG);
    }
    
    /**
     * Merge the package configuration with the application's published copy
     * 
     * @return void
     */
    private function mergeConfiguration(): void
    {
        $this->mergeConfigFrom(
            $this->getConfigPath(),
            'crunchzapp'
        );
    }
    
    /**
     * Register CrunchzApp as a singleton in the container
     * 
     * @return void
     */
    private function registerCrunchzAppSingleton(): void
    {
        $this->app->singleton(CrunchzApp::class, function () {
            return new CrunchzApp();
        });
    }
    
    /**
     * Get the configuration file path
     * 
     * @return string
     */
    private function getConfigPath(): string
    {
        return __DIR__ . '/../config/' . self::CONFIG_FILE;
    }
}
