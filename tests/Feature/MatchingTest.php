<?php

declare(strict_types=1);

namespace Milenmk\LaravelBlacklist\Tests\Feature;

use Milenmk\LaravelBlacklist\Services\BlacklistService;
use Milenmk\LaravelBlacklist\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;

class MatchingTest extends BaseTest
{
    #[Test]
    public function exact_matching_blocks_whole_words_only(): void
    {
        config()->set('blacklist.lists', [
            'system' => [
                'terms' => ['spam'],
                'matching' => 'exact',
            ],
        ]);

        $service = app(BlacklistService::class);

        $this->assertFalse(
            $service->checkValue('this is spam')->isClean()
        );

        $this->assertTrue(
            $service->checkValue('spammer')->isClean()
        );
    }

    #[Test]
    public function fuzzy_matching_allows_typos(): void
    {
        config()->set('blacklist.lists', [
            'profanity' => [
                'terms' => ['idiot'],
                'matching' => 'fuzzy',
                'threshold' => 1,
            ],
        ]);

        $service = app(BlacklistService::class);

        $this->assertFalse(
            $service->checkValue('idi0t')->isClean()
        );
    }

    #[Test]
    public function substitution_matching_detects_leetspeak(): void
    {
        config()->set('blacklist.lists', [
            'leet' => [
                'terms' => ['shit'],
                'matching' => 'substitution',
            ],
        ]);

        $service = app(BlacklistService::class);

        $this->assertFalse(
            $service->checkValue('sh1t')->isClean()
        );
    }
}
