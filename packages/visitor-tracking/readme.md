# Visitor Tracking Package for Laravel

The `monishroy/visitor-tracking` package provides a simple way to track and analyze visitor data in your Laravel application, including total visitors, unique visitors, top visited pages, countries, operating systems, and devices.

## Installation

1. **Install the Package**

   Require the package via Composer:

   ```bash
   composer require monishroy/visitor-tracking
   ```

2. **Run Database Migrations**

   After installing the package, run the migrations to set up the necessary database tables:

   ```bash
   php artisan migrate
   ```

## Middleware

The package includes a middleware aliased as `visitor_tracking` to track visitor data for specific routes. To use it, apply the middleware to your routes.

### Applying the Middleware

You can apply the `visitor_tracking` middleware to individual routes or route groups in your `web.php` or `api.php` files.

#### Example: Single Route

```php
Route::get('/home', [HomeController::class, 'index'])->middleware('visitor_tracking');
```

#### Example: Route Group

```php
Route::middleware(['visitor_tracking'])->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/about', [AboutController::class, 'index']);
});
```

The middleware will automatically track visitor data for the routes it is applied to.

## Usage

The package provides a `Visitor` facade to access visitor tracking data. You can use the following methods to retrieve analytics:

```php
use Monishroy\VisitorTracking\Helpers\Visitor;

Visitor::totalVisitors(),     // Returns the total number of visitors
Visitor::uniqueVisitors(),    // Returns the count of unique visitors
Visitor::topVisitedPages($limit),   // Returns the most visited pages $limit = 5 default
Visitor::countries(),         // Returns visitor countries
Visitor::os(),                // Returns operating systems used by visitors
Visitor::devices()            // Returns devices used by visitors
```

### Example Output

Running the above code will dump the visitor data, which might look like this (example):

```php
[
    "totalVisitors" => 1000,
    "uniqueVisitors" => 750,
    "topVisitedPages" => [
        [
            'page_title' => 'Home',
            'url' => 'https://example.com'
        ],[
            'page_title' => 'About',
            'url' => 'https://example.com/about'
        ],[
            'page_title' => 'Contact',
            'url' => 'https://example.com/contact'
        ],
    ],
    "countries" => [
        "US" => 400,
        "IN" => 300,
        "BD" => 100
    ],
    "os" => [
        "Windows" => 600,
        "MacOS" => 300,
        "Linux" => 100
    ],
    "devices" => [
        "Desktop" => 700,
        "Mobile" => 250,
        "Tablet" => 50
    ]
]
```

## Customization

### Using the VisitorTable Model

The package includes a `VisitorTable` model that you can use to interact directly with the visitor tracking database table. This allows you to perform custom queries or extend the functionality.

#### Example: Querying Visitor Data

```php
use Monishroy\VisitorTracking\Models\VisitorTable;

$visitors = VisitorTable::where('country', 'US')->get();
foreach ($visitors as $visitor) {
    echo $visitor->page . ' visited from ' . $visitor->country;
}
```

You can also extend the `VisitorTable` model to add custom methods or relationships.

## Requirements

- PHP &gt;= 7.4
- Laravel &gt;= 8.0

## License

This package is open-sourced software licensed under the MIT license.

## Support

For issues or feature requests, please open an issue on the GitHub repository.