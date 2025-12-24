<?php

declare(strict_types=1);

namespace Milenmk\LaravelBlacklist\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Milenmk\LaravelBlacklist\Services\BlacklistService;

final class BlacklistRule implements ValidationRule
{
    public function __construct(
        protected ?string $context = null
    ) {}

    /**
     * @param  Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $result = app(BlacklistService::class)
            ->checkValue($value, $this->context ?? $attribute);

        if (! $result->isClean()) {
            $fail(__('blacklist::validation.blocked', [
                'attribute' => $attribute,
                'term' => $result->term(),
                'list' => $result->list(),
            ]));
        }
    }
}
