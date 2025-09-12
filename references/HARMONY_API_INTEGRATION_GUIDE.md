# Harmony API Integration Guide

**Author**: Faiz Nasir  
**Date**: September 12, 2025  
**Version**: 2.0  
**Status**: Production Ready

---

## Table of Contents

1. [Overview](#overview)
2. [Installation & Setup](#installation--setup)
3. [Authentication Methods](#authentication-methods)
4. [API Implementations](#api-implementations)
5. [CRUD Test API](#crud-test-api)
6. [Usage Examples](#usage-examples)
7. [Best Practices](#best-practices)
8. [Testing Guide](#testing-guide)
9. [Troubleshooting](#troubleshooting)
10. [API Reference](#api-reference)

---

## Overview

This guide covers the comprehensive implementation of the Harmony package for API integrations in Laravel. The Harmony package provides a structured, fluent way to manage third-party API configurations and make HTTP requests.

### Key Features

- 🔧 **Structured API Management**: Clean, maintainable API configurations
- 🔐 **Multiple Authentication Types**: Bearer token, API key, basic auth, and none
- 📊 **Comprehensive CRUD Operations**: Full Create, Read, Update, Delete functionality
- 🌤️ **Weather API Integration**: Open-Meteo weather data integration
- 🧪 **Mock Data Testing**: Complete CRUD test API with mock data
- 📱 **Mobile Ready**: JSON responses perfect for mobile applications
- ⚡ **High Performance**: Optimized for speed and efficiency

### Implemented APIs

| API Type | Description | Status | Authentication |
|----------|-------------|---------|----------------|
| Open-Meteo Weather | Free weather data API | ✅ Production | None |
| Mock Data CRUD | Complete CRUD test API | ✅ Testing | None |
| API Key Example | API key authentication demo | ✅ Demo | API Key |
| Article Management | Article CRUD operations | ✅ Testing | Bearer Token |

---

## Installation & Setup

### 1. Install Harmony Package

```bash
composer require aisyahhanifiahrv/harmony:dev-main
```

### 2. Register Service Provider

The package is automatically registered via `composer.json`. For manual registration, add to `config/app.php`:

```php
'providers' => [
    // ... other providers
    Harmony\HarmonyServiceProvider::class,
],
```

### 3. Bind Harmony Service

In `app/Providers/AppServiceProvider.php`:

```php
use Harmony\Harmony;

public function register(): void
{
    $this->app->bind('harmony', function(){
        return new Harmony();
    });
}
```

### 4. Generate Harmony Classes

```bash
# Generate Harmony classes
php artisan make:harmony ArticleHarmony
php artisan make:harmony OpenMeteoHarmony
php artisan make:harmony ApiKeyHarmony
```

---

## Authentication Methods

### 1. No Authentication

For APIs that don't require authentication (like Open-Meteo):

```php
public static function connector(): Connector
{
    return Harmony::createConnector(function (Connector $connector) {
        $connector
        ->authorization(function (Authorization $authorization) {
            $authorization->type('none');
        })
        ->header(function (Header $header){
            $header->type('Content-Type', 'application/json');
            $header->type('Accept', 'application/json');
        });
    });
}
```

### 2. Bearer Token Authentication

For APIs that use bearer tokens:

```php
public static $baseUrl = 'https://api.example.com/v1/';
public static $token = 'your-bearer-token-here';

public static function connector(): Connector
{
    return Harmony::createConnector(function (Connector $connector) {
        $connector
        ->authorization(function (Authorization $authorization) {
            $authorization->type('bearer');
        })
        ->header(function (Header $header){
            $header->type('Content-Type', 'application/json');
            $header->type('Accept', 'application/json');
        });
    });
}
```

### 3. API Key Authentication

For APIs that use API keys:

```php
public static $baseUrl = 'https://api.example.com/v1/';
public static $apiKey = 'your-api-key-here';

public static function connector(): Connector
{
    return Harmony::createConnector(function (Connector $connector) {
        $connector
        ->authorization(function (Authorization $authorization) {
            $authorization->type('api_key');
        })
        ->header(function (Header $header){
            $header->type('Content-Type', 'application/json');
            $header->type('Accept', 'application/json');
            $header->type('X-API-Key', self::$apiKey);
        });
    });
}
```

### 4. Basic Authentication

For APIs that use username/password:

```php
public static $baseUrl = 'https://api.example.com/v1/';
public static $username = 'your-username';
public static $password = 'your-password';

public static function connector(): Connector
{
    return Harmony::createConnector(function (Connector $connector) {
        $connector
        ->authorization(function (Authorization $authorization) {
            $authorization->type('basic');
        })
        ->header(function (Header $header){
            $header->type('Content-Type', 'application/json');
            $header->type('Accept', 'application/json');
        });
    });
}
```

---

## API Implementations

### 1. Open-Meteo Weather API

**Location**: `app/Harmony/OpenMeteoHarmony.php`

**Features**:
- Weather forecast (up to 16 days)
- Current weather conditions
- Historical weather data
- Air quality information
- Elevation data
- Marine weather forecast

**Usage**:
```php
use App\Harmony\OpenMeteoHarmony;
use Harmony\Facade\Harmony;

$openMeteo = new OpenMeteoHarmony();
$collection = $openMeteo->getForecast(3.1390, 101.6869); // Kuchai Lama, Malaysia

$query = [
    'latitude' => 3.1390,
    'longitude' => 101.6869,
    'hourly' => 'temperature_2m,precipitation',
    'daily' => 'temperature_2m_max,temperature_2m_min',
    'forecast_days' => 7,
    'timezone' => 'auto',
];

$response = Harmony::send($collection, null, $query);
```

### 2. Article Management API

**Location**: `app/Harmony/ArticleHarmony.php`

**Features**:
- Article CRUD operations
- Search functionality
- Bearer token authentication

**Usage**:
```php
use App\Harmony\ArticleHarmony;
use Harmony\Facade\Harmony;
use App\Models\Article;

$articleHarmony = new ArticleHarmony();
$article = new Article(['id' => 1]);

// Get article
$collection = $articleHarmony->show($article);
$response = Harmony::send($collection);

// Update article
$collection = $articleHarmony->update($article);
$body = [
    'title' => 'Updated Article Title',
    'content' => 'Updated content...',
    'status' => 'published'
];
$response = Harmony::send($collection, $body);
```

### 3. API Key Authentication Example

**Location**: `app/Harmony/ApiKeyHarmony.php`

**Features**:
- Complete CRUD operations
- API key authentication
- Search functionality
- Statistics endpoint

**Usage**:
```php
use App\Harmony\ApiKeyHarmony;
use Harmony\Facade\Harmony;

$apiKeyHarmony = new ApiKeyHarmony();

// Get users
$collection = $apiKeyHarmony->getUsers();
$query = ['page' => 1, 'limit' => 10];
$response = Harmony::send($collection, null, $query);

// Create user
$collection = $apiKeyHarmony->createUser();
$body = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+60123456789'
];
$response = Harmony::send($collection, $body);
```

---

## CRUD Test API

### Overview

The Mock Data CRUD API provides a complete testing environment with in-memory data storage. This is perfect for development, testing, and demonstration purposes.

**Base URL**: `http://your-domain.com/api/`

### Available Endpoints

#### Users Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/users` | Get all users with pagination |
| GET | `/api/users/{id}` | Get user by ID |
| POST | `/api/users` | Create new user |
| PUT | `/api/users/{id}` | Update user |
| DELETE | `/api/users/{id}` | Delete user |

#### Products Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/products` | Get all products with pagination |
| GET | `/api/products/{id}` | Get product by ID |
| POST | `/api/products` | Create new product |
| PUT | `/api/products/{id}` | Update product |
| DELETE | `/api/products/{id}` | Delete product |

#### Orders Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/orders` | Get all orders with pagination |
| GET | `/api/orders/{id}` | Get order by ID |
| POST | `/api/orders` | Create new order |
| PUT | `/api/orders/{id}` | Update order |
| DELETE | `/api/orders/{id}` | Delete order |

#### Utilities

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/stats` | Get API statistics |
| POST | `/api/reset` | Reset all mock data |

### Sample Data

The API comes pre-loaded with Malaysian-themed sample data:

#### Users
- Ahmad Rahman (Kuchai Lama, Kuala Lumpur)
- Siti Nurhaliza (Puchong, Selangor)
- Muhammad Ali (Ampang, Kuala Lumpur)

#### Products
- Samsung Galaxy S24 (Electronics)
- MacBook Pro M3 (Electronics)
- Nike Air Max 270 (Fashion)

#### Orders
- Order #1: Ahmad Rahman → Samsung Galaxy S24 (2 units)
- Order #2: Siti Nurhaliza → MacBook Pro M3 (1 unit)

---

## Usage Examples

### 1. Weather API Integration

```php
// Get weather forecast for Kuchai Lama, Malaysia
$response = Http::get('http://localhost:8000/weather/forecast', [
    'latitude' => 3.1390,
    'longitude' => 101.6869,
    'forecast_days' => 3
]);

$weatherData = $response->json();
echo "Temperature: " . $weatherData['data']['hourly']['temperature_2m'][0] . "°C";
```

### 2. CRUD Operations

```php
// Get all users
$response = Http::get('http://localhost:8000/api/users');
$users = $response->json()['data'];

// Get specific user
$response = Http::get('http://localhost:8000/api/users/1');
$user = $response->json()['data'];

// Create new user
$response = Http::post('http://localhost:8000/api/users', [
    'name' => 'Faiz Nasir',
    'email' => 'faiz@example.com',
    'phone' => '+60123456792',
    'address' => '999 Jalan Kuchai Lama, Kuala Lumpur'
]);

// Update user
$response = Http::put('http://localhost:8000/api/users/1', [
    'name' => 'Ahmad Rahman Updated',
    'email' => 'ahmad.updated@example.com'
]);

// Delete user
$response = Http::delete('http://localhost:8000/api/users/1');
```

### 3. Product Management

```php
// Get all products
$response = Http::get('http://localhost:8000/api/products');
$products = $response->json()['data'];

// Search products by category
$response = Http::get('http://localhost:8000/api/products', [
    'category' => 'Electronics'
]);

// Create new product
$response = Http::post('http://localhost:8000/api/products', [
    'name' => 'iPhone 15 Pro',
    'description' => 'Latest iPhone with advanced features',
    'price' => 4999.00,
    'category' => 'Electronics',
    'stock' => 30,
    'is_active' => true
]);
```

### 4. Order Management

```php
// Get all orders
$response = Http::get('http://localhost:8000/api/orders');
$orders = $response->json()['data'];

// Get orders by user
$response = Http::get('http://localhost:8000/api/orders', [
    'user_id' => 1
]);

// Create new order
$response = Http::post('http://localhost:8000/api/orders', [
    'user_id' => 1,
    'product_id' => 1,
    'quantity' => 2,
    'status' => 'pending'
]);
```

### 5. Statistics

```php
// Get API statistics
$response = Http::get('http://localhost:8000/api/stats');
$stats = $response->json()['data'];

echo "Total Users: " . $stats['users'];
echo "Total Products: " . $stats['products'];
echo "Total Revenue: RM" . $stats['total_revenue'];
```

---

## Best Practices

### 1. Error Handling

```php
try {
    $response = Harmony::send($collection, $body, $query);
    
    if ($response->successful()) {
        $data = $response->json();
        // Process successful response
    } else {
        // Handle API errors
        switch ($response->status()) {
            case 400:
                throw new \Exception('Bad Request: Invalid parameters');
            case 401:
                throw new \Exception('Unauthorized: Check your API key');
            case 404:
                throw new \Exception('Not Found: Resource not found');
            case 429:
                throw new \Exception('Rate Limited: Too many requests');
            case 500:
                throw new \Exception('Server Error: API server error');
            default:
                throw new \Exception('API Error: ' . $response->body());
        }
    }
} catch (\Exception $e) {
    \Log::error('Harmony API Error: ' . $e->getMessage());
    return response()->json(['error' => $e->getMessage()], 500);
}
```

### 2. Caching Strategy

```php
// Cache API responses for better performance
$cacheKey = "weather_forecast_{$latitude}_{$longitude}_{$forecastDays}";
$weatherData = Cache::remember($cacheKey, 600, function () use ($collection, $query) {
    $response = Harmony::send($collection, null, $query);
    return $response->json();
});
```

### 3. Input Validation

```php
$request->validate([
    'latitude' => 'required|numeric|between:-90,90',
    'longitude' => 'required|numeric|between:-180,180',
    'forecast_days' => 'integer|min:1|max:16',
    'timezone' => 'string|max:50',
]);
```

### 4. Rate Limiting

```php
// Implement rate limiting for API calls
RateLimiter::attempt(
    'api-calls:' . $request->ip(),
    100, // 100 requests
    function () {
        // Process API request
    },
    3600 // per hour
);
```

### 5. Logging

```php
// Log all API calls for monitoring
\Log::info('API Call', [
    'endpoint' => $endpoint,
    'method' => $method,
    'parameters' => $query,
    'response_time' => $response->transferStats->getHandlerStat('total_time')
]);
```

---

## Testing Guide

### 1. Test Weather API

```bash
# Test weather forecast
curl -X GET "http://localhost:8000/weather/forecast?latitude=3.1390&longitude=101.6869&forecast_days=3"

# Test current weather
curl -X GET "http://localhost:8000/weather/current?latitude=3.1390&longitude=101.6869"

# Test all weather endpoints
curl -X GET "http://localhost:8000/weather/test-all"
```

### 2. Test CRUD API

```bash
# Test users
curl -X GET "http://localhost:8000/api/users"
curl -X GET "http://localhost:8000/api/users/1"

# Test products
curl -X GET "http://localhost:8000/api/products"
curl -X GET "http://localhost:8000/api/products/1"

# Test orders
curl -X GET "http://localhost:8000/api/orders"
curl -X GET "http://localhost:8000/api/orders/1"

# Test statistics
curl -X GET "http://localhost:8000/api/stats"
```

### 3. Test with Postman

Import the following collection for comprehensive testing:

```json
{
  "info": {
    "name": "Harmony API Testing",
    "description": "Complete API testing collection"
  },
  "item": [
    {
      "name": "Weather Forecast",
      "request": {
        "method": "GET",
        "url": "{{base_url}}/weather/forecast?latitude=3.1390&longitude=101.6869&forecast_days=3"
      }
    },
    {
      "name": "Get Users",
      "request": {
        "method": "GET",
        "url": "{{base_url}}/api/users"
      }
    },
    {
      "name": "Get Products",
      "request": {
        "method": "GET",
        "url": "{{base_url}}/api/products"
      }
    }
  ]
}
```

### 4. Unit Testing

```php
// tests/Feature/HarmonyApiTest.php
public function test_weather_forecast_endpoint()
{
    $response = $this->get('/weather/forecast?latitude=3.1390&longitude=101.6869');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'latitude',
            'longitude',
            'hourly',
            'daily'
        ]
    ]);
}

public function test_crud_users_endpoint()
{
    $response = $this->get('/api/users');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            '*' => [
                'id',
                'name',
                'email',
                'phone',
                'address'
            ]
        ],
        'pagination'
    ]);
}
```

---

## Troubleshooting

### Common Issues

#### 1. CSRF Token Mismatch

**Problem**: POST requests return 419 Page Expired  
**Solution**: Disable CSRF for API routes or use API middleware

```php
// In routes/api.php (recommended)
Route::post('/users', [MockDataController::class, 'createUser']);

// Or disable CSRF for specific routes
Route::post('/users', [MockDataController::class, 'createUser'])->withoutMiddleware(['csrf']);
```

#### 2. Route Not Found

**Problem**: 404 Not Found errors  
**Solution**: Check if routes are properly registered

```bash
php artisan route:list | grep api
php artisan route:list | grep weather
```

#### 3. Memory Issues

**Problem**: Large responses cause memory issues  
**Solution**: Implement pagination and limit data

```php
// Limit forecast days
$forecastDays = min($request->input('forecast_days', 7), 7);

// Implement pagination
$page = $request->input('page', 1);
$perPage = $request->input('per_page', 10);
```

#### 4. API Timeout

**Problem**: API requests timeout  
**Solution**: Increase timeout and check internet connection

```php
// In Harmony class
$httpRequest = Http::timeout(30)->withHeaders($headers);
```

### Debug Mode

Enable debug mode for detailed error information:

```php
// In .env
APP_DEBUG=true
LOG_LEVEL=debug

// In controller
if (config('app.debug')) {
    return response()->json([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], 500);
}
```

---

## API Reference

### Harmony Package Methods

#### `Harmony::createConnector(Closure $callback)`

Create a new connector instance.

**Parameters**:
- `$callback`: Closure function to configure the connector

**Returns**: `Connector` object

#### `Harmony::createCollection(Closure $callback)`

Create a new collection instance.

**Parameters**:
- `$callback`: Closure function to configure the collection

**Returns**: `Collection` object

#### `Harmony::send($api, $body, $query)`

Send an API request.

**Parameters**:
- `$api`: Collection object with request configuration
- `$body`: Request body data (optional)
- `$query`: Query parameters (optional)

**Returns**: HTTP response object

### Available Endpoints

#### Weather API Endpoints

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/weather/forecast` | GET | Weather forecast | latitude, longitude, forecast_days |
| `/weather/current` | GET | Current weather | latitude, longitude |
| `/weather/historical` | GET | Historical weather | latitude, longitude, start_date, end_date |
| `/weather/air-quality` | GET | Air quality data | latitude, longitude, forecast_days |
| `/weather/elevation` | GET | Elevation data | latitude, longitude |
| `/weather/marine` | GET | Marine weather | latitude, longitude, forecast_days |
| `/weather/test-all` | GET | Test all endpoints | None |

#### CRUD API Endpoints

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/api/users` | GET | Get all users | page, per_page, search |
| `/api/users/{id}` | GET | Get user by ID | id |
| `/api/users` | POST | Create user | name, email, phone, address |
| `/api/users/{id}` | PUT | Update user | id, name, email, phone, address |
| `/api/users/{id}` | DELETE | Delete user | id |
| `/api/products` | GET | Get all products | page, per_page, category, search |
| `/api/products/{id}` | GET | Get product by ID | id |
| `/api/products` | POST | Create product | name, description, price, category, stock |
| `/api/products/{id}` | PUT | Update product | id, name, description, price, category, stock |
| `/api/products/{id}` | DELETE | Delete product | id |
| `/api/orders` | GET | Get all orders | page, per_page, user_id, status |
| `/api/orders/{id}` | GET | Get order by ID | id |
| `/api/orders` | POST | Create order | user_id, product_id, quantity, status |
| `/api/orders/{id}` | PUT | Update order | id, user_id, product_id, quantity, status |
| `/api/orders/{id}` | DELETE | Delete order | id |
| `/api/stats` | GET | Get statistics | None |
| `/api/reset` | POST | Reset mock data | None |

---

## Support & Resources

### Official Documentation
- [Harmony Package Documentation](https://github.com/aisyahhanifiahrv/harmony)
- [Open-Meteo API Documentation](https://open-meteo.com/en/docs)
- [Laravel HTTP Client Documentation](https://laravel.com/docs/http-client)

### Contact Information
- **Developer**: Faiz Nasir
- **Email**: faiznasir@rocketsview.com
- **Project**: E-commerce Training Application
- **Location**: Kuchai Lama, Kuala Lumpur, Malaysia

### License
This integration is provided under the MIT License. All APIs used are free for both commercial and non-commercial purposes.

---

**Last Updated**: September 12, 2025  
**Documentation Version**: 2.0  
**Harmony Package Version**: dev-main  
**Laravel Version**: 8.0+
