<?php

namespace JPCaparas\LLamaparse;

use Illuminate\Support\ServiceProvider;

class LLamaparseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/llamaparse.php',
            'llamaparse'
        );

        $this->app->singleton('llamaparse', function ($app) {
            return new LLamaparseClient();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/llamaparse.php' => config_path('llamaparse.php'),
        ]);
    }
}
