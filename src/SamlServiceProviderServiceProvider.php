<?php

namespace PhilWilliammee\SamlServiceProvider;

use Illuminate\Support\ServiceProvider;
use PhilWilliammee\SamlServiceProvider\Components\Login;
use Illuminate\Support\Facades\Blade;

class SamlServiceProviderServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'philwilliammee');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'samlserviceprovider');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadViewComponentsAs('samlserviceprovider', [
            Login::class,
        ]);

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    protected function configureComponents()
    {
        $this->callAfterResolving(BladeCompiler::class, function () {
            $this->registerComponent('login');
            // Register other components here
        });
    }

    protected function registerComponent(string $component)
    {
        Blade::component('samlserviceprovider::components.'.$component, 'samlserviceprovider-'.$component);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/samlserviceprovider.php', 'samlserviceprovider');

        // Register the service the package provides.
        $this->app->singleton('samlserviceprovider', function ($app) {
            return new SamlServiceProvider;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['samlserviceprovider'];
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
            __DIR__ . '/../config/samlserviceprovider.php' => config_path('samlserviceprovider.php'),
        ], 'samlserviceprovider.config');

        // Publishing the views.
        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/philwilliammee'),
        ], 'samlserviceprovider.views');

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/philwilliammee'),
        ], 'samlserviceprovider.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/philwilliammee'),
        ], 'samlserviceprovider.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
