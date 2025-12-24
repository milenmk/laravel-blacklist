<?php

declare(strict_types=1);

namespace Milenmk\LaravelBlacklist\Tests\Feature;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Config;
use Milenmk\LaravelBlacklist\Services\BlacklistService;
use Milenmk\LaravelBlacklist\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;

class BlacklistServiceProviderTest extends BaseTest
{
    /**
     * Test that the service provider registers the BlacklistService as a singleton.
     *
     * @throws BindingResolutionException
     */
    #[Test]
    public function service_is_registered_as_singleton()
    {
        // Get two instances from the container
        $service1 = $this->app->make(BlacklistService::class);
        $service2 = $this->app->make(BlacklistService::class);

        // They should be the same instance
        $this->assertSame($service1, $service2);
    }

    /**
     * Test that the config is properly merged.
     */
    #[Test]
    public function config_is_properly_merged()
    {
        // Verify that the config values are available
        $this->assertNotNull(Config::get('blacklist'));
        $this->assertIsArray(Config::get('blacklist.blacklist'));
        $this->assertIsArray(Config::get('blacklist.profanity'));

        // Verify that the default mode is set
        $this->assertEquals('blacklist', Config::get('blacklist.mode'));
    }
}
