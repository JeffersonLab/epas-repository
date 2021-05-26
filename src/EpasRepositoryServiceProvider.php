<?php

namespace Jlab\EpasRepository;

use Illuminate\Support\ServiceProvider;

class EpasRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'jlab');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'jlab');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/epas-repository.php', 'epas-repository');

        // Register the service the package provides.
        $this->app->singleton('epas-repository', function ($app) {
            return new EpasRepository;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['epas-repository'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/epas-repository.php' => config_path('epas-repository.php'),
        ], 'epas-repository.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/jlab'),
        ], 'epas-repository.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/jlab'),
        ], 'epas-repository.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/jlab'),
        ], 'epas-repository.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
