# Laravel Blacklist

A Laravel package for blacklist validation of user input. Includes both system blacklist words and profanity/offensive
terms, with the flexibility to choose which lists to use.

## Installation

You can install the package via composer:

```copy
composer require milenmk/laravel-blacklist
```

## Configuration

Publish the config file:

```copy
php artisan vendor:publish --tag=blacklist-config
```

This will create a `config/blacklist.php` file where you can:

1. Choose which word lists to use (system blacklist, profanity, or both)
2. Customize the blacklisted terms in each list

### Configuration Options

The package provides three modes for filtering content:

```php
// config/blacklist.php
return [
    // Choose which lists to use: 'blacklist', 'profanity', or 'both'
    'mode' => 'blacklist',
    
    // System blacklist words (usernames, reserved terms, etc.)
    'blacklist' => [
        'admin',
        'system',
        // ...
    ],
    
    // Profanity and offensive terms
    'profanity' => [
        // Common profanity words
        // ...
    ],
];
```

## Usage

```php
use Milenmk\LaravelBlacklist\BlacklistService;

class YourController
{
    protected BlacklistService $blacklistService;
    
    public function __construct(BlacklistService $blacklistService)
    {
        $this->blacklistService = $blacklistService;
    }
    
    public function store(Request $request)
    {
        // Validate request...
        
        // Check fields against blacklisted words
        $blacklistErrors = $this->blacklistService->checkFields([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            // Add any other fields you want to check
        ]);
        
        if (!empty($blacklistErrors)) {
            return redirect()->back()->withErrors($blacklistErrors);
        }
        
        // Continue with your logic...
    }
}
```

## Advanced Usage

### Custom Log Channel

You can specify a custom log channel:

```php
$blacklistErrors = $this->blacklistService->checkFields([
    'name' => $request->input('name'),
    'email' => $request->input('email'),
], 'security');
```

### Switching Modes

You can change the filtering mode in your config file:

```php
// config/blacklist.php
'mode' => 'blacklist', // Only check system blacklist words
// OR
'mode' => 'profanity', // Only check profanity/offensive words
// OR
'mode' => 'both',      // Check both lists
```

The error messages will indicate which list the matched term belongs to:

- "The {field} contains a blacklisted word: {term}"
- "The {field} contains a profanity word: {term}"

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.