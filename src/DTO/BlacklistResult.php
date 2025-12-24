<?php

declare(strict_types=1);

namespace Milenmk\LaravelBlacklist\DTO;

final class BlacklistResult
{
    private function __construct(
        private bool $clean,
        private ?string $term = null,
        private ?string $list = null,
        private ?string $context = null,
    ) {}

    public static function clean(): self
    {
        return new self(true);
    }

    public static function blocked(
        string $term,
        string $list,
        ?string $context = null
    ): self {
        return new self(false, $term, $list, $context);
    }

    public function isClean(): bool
    {
        return $this->clean;
    }

    public function term(): ?string
    {
        return $this->term;
    }

    public function list(): ?string
    {
        return $this->list;
    }

    public function context(): ?string
    {
        return $this->context;
    }
}
