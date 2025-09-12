# Harmony Package Usage Guide

**Author**: Faiz Nasir  
**Date**: September 12, 2025  
**Version**: 2.0  
**Status**: Production Ready

## Overview
The Harmony package is a Laravel API package for managing 3rd party API configurations and making HTTP requests in a structured, fluent way. This guide covers comprehensive implementations including weather APIs, CRUD operations, and various authentication methods.

## Installation
The package is already installed in this project. If you need to install it in another project:

```bash
composer require aisyahhanifiahrv/harmony:dev-main
```

## Configuration

### 1. Service Provider Registration
The package is automatically registered via the `composer.json` configuration.

### 2. AppServiceProvider Configuration
Add this to your `AppServiceProvider.php`:

```php
use Harmony\Harmony;

public function register(): void
{
    $this->app->bind('harmony', function(){
        return new Harmony();
    });
}
```

## Implemented APIs

### 1. Open-Meteo Weather API
- **Location**: `app/Harmony/OpenMeteoHarmony.php`
- **Features**: Weather forecast, current weather, historical data, air quality
- **Authentication**: None (free API)
- **Test Location**: Kuchai Lama, Malaysia (3.1390, 101.6869)
- **Endpoints**: `/weather/forecast`, `/weather/current`, `/weather/historical`, etc.

### 2. Article Management API
- **Location**: `app/Harmony/ArticleHarmony.php`
- **Features**: Article CRUD operations, search functionality
- **Authentication**: Bearer Token
- **Endpoints**: `/harmony-test/articles/*`

### 3. API Key Authentication Example
- **Location**: `app/Harmony/ApiKeyHarmony.php`
- **Features**: Complete CRUD operations with API key auth
- **Authentication**: API Key
- **Usage**: Demonstration of API key implementation

### 4. Mock Data CRUD API
- **Location**: `app/Http/Controllers/MockDataController.php`
- **Features**: Complete CRUD testing with Malaysian sample data
- **Authentication**: None (testing purposes)
- **Endpoints**: `/api/users/*`, `/api/products/*`, `/api/orders/*`

## Creating Harmony Classes

### Generate a Harmony Class
```bash
php artisan make:harmony ArticleHarmony
php artisan make:harmony OpenMeteoHarmony
php artisan make:harmony ApiKeyHarmony
```

This creates new Harmony classes in `app/Harmony/` directory.

## API Methods Structure

### 1. Basic Structure
Each Harmony class should have:
- Static properties for configuration (`$baseUrl`, `$token`, etc.)
- A `connector()` method for authentication and headers
- API methods that return `Collection` objects

### 2. Available API Methods
We've implemented the following methods in `ArticleHarmony`:

#### Index (GET all articles)
```php
public function index(): Collection
{
    return Harmony::createCollection(function (Collection $collection) {
        $collection->group(function (Request $request) {
            $request->endpoint('api/articles');
            $request->method('GET');
            $request->query(function (Query $query){
                $query->field('title');
                $query->field('keywords');
            });
        });
    });
}
```

#### Create (POST new article)
```php
public function create(): Collection
{
    return Harmony::createCollection(function (Collection $collection) {
        $collection->group(function (Request $request) {
            $request->endpoint('api/articles');
            $request->method('POST');
            $request->body(function (Body $body) {
                $body->group('data', function (Body $body) {
                    $body->group('attributes', function (Body $body) {
                        $body->field('title');
                        $body->field('keywords');
                        $body->field('content');
                        $body->field('status');
                        $body->field('author_id');
                    });
                });
            });
        });
    });
}
```

#### Update (PUT existing article)
```php
public function update(Article $article): Collection
{
    return Harmony::createCollection(function (Collection $collection) use ($article) {
        $collection->group(function (Request $request) use ($article) {
            $request->endpoint('api/articles/' . $article->id);
            $request->method('PUT');
            $request->body(function (Body $body) {
                $body->group('data', function (Body $body) {
                    $body->group('attributes', function (Body $body) {
                        $body->field('title');
                        $body->field('keywords');
                        $body->field('content');
                        $body->field('status');
                    });
                });
            });
        });
    });
}
```

#### Show (GET specific article)
```php
public function show(Article $article): Collection
{
    return Harmony::createCollection(function (Collection $collection) use ($article) {
        $collection->group(function (Request $request) use ($article) {
            $request->endpoint('api/articles/' . $article->id);
            $request->method('GET');
        });
    });
}
```

#### Delete (DELETE article)
```php
public function delete(Article $article): Collection
{
    return Harmony::createCollection(function (Collection $collection) use ($article) {
        $collection->group(function (Request $request) use ($article) {
            $request->endpoint('api/articles/' . $article->id);
            $request->method('DELETE');
        });
    });
}
```

#### Search (GET with query parameters)
```php
public function search(): Collection
{
    return Harmony::createCollection(function (Collection $collection) {
        $collection->group(function (Request $request) {
            $request->endpoint('api/articles/search');
            $request->method('GET');
            $request->query(function (Query $query) {
                $query->field('q');
                $query->field('category');
                $query->field('author');
                $query->field('status');
                $query->field('page');
                $query->field('per_page');
            });
        });
    });
}
```

## Making API Requests

### Basic Usage
```php
use Harmony\Facade\Harmony;

// Get all articles
$response = Harmony::send(new ArticleHarmony()->index(), null, $query);

// Create new article
$response = Harmony::send(new ArticleHarmony()->create(), $body);

// Update article
$response = Harmony::send(new ArticleHarmony()->update($article), $body);

// Get specific article
$response = Harmony::send(new ArticleHarmony()->show($article));

// Delete article
$response = Harmony::send(new ArticleHarmony()->delete($article));

// Search articles
$response = Harmony::send(new ArticleHarmony()->search(), null, $query);
```

