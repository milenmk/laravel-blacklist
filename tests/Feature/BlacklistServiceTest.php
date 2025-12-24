<?php

declare(strict_types=1);

namespace Milenmk\LaravelBlacklist\Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Milenmk\LaravelBlacklist\Services\BlacklistService;
use Milenmk\LaravelBlacklist\Tests\BaseTest;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class BlacklistServiceTest extends BaseTest
{
    protected BlacklistService $blacklistService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->blacklistService = $this->app->make(BlacklistService::class);
    }

    protected function tearDown(): void
    {
        // Make sure Mockery expectations are verified and reset
        if (class_exists(Mockery::class)) {
            Mockery::close();
        }

        parent::tearDown();
    }

    /**
     * Test that non-string values are skipped.
     */
    #[Test]
    public function non_string_values_are_skipped()
    {
        // First, let's temporarily disable the blacklist check by setting an empty blacklist
        Config::set('blacklist.blacklist', []);
        Config::set('blacklist.profanity', []);

        $fields = [
            'username' => 'valid_person', // Using a name that doesn't contain blacklisted words
            'age' => 25, // This is an integer and should be skipped
            'is_admin' => true, // This is a boolean and should be skipped
            'settings' => ['theme' => 'dark'], // This is an array and should be skipped
        ];

        $errors = $this->blacklistService->checkFields($fields);
        $this->assertEmpty($errors);
    }

    /**
     * Test that logging works with a custom channel.
     */
    #[Test]
    public function logging_with_custom_channel()
    {
        try {
            // Mock the Log facade
            Log::shouldReceive('channel')
                ->once()
                ->with('custom_channel')
                ->andReturnSelf();

            Log::shouldReceive('warning')
                ->once()
                ->with(Mockery::pattern('/An attempt to use username containing the blacklisted word: "adm" detected/'));

            Config::set('blacklist.mode', 'blacklist');

            $fields = ['username' => 'adm user'];  // Using a space to ensure it's a whole word
            $errors = $this->blacklistService->checkFields($fields, 'custom_channel');

            // Add an explicit assertion to prevent PHPUnit from marking the test as risky
            $this->assertArrayHasKey('username', $errors, 'The username field should be flagged as containing a blacklisted word');
        } finally {
            // Ensure Mockery expectations are verified and reset
            Mockery::close();
        }
    }

    /**
     * Test that logging works with the default channel.
     */
    #[Test]
    public function logging_with_default_channel()
    {
        try {
            // Mock the Log facade
            Log::shouldReceive('warning')
                ->once()
                ->with(Mockery::pattern('/An attempt to use username containing the blacklisted word: "adm" detected/'));

            Config::set('blacklist.mode', 'blacklist');

            $fields = ['username' => 'adm user'];  // Using a space to ensure it's a whole word
            $errors = $this->blacklistService->checkFields($fields);

            // Add an explicit assertion to prevent PHPUnit from marking the test as risky
            $this->assertArrayHasKey('username', $errors, 'The username field should be flagged as containing a blacklisted word');
        } finally {
            // Ensure Mockery expectations are verified and reset
            Mockery::close();
        }
    }
}
