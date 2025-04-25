# Laravel Blacklist

A Laravel package for blacklist validation of user input. Includes 80 of the most common bad words and phrases, but also allows you to customize it as needed.

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

This will create a `config/blacklist.php` file where you can add your blacklisted terms to the blacklist array.

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

You can specify a custom log channel:

```php
$blacklistErrors = $this->blacklistService->checkFields([
    'name' => $request->input('name'),
    'email' => $request->input('email'),
], 'security');
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.