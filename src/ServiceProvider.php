<?php

namespace SportSpar\Grids;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $pkg_path = dirname(__DIR__);
        $views_path = $pkg_path . '/resources/views';

        $this->loadViewsFrom($views_path, 'grids');
        $this->loadTranslationsFrom($pkg_path . '/resources/lang', 'grids');
        $this->loadJsonTranslationsFrom($pkg_path . '/resources/lang');
        $this->publishes([
            $views_path => base_path('resources/views/vendor/grids')
        ]);

        if (!class_exists('Grids')) {
            class_alias('\\SportSpar\\Grids\\Grids', '\\Grids');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
