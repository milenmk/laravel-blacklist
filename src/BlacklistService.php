<?php

declare(strict_types=1);

namespace Milenmk\LaravelBlacklist;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class BlacklistService
{
    /**
     * Check if any of the provided fields contain blacklisted terms.
     *
     * @param  array<string, string>  $fields  Associative array of field names and their values
     * @param  string|null  $logChannel  The log channel to use for logging blacklist matches
     * @return array<string, string> Array of validation errors, empty if no blacklisted terms found
     */
    public function checkFields(array $fields, ?string $logChannel = null): array
    {
        $mode = Config::get('blacklist.mode', 'blacklist');
        $terms = $this->getTermsBasedOnMode($mode);
        $errors = [];

        foreach ($fields as $fieldName => $fieldValue) {
            if (! is_string($fieldValue)) {
                continue;
            }

            foreach ($terms as $term) {
                // Check for whole word match using word boundaries in regex
                if (preg_match('/\b' . preg_quote($term, '/') . '\b/i', $fieldValue)) {
                    $listType = $this->getListTypeForTerm($term, $mode);
                    $errors[$fieldName] = "The $fieldName contains a $listType word: $term";

                    $this->logBlacklistMatch($fieldName, $term, $listType, $logChannel);

                    // Break the inner loop once we find a match for this field
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * Get the terms to check based on the configured mode.
     *
     * @param  string  $mode  The blacklist mode ('blacklist', 'profanity', or 'both')
     * @return array<string> Array of terms to check
     */
    protected function getTermsBasedOnMode(string $mode): array
    {
        $terms = [];

        if ($mode === 'blacklist' || $mode === 'both') {
            $terms = array_merge($terms, Config::get('blacklist.blacklist', []));
        }

        if ($mode === 'profanity' || $mode === 'both') {
            $terms = array_merge($terms, Config::get('blacklist.profanity', []));
        }

        return $terms;
    }

    /**
     * Determine which list a term belongs to.
     *
     * @param  string  $term  The term that was matched
     * @param  string  $mode  The current mode
     * @return string The type of list ('blacklisted' or 'profanity')
     */
    protected function getListTypeForTerm(string $term, string $mode): string
    {
        // If we're only using one list, we already know the type
        if ($mode === 'blacklist') {
            return 'blacklisted';
        }

        if ($mode === 'profanity') {
            return 'profanity';
        }

        // For 'both' mode, we need to check which list the term is in
        $blacklist = Config::get('blacklist.blacklist', []);

        return in_array($term, $blacklist) ? 'blacklisted' : 'profanity';
    }

    /**
     * Log a blacklist match.
     *
     * @param  string  $fieldName  The name of the field that matched
     * @param  string  $term  The term that was matched
     * @param  string  $listType  The type of list the term was found in ('blacklisted' or 'profanity')
     * @param  string|null  $channel  The log channel to use
     */
    protected function logBlacklistMatch(string $fieldName, string $term, string $listType, ?string $channel = null): void
    {
        $message = "An attempt to use $fieldName containing the $listType word: \"$term\" detected";

        if ($channel) {
            Log::channel($channel)->warning($message);
        } else {
            Log::warning($message);
        }
    }
}
