<?php

declare(strict_types=1);

namespace Milenmk\LaravelBlacklist\Tests\Feature;

use Illuminate\Support\Facades\Config;
use Milenmk\LaravelBlacklist\BlacklistService;
use Milenmk\LaravelBlacklist\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;

class BlacklistModeTest extends BaseTest
{
    protected BlacklistService $blacklistService;

    protected function setUp(): void
    {
        parent::setUp();
        // Resolve from the container to test the service provider registration
        $this->blacklistService = $this->app->make(BlacklistService::class);
    }

    #[Test]
    public function blacklist_mode_only()
    {
        Config::set('blacklist.mode', 'blacklist');

        // Should detect blacklisted word
        $errors = $this->getFields();

        $this->assertEmpty($errors);
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        $fields = ['username' => 'admin_user'];
        $errors = $this->blacklistService->checkFields($fields);

        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('username', $errors);
        $this->assertStringContainsString('blacklisted word', $errors['username']);

        // Should not detect profanity word
        $fields = ['comment' => 'This is a damn good product'];

        return $this->blacklistService->checkFields($fields);
    }

    #[Test]
    public function profanity_mode_only()
    {
        Config::set('blacklist.mode', 'profanity');

        // Should detect profanity word
        $fields = ['comment' => 'This is a damn good product'];
        $errors = $this->blacklistService->checkFields($fields);

        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('comment', $errors);
        $this->assertStringContainsString('profanity word', $errors['comment']);

        // Should not detect blacklisted word
        $fields = ['username' => 'admin_user'];
        $errors = $this->blacklistService->checkFields($fields);

        $this->assertEmpty($errors);
    }

    #[Test]
    public function both_mode()
    {
        Config::set('blacklist.mode', 'both');

        // Should detect blacklisted word
        $errors = $this->getFields();

        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('comment', $errors);
        $this->assertStringContainsString('profanity word', $errors['comment']);
    }
}
