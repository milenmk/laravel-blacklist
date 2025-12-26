# Laravel Blacklist

<div align="center">

<a href="https://packagist.org/packages/milenmk/laravel-blacklist">![Latest Version on Packagist](https://img.shields.io/packagist/v/milenmk/laravel-blacklist.svg?style=flat)</a>
<a href="https://packagist.org/packages/milenmk/laravel-blacklist">![Total Downloads](https://img.shields.io/packagist/dt/milenmk/laravel-blacklist.svg?style=flat)</a>
<a href="https://github.com/milenmk/laravel-blacklist">![GitHub User's stars](https://img.shields.io/github/stars/milenmk/laravel-blacklist)</a>
<a href="https://laravel.com/docs">![Laravel 10 Support](https://img.shields.io/badge/Laravel-10.x|11.x|12.x-orange?style=flat&logo=laravel)</a>
<a href="https://www.php.net">![PHP Version Support](https://img.shields.io/packagist/php-v/milenmk/laravel-blacklist?style=flat)</a>
<a href="https://github.com/milenmk/laravel-blacklist/blob/develop/LICENSE.md">![License](https://img.shields.io/packagist/l/milenmk/laravel-blacklist.svg?style=flat)</a>
<a href="https://github.com/milenmk/laravel-blacklist/issues">![Contributions Welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)</a>
<a href="https://www.patreon.com/c/LaravelAddonsbyMilen">![Sponsor me](https://img.shields.io/badge/Sponsor-%E2%9D%A4-ff69b4?style=flat)</a>

</div>

Laravel Blacklist: A robust content filtering solution for Laravel applications that provides comprehensive validation
against unwanted user input. This package offers dual-layer protection with both system blacklist words (preventing
username squatting and system impersonation) and profanity/offensive terms filtering.

## Key features:

- **Intelligent Word Boundary Matching**: Prevents false positives while catching problematic content.
- **Flexible Filtering Modes**: Use system blacklist only, profanity filtering only, or both simultaneously.
- **Advanced Matching Strategies**:
    - **Exact**: Whole word matching (default).
    - **Fuzzy**: Catch typos using Levenshtein distance (e.g., "admin" matches "adm1n").
    - **Substitution**: Catch "leet speak" substitutions (e.g., "h3ll0").
- **Context-Aware Validation**: Define different validation rules for different fields (e.g., stricter rules for
  usernames vs comments).
- **Whitelist & Ignore Patterns**: Globally whitelist terms or use regex to ignore specific patterns.
- **Customizable Word Lists**: Easily extend or modify lists via configuration.
- **Detailed Error Messages**: Users receive context-aware validation feedback.
- **Built-in Security Logging**: Support for custom log channels.
- **Simple Integration**: Works with Laravel controllers, Livewire components, and forms.
- **Zero Dependencies**: Lightweight and efficient.
- **Optional Logging Support**: Pass a custom log channel to capture violations or auditing.

Perfect for applications requiring content moderation, user registration systems, comment sections, or any
user-generated content that needs protection against inappropriate language or system term abuse.

## Installation

1. Install the package via composer:

    ```copy
    composer require milenmk/laravel-blacklist
    ```

2. Publish the configuration file

   The package works out of the box with default settings, but you can customize it by publishing the config file:

    ```copy
    php artisan vendor:publish --tag=blacklist-config
    ```

   This will create a `config/blacklist.php` file where you can:

    1. Choose which word lists to use (system blacklist, profanity, or both)
    2. Customize the blacklisted terms in each list

   > **Note:** If you don't publish the config file, the package will use the default configuration with the 'blacklist'
   > mode enabled.

3. Publish the package language file

    ```php
    php artisan vendor:publish --tag=blacklist-translations
    ```

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

    // Whitelist words that should never be flagged
    'whitelist' => [
        'Laravel',
    ],

    // Regex patterns to ignore
    'ignore_patterns' => [
        '/^uuid-.*$/', 
    ],

    // Advanced matching strategies
    'lists' => [
        'custom_list' => [
            'terms' => ['forbidden'],
            'matching' => 'fuzzy', // Options: exact, fuzzy, substitution
            'threshold' => 1,      // For fuzzy matching
        ],
    ],

    // Per-field contexts
    'contexts' => [
        'username' => ['blacklist', 'custom_list'],
    ],
];
```

## Usage

#### Basic controller

```php
use Milenmk\LaravelBlacklist\Services\BlacklistService;

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
use Milenmk\LaravelBlacklist\Services\BlacklistService;

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
use Milenmk\LaravelBlacklist\Services\BlacklistService;

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

### 1. Blacklist Validation Rule

You can use the `BlacklistRule` in your form requests or validation logic.

```php
use Milenmk\LaravelBlacklist\Rules\BlacklistRule;

// ...

public function rules(): array
{
    return [
        // Uses 'username' as the context (checks 'contexts.username' in config)
        'username' => ['required', new BlacklistRule()],
        
        // Explicitly specify the context
        'bio' => ['required', new BlacklistRule('strict_bio')],
    ];
}
```

### 2. Route Middleware

Protect your routes using the `blacklist` middleware.

```php
// Protect a route using the 'comment' context
Route::post('/comments', ...)->middleware('blacklist:comment');

// If no context is provided, it uses the field names from the request as contexts
Route::post('/profile', ...)->middleware('blacklist');
```

### 3. Whitelist and Ignore Patterns

You can define global exceptions in your configuration file:

- **Whitelist**: Exact words that should never be blocked (e.g., "Analyst").
- **Ignore Patterns**: Regex patterns to ignore (e.g., ignoring UUIDs or specific codes).

```php
// config/blacklist.php
'whitelist' => ['Analyst', 'Dickson'],
'ignore_patterns' => ['/^TX-\d+$/'],
```

### 4. Advanced Matching Strategies

You can define custom lists with specific matching strategies in `config/blacklist.php`:

```php
'lists' => [
    'strict_list' => [
        'terms' => ['forbidden'],
        'matching' => 'exact',
    ],
    'typo_list' => [
        'terms' => ['important'],
        'matching' => 'fuzzy', // Uses Levenshtein distance
        'threshold' => 1,      // Matches "1mportant"
    ],
    'leet_list' => [
        'terms' => ['hacker'],
        'matching' => 'substitution', // Matches "h4ck3r"
    ],
],
```

### 5. Context-Aware Validation

Map different contexts (fields) to specific lists:

```php
'contexts' => [
    'username' => ['blacklist', 'strict_list'],
    'comment' => ['profanity', 'typo_list', 'leet_list'],
],
```

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

### Enhanced Attribute Name Support (New)

The checkFields() method now accepts an optional third parameter `$attributes` — an associative array mapping field
names to human-readable labels:

```php
$attributes = [
    'last_name' => 'Last Name',
    'name' => 'Name',
    'email' => 'Email Address',
    // Add your fields here
];

$blacklistErrors = $this->blacklistService->checkFields($input, null, $attributes);
```

This enables error messages to display friendly field names instead of raw input keys. For example:

```text
The Last Name contains a blacklisted word: administrator
```

instead of

```text
The last_name contains a blacklisted word: administrator
```

This ensures your error messages remain clear and consistent with Laravel's native validation attribute naming
conventions, improving user experience.

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.

## Support My Work

If this package saves you time, you can support ongoing development:  
👉 [Become a Patron](https://www.patreon.com/c/LaravelAddonsbyMilen)

## Other Packages

Check out my other Laravel packages:

- **[Laravel GDPR Cookie Manager](https://packagist.org/packages/milenmk/laravel-gdpr-cookie-manager)** - GDPR-compliant
  cookie consent management with user preference tracking
- **[Laravel Email Change Confirmation](https://packagist.org/packages/milenmk/laravel-email-change-confirmation)** -
  Secure email change confirmation system
- **[Laravel GDPR Exporter](https://packagist.org/packages/milenmk/laravel-gdpr-exporter)** - GDPR-compliant data export
  functionality
- **[Laravel Locations](https://packagist.org/packages/milenmk/laravel-locations)** - Add Countries, Cities, Areas,
  Languages and Currencies models to your Laravel application
- **[Laravel Rate Limiting](https://packagist.org/packages/milenmk/laravel-rate-limiting)** - Advanced rate limiting
  capabilities with exponential backoff
- **[Laravel Datatables and Forms](https://packagist.org/packages/milenmk/laravel-simple-datatables-and-forms)** - Easy
  to use package to create datatables and forms for Livewire components

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE.md) file for more details.

## Disclaimer

This package is provided "as is", without warranty of any kind, express or implied, including but not limited to
warranties of merchantability, fitness for a particular purpose, or noninfringement.

The author(s) make no guarantees regarding the accuracy, reliability, or completeness of the code, and shall not be held
liable for any damages or losses arising from its use.

Please ensure you thoroughly test this package in your environment before deploying it to production.