### Complete Examples

#### 1. Get All Articles
```php
$query = [
    'title' => 'Laravel Tutorial',
    'keywords' => 'php,laravel,api'
];

$response = Harmony::send(new ArticleHarmony()->index(), null, $query);

if ($response->successful()) {
    $articles = $response->json();
    return $articles;
}

return $response->throw();
```

#### 2. Create New Article
```php
$body = [
    'title' => 'New Laravel Article',
    'keywords' => 'laravel,php,web-development',
    'content' => 'This is a comprehensive guide to Laravel development...',
    'status' => 'published',
    'author_id' => 1
];

$response = Harmony::send(new ArticleHarmony()->create(), $body);

if ($response->successful()) {
    $newArticle = $response->json();
    return $newArticle;
}

return $response->throw();
```

#### 3. Update Article
```php
$article = new Article(['id' => 1]);
$body = [
    'title' => 'Updated Laravel Article Title',
    'keywords' => 'laravel,php,api,updated',
    'content' => 'This is the updated content for the article...',
    'status' => 'published'
];

$response = Harmony::send(new ArticleHarmony()->update($article), $body);

if ($response->successful()) {
    $updatedArticle = $response->json();
    return $updatedArticle;
}

return $response->throw();
```

## Testing the Implementation

### Available Test Routes
The following test routes are available for testing the Harmony package:

- `GET /harmony-test/articles` - Test getting all articles
- `POST /harmony-test/articles` - Test creating a new article
- `GET /harmony-test/articles/{articleId}` - Test getting a specific article
- `PUT /harmony-test/articles/{articleId}` - Test updating an article
- `DELETE /harmony-test/articles/{articleId}` - Test deleting an article
- `GET /harmony-test/articles/search` - Test searching articles
- `GET /harmony-test/workflow` - Test complete CRUD workflow
- `GET /harmony-test/errors` - Test error handling

### Testing with cURL
```bash
# Get all articles
curl -X GET http://your-app.test/harmony-test/articles

# Create new article
curl -X POST http://your-app.test/harmony-test/articles

# Get specific article
curl -X GET http://your-app.test/harmony-test/articles/1

# Update article
curl -X PUT http://your-app.test/harmony-test/articles/1

# Delete article
curl -X DELETE http://your-app.test/harmony-test/articles/1

# Search articles
curl -X GET http://your-app.test/harmony-test/articles/search

# Test complete workflow
curl -X GET http://your-app.test/harmony-test/workflow

# Test error handling
curl -X GET http://your-app.test/harmony-test/errors
```

## Error Handling

### Basic Error Handling
```php
try {
    $response = Harmony::send(new ArticleHarmony()->index());
    
    if ($response->successful()) {
        return $response->json();
    }
    
    // Handle different HTTP status codes
    switch ($response->status()) {
        case 401:
            throw new \Exception('Unauthorized: Check your API token');
        case 403:
            throw new \Exception('Forbidden: You do not have permission to access this resource');
        case 404:
            throw new \Exception('Not Found: The requested resource was not found');
        case 422:
            throw new \Exception('Validation Error: ' . $response->json('message'));
        case 500:
            throw new \Exception('Server Error: The API server encountered an error');
        default:
            throw new \Exception('API Error: ' . $response->body());
    }
    
} catch (\Exception $e) {
    \Log::error('Harmony API Error: ' . $e->getMessage());
    
    return [
        'success' => false,
        'error' => $e->getMessage(),
        'status_code' => $response->status() ?? 'unknown'
    ];
}
```

## Configuration Options

### Authentication Types
The package supports three authentication types:

1. **None** - No authentication
2. **Bearer** - Bearer token authentication
3. **Basic** - Basic authentication

### Example Configuration
```php
public static function connector(): Connector
{
    return Harmony::createConnector(function (Connector $connector) {
        $connector
        ->authorization(function (Authorization $authorization) {
            $authorization->type('bearer'); // none, bearer, basic
        })
        ->header(function (Header $header){
            $header->type('Content-Type', 'application/json');
            $header->type('Accept', 'application/json');
        });
    });
}
```

## Key Features

- **Fluent API**: Easy-to-read, chainable methods
- **Structured Requests**: Predefined request templates with validation
- **Multiple Auth Types**: Supports none, bearer token, and basic auth
- **Nested Body Support**: Can handle complex JSON structures
- **Query Parameter Management**: Organized query parameter handling
- **Laravel Integration**: Uses Laravel's HTTP client and facade system
- **Error Handling**: Comprehensive error handling and logging
- **Testing Support**: Built-in test routes and examples

## Best Practices

1. **Always handle errors** - Use try-catch blocks and proper error handling
2. **Validate responses** - Check response status before processing data
3. **Use meaningful method names** - Make your API methods self-documenting
4. **Group related functionality** - Keep related API methods in the same Harmony class
5. **Document your API structure** - Use comments to explain complex request structures
6. **Test thoroughly** - Use the provided test routes to verify functionality

## Troubleshooting

### Common Issues

1. **Class not found errors** - Ensure the Harmony package is properly installed and autoloaded
2. **Authentication errors** - Check your API token and base URL configuration
3. **Route not found** - Ensure routes are properly registered in `web.php`
4. **Response parsing errors** - Verify the API response format matches your expectations

### Debug Tips

1. Use `dd($response)` to inspect response objects
2. Check Laravel logs for detailed error messages
3. Use the test routes to verify basic functionality
4. Test with simple requests before complex ones
