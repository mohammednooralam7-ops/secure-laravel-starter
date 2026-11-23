# Install notes (Laravel 11)

In `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
})
```

In `bootstrap/providers.php` add:

```php
App\Providers\RateLimitServiceProvider::class,
```

In `routes/web.php`:

```php
use App\Http\Controllers\Auth\SecureLoginController;
use App\Http\Controllers\DocumentController;

Route::post('/login', [SecureLoginController::class, 'login'])->middleware('throttle:login');
Route::middleware('auth')->group(function () {
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::get('/documents/{document}', [DocumentController::class, 'show']);
});
```

Create the Document model and a factory for the tests (`php artisan make:model Document -mf`) with fields `user_id`, `path`, `original_name`.
Also add a `failed_logins` (int, default 0) and `locked_until` (timestamp, nullable) column to `users`; the provided migration does this.
