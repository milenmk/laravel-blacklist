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
        $blacklist = Config::get('blacklist.blacklist', []);
        $errors = [];

        foreach ($fields as $fieldName => $fieldValue) {
            if (! is_string($fieldValue)) {
                continue;
            }

            foreach ($blacklist as $blacklistedTerm) {
                if (stripos($fieldValue, $blacklistedTerm) !== false) {
                    $errors[$fieldName] = "The {$fieldName} contains a blacklisted word: {$blacklistedTerm}";

                    $this->logBlacklistMatch($fieldName, $blacklistedTerm, $logChannel);

                    // Break the inner loop once we find a match for this field
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * Log a blacklist match.
     *
     * @param  string  $fieldName  The name of the field that matched
     * @param  string  $blacklistedTerm  The blacklisted term that was matched
     * @param  string|null  $channel  The log channel to use
     */
    protected function logBlacklistMatch(string $fieldName, string $blacklistedTerm, ?string $channel = null): void
    {
        $message = "An attempt to use {$fieldName} containing a blacklisted word: {{$blacklistedTerm}} detected";

        if ($channel) {
            Log::channel($channel)->warning($message);
        } else {
            Log::warning($message);
        }
    }
}
