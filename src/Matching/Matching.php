<?php

declare(strict_types=1);

namespace Milenmk\LaravelBlacklist\Matching;

final class Matching
{
    public static function match(string $term, string $value, array $config): bool
    {
        $strategy = $config['matching']
            ?? config('blacklist.default_matching', 'exact');

        return match ($strategy) {
            'fuzzy' => self::fuzzy(
                $term,
                $value,
                (int) ($config['threshold'] ?? 1)
            ),
            'substitution' => self::substitution($term, $value),
            default => self::exact($term, $value),
        };
    }

    private static function fuzzy(string $term, string $value, int $threshold): bool
    {
        foreach (preg_split('/\W+/', $value) as $word) {
            if (levenshtein(mb_strtolower($term), mb_strtolower($word)) <= $threshold) {
                return true;
            }
        }

        return false;
    }

    private static function substitution(string $term, string $value): bool
    {
        $map = [
            'a' => '[a@4]',
            'i' => '[i1!]',
            'e' => '[e3]',
            'o' => '[o0]',
            's' => '[s5$]',
        ];

        $pattern = '';
        foreach (mb_str_split($term) as $char) {
            $pattern .= $map[$char] ?? preg_quote($char, '/');
        }

        return preg_match('/' . $pattern . '/i', $value) === 1;
    }

    private static function exact(string $term, string $value): bool
    {
        return preg_match('/\b' . preg_quote($term, '/') . '\b/i', $value) === 1;
    }
}
