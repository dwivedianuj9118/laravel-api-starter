# README.md
# Laravel API Starter 🚀
- 🌐 **Website:** https://anujdwivedi.in
- 🌐 **Company:** https://papayacoders.com
- 📦 **Packagist:** https://packagist.org/packages/dwivedianuj9118/laravel-api-starter  
- 🐙 **GitHub:** https://github.com/dwivedianuj9118/laravel-api-starter

A **production-ready API starter package** for **Laravel 11+** designed to help you bootstrap APIs instantly without reinventing the wheel. This package is **opinionated but highly configurable**, tailored for real-world backend applications.

## ✨ Features

- ✅ **Dual Auth Support:** JWT Authentication (for mobile/external) & Sanctum SPA Authentication.
- ✅ **Toggleable Auth:** Enable or disable JWT/Sanctum via config.
- ✅ **Standardized Responses:** Uniform JSON structure for success and error states.
- ✅ **API Versioning:** Pre-configured versioning (defaults to `/api/v1`).
- ✅ **Auto-Documentation:** Swagger/OpenAPI integration out of the box.
- ✅ **Robust Error Handling:** Global API exception handling (No more HTML errors in your API!).
- ✅ **Health Monitoring:** Dedicated `/health` endpoint for uptime checks.
- ✅ **Performance:** Built-in rate limiting and JSON-only enforcement.
- ✅ **Laravel 11 Ready:** Fully compatible with the latest Laravel structures.
## 📦 Requirements

- **PHP:** 8.2+
- **Laravel:** 11+

---

## 📥 Installation

Install the package via Composer:

```bash
composer require dwivedianuj9118/laravel-api-starter
```
### Configuration

Publish the configuration file to customize the behavior:
```bash
php artisan api-starter:install
```

### Environment Variables (`.env`)

Add or modify these variables to control your API behavior:

```env
API_VERSION=v1
API_RATE_LIMIT=60

API_ENABLE_JWT=true
API_ENABLE_SANCTUM=true

API_AUTH_MODEL=App\Models\User

```
### JWT Guard 
You must define a JWT guard in `config/auth.php`.

```php
'guards' => [

    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'jwt' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],

],
```
### Ensure provider exists
```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
],

```
---
## 🧯 Global API Exception Handling

The package automatically ensures JSON-only API exception responses.

If you want to customize exception rendering further, you may optionally integrate
`ApiExceptionHandler` into your global exception flow.


### 📄 bootstrap/app.php (Laravel 11+)
```php
use Dwivedianuj9118\ApiStarter\Exceptions\ApiExceptionHandler;

->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (Throwable $e, $request) {
        if ($request->is('api/*')) {
            return ApiExceptionHandler::handle($e);
        }
    });
});

```
This ensures:
No HTML error pages
Consistent API error responses
Validation & auth errors normalized


### 📄 App\Providers\AppServiceProvider.php

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

public function boot(): void
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(
            config('api-starter.rate_limit.per_minute')
        )->by($request->ip());
    });
}
```

## 🔐 Preparing the Authentication Model (JWT & Sanctum)

This package supports **JWT authentication** and **Sanctum SPA authentication**.  
Your authentication model (usually `User`) must be configured correctly.

---

### JWT & Sanctum Model Setup (Required)

To enable **JWT** and **Sanctum** authentication, update your auth model (usually `User`) as follows:

1. **Implement `JWTSubject`**
2. **Use `HasApiTokens` trait**
3. **Add the two JWT methods**

```php
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
```
If you are using JWT authentication, you must also define a `JWTSubject` interface in your model. And if you are using Sanctum authentication, you must also define a `HasApiTokens` trait in your model.

If you are using JWT and want to use middleware, you can use the `auth:jwt` middleware. And if you are using Sanctum, you can use the `auth:sanctum` middleware.

### Default:

## 🔐 Authentication

### 1. JWT Authentication

Ideal for mobile apps and external clients.

* **Register:** `POST /api/v1/auth/register`
* **Login:** `POST /api/v1/auth/login`
* **Refresh:** `POST /api/v1/auth/refresh`
* **Logout:** `POST /api/v1/auth/logout`

*To disable, set `API_ENABLE_JWT=false*`

### 2. Sanctum SPA Authentication

Optimized for first-party web applications.

* **Login:** `POST /api/v1/spa/login`
* **Logout:** `POST /api/v1/spa/logout`

*To disable, set `API_ENABLE_SANCTUM=false*`

### Custom Auth Model

You can define which model is used for authentication (e.g., for an Admin panel):

```env
API_AUTH_MODEL=App\Models\Admin

```

> **Note:** Your model must extend `Illuminate\Foundation\Auth\User` and use the `HasApiTokens` trait.

---

## 📊 API Response Format

All responses are returned as structured JSON.

### Success Response

```json
{
  "success": true,
  "message": "Success",
  "data": {},
  "errors": null
}

```

### Error Response

```json
{
  "success": false,
  "message": "Validation failed",
  "data": null,
  "errors": {
    "email": ["The email field is required."]
  }
}

```

---
## Swagger Setup (Optional)

Install Swagger:

```bash
composer require darkaonline/l5-swagger
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
```
Edit the generated file:

📄 config/l5-swagger.php

Update the annotations paths:
```php
'annotations' => [
    base_path('app'),
    base_path('vendor/dwivedianuj9118/laravel-api-starter/src'),
],
```
### 🔐 REQUIRED: Sanctum Security Scheme (Swagger)

Make sure this exists in `config/l5-swagger.php`

```php
'securityDefinitions' => [
    'securitySchemes' => [

        // JWT Authentication
        'bearerAuth' => [
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
            'description' => 'JWT Authorization header using the Bearer scheme. Example: Bearer {token}',
        ],

        // Sanctum Authentication
        'sanctum' => [
            'type' => 'http',
            'scheme' => 'bearer',
            'description' => 'Sanctum token using Bearer scheme. Example: Bearer {token}',
            'in' => 'header',
        ],
    ],
],


```
Generate documentation:

```bash
php artisan l5-swagger:generate
```


Access Swagger UI at:

/api/documentation

---

## 🛠 Features in Detail

### Health Check

Monitor your application status easily.

* **Endpoint:** `GET /api/v1/health`

### Rate Limiting

Prevent abuse with built-in throttling (per IP).

* **Default:** 60 requests/min.
* **Customization:** Update `API_RATE_LIMIT` in your `.env`.

---

## 🧪 Testing

Run the package test suite:

```bash
vendor/bin/phpunit

```

---

## 🚀 Roadmap

* [ ] OAuth / Social Login support
* [ ] Multi-guard API configurations
* [ ] API Key-based authentication
* [ ] Webhook signature verification support

---

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

© 2026 **Dwivedianuj9118**
