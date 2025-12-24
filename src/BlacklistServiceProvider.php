<?php

declare(strict_types=1);

namespace Milenmk\LaravelBlacklist;

use Illuminate\Support\ServiceProvider;
use Milenmk\LaravelBlacklist\Http\Middleware\BlacklistMiddleware;
use Milenmk\LaravelBlacklist\Services\BlacklistService;

class BlacklistServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/blacklist.php', 'blacklist'
        );

        $this->app->singleton(BlacklistService::class, function ($app) {
            return new BlacklistService;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/blacklist.php' => config_path('blacklist.php'),
        ], 'blacklist-config');

        $this->app['router']->aliasMiddleware(
            'blacklist',
            BlacklistMiddleware::class
        );

        $this->loadTranslationsFrom(
            __DIR__ . '/../lang',
            'blacklist'
        );

        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/blacklist'),
        ], 'blacklist-translations');
    }
}
