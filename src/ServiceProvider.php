<?php

namespace JacobBennett\Http2ServerPush;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot()
	{
        $this->mergeConfigFrom(__DIR__.'/config.php', 'http2serverpush');

        // Register paths to be published by 'vendor:publish' Artisan command
        $this->publishes([
            __DIR__ . '/config.php' => config_path('http2serverpush.php'),
        ], 'config');
	}

}
