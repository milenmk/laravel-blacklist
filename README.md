# Laravel Blacklist

<p style="display: flex; justify-content: center; gap: 8px;">
    <a href="https://packagist.org/packages/milenmk/laravel-blacklist" target="_blank">
        <img src="https://img.shields.io/packagist/v/milenmk/laravel-blacklist.svg?style=flat-square" alt="Latest Version on Packagist" />
    </a>
    <a href="https://packagist.org/packages/milenmk/laravel-blacklist" target="_blank">
        <img src="https://img.shields.io/packagist/dt/milenmk/laravel-blacklist.svg?style=flat-square" alt="Total Downloads" />
    </a>
    <a href="https://github.com/milenmk/laravel-blacklist" target="_blank">
        <img alt="GitHub User's stars" src="https://img.shields.io/github/stars/milenmk/laravel-blacklist">
    </a>
    <a href="https://laravel.com/docs" target="_blank">
        <img src="https://img.shields.io/badge/Laravel-10.x-orange?style=flat-square&logo=laravel" alt="Laravel 10 Support" />
    </a>
    <a href="https://www.php.net" target="_blank">
        <img src="https://img.shields.io/packagist/php-v/milenmk/laravel-blacklist?style=flat-square" alt="PHP Version Support" />
    </a>
    <a href="https://github.com/milenmk/laravel-blacklist/blob/develop/LICENSE.md" target="_blank">
        <img src="https://img.shields.io/packagist/l/milenmk/laravel-blacklist.svg?style=flat-square" alt="License" />
    </a>
    <a href="https://github.com/milenmk/laravel-blacklist/issues" target="_blank">
        <img src="https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat-square" alt="Contributions Welcome" />
    </a>
    <a href="https://www.patreon.com/c/LaravelAddonsbyMilen" target="_blank">
        <img src="https://img.shields.io/badge/Sponsor-%E2%9D%A4-ff69b4?style=flat-square" alt="Sponsor me" />
    </a>
</p>

Laravel Blacklist: A robust content filtering solution for Laravel applications that provides comprehensive validation
against unwanted user input. This package offers dual-layer protection with both system blacklist words (preventing
username squatting and system impersonation) and profanity/offensive terms filtering.

## Key features:

- Intelligent word boundary matching to prevent false positives while catching problematic content
- Flexible filtering modes: use system blacklist only, profanity filtering only, or both simultaneously
- Customizable word lists that can be easily extended or modified via configuration
- Whole Word Matching: Utilizes whole-word boundary detection to avoid misclassifying similar substrings.
  For example, "admin" will match "admin user" but will not flag "administrator" or "badminton."
  Likewise, "damn" will trigger in "that's damn good" but not in "condemnation."
- Detailed error messages that specify which list triggered the validation failure: the users receive context-aware
  validation feedback, such as:
    - The {field} contains the blacklisted word: "{term}"
    - The {field} contains the profanity word: "{term}"
- Built-in security logging with support for custom log channels
- Simple integration with Laravel controllers, Livewire components, and forms
- Zero dependencies beyond Laravel itself
- Thoroughly tested with comprehensive test coverage
- Optional Logging Support: You can pass a custom log channel to capture violations or auditing—for example, logging
  under security—for maintainability or monitoring.

Perfect for applications requiring content moderation, user registration systems, comment sections, or any
user-generated content that needs protection against inappropriate language or system term abuse.

## Installation

You can install the package via composer:

```copy
composer require milenmk/laravel-blacklist
```

## Configuration

The package works out of the box with default settings, but you can customize it by publishing the config file:

```copy
php artisan vendor:publish --tag=blacklist-config
```

This will create a `config/blacklist.php` file where you can:

1. Choose which word lists to use (system blacklist, profanity, or both)
2. Customize the blacklisted terms in each list

> **Note:** If you don't publish the config file, the package will use the default configuration with the 'blacklist'
> mode enabled.

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

#### Basic controller

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

#### Livewire component

```php
use Livewire\Component;
use Milenmk\LaravelBlacklist\BlacklistService;

class YourComponent extends Component
{

    protected BlacklistService $blacklistService;

    public function mount(): void
    {
        $this->blacklistService = app(BlacklistService::class);
    }
```

#### Livewire form

```php
use Livewire\Form;
use Milenmk\LaravelBlacklist\BlacklistService;

class YourForm extends Form
{
    protected BlacklistService $blacklistService;

    public function __construct($componentOrService = null, $propertyName = null)
    {

        parent::__construct($componentOrService, $propertyName);
        $this->blacklistService = app(BlacklistService::class);
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

- "The {field} contains the blacklisted word: "{term}""
- "The {field} contains the profanity word: "{term}""

### Word Matching

This package uses whole word boundary matching to prevent false positives. For example:

- "admin" will match in "admin user" but not in "administrator" or "badminton"
- "damn" will match in "that's damn good" but not in "condamnation"

This ensures that legitimate content isn't incorrectly flagged while still catching problematic terms.

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.

## Support My Work

If this package saves you time, you can support ongoing development:  
👉 [Become a Patron](https://www.patreon.com/c/LaravelAddonsbyMilen)

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE.md) file for more details.

## Disclaimer

This package is provided "as is", without warranty of any kind, express or implied, including but not limited to
warranties of merchantability, fitness for a particular purpose, or noninfringement.

The author(s) make no guarantees regarding the accuracy, reliability, or completeness of the code, and shall not be held
liable for any damages or losses arising from its use.

Please ensure you thoroughly test this package in your environment before deploying it to production.

