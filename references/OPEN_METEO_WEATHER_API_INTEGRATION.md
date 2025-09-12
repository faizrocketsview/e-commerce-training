# Open-Meteo Weather API Integration with Harmony Package

**Author**: Faiz Nasir  
**Date**: September 12, 2025  
**Version**: 1.0  
**Status**: Production Ready

---

## Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Installation & Setup](#installation--setup)
4. [API Endpoints](#api-endpoints)
5. [Usage Examples](#usage-examples)
6. [Weather Data Structure](#weather-data-structure)
7. [Error Handling](#error-handling)
8. [Best Practices](#best-practices)
9. [Testing Guide](#testing-guide)
10. [Troubleshooting](#troubleshooting)
11. [API Reference](#api-reference)

---

## Overview

This documentation covers the integration of the Open-Meteo Weather API with the Harmony package in Laravel. The Open-Meteo API provides free, open-source weather data without requiring an API key, making it perfect for development and production applications.

### Key Features

- 🌤️ **Free Weather Data**: No API key required
- 🌍 **Global Coverage**: Worldwide weather data
- 📊 **Multiple Data Types**: Current, forecast, historical, air quality
- ⚡ **High Performance**: Fast response times
- 🔧 **Easy Integration**: Simple Harmony package integration
- 📱 **Mobile Ready**: JSON responses perfect for mobile apps

### Supported Weather Services

| Service | Description | Endpoint |
|---------|-------------|----------|
| Weather Forecast | 7-16 day forecasts | `/weather/forecast` |
| Current Weather | Real-time conditions | `/weather/current` |
| Historical Weather | Past weather data | `/weather/historical` |
| Air Quality | Air pollution data | `/weather/air-quality` |
| Elevation | Height above sea level | `/weather/elevation` |
| Marine Weather | Ocean weather conditions | `/weather/marine` |
| Geocoding | Location search | `/weather/geocoding` |

---

## Prerequisites

Before implementing the Open-Meteo API integration, ensure you have:

- ✅ Laravel 8.0+ installed
- ✅ Harmony package installed (`aisyahhanifiahrv/harmony`)
- ✅ Guzzle HTTP client (usually included with Laravel)
- ✅ PHP 7.4+ with cURL extension
- ✅ Internet connection for API calls

---

## Installation & Setup

### 1. Install Harmony Package

```bash
composer require aisyahhanifiahrv/harmony:dev-main
```

### 2. Register Service Provider

The Harmony package is automatically registered via `composer.json`. If manual registration is needed, add to `config/app.php`:

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

### 4. Files Created

The integration creates the following files:

```
app/
├── Harmony/
│   └── OpenMeteoHarmony.php          # Main Harmony class
├── Http/Controllers/
│   └── WeatherController.php         # API controller
└── Models/
    └── Article.php                   # Example model

routes/
└── web.php                          # Updated with weather routes

references/
└── OPEN_METEO_WEATHER_API_INTEGRATION.md  # This documentation
```

---

## API Endpoints

### Base URL
```
http://your-domain.com/weather/
```

### 1. Weather Forecast

**Endpoint**: `GET /weather/forecast`

Get weather forecast for up to 16 days with hourly and daily data.

**Parameters**:
- `latitude` (required): Latitude coordinate (-90 to 90)
- `longitude` (required): Longitude coordinate (-180 to 180)
- `forecast_days` (optional): Number of days (1-16, default: 7)
- `timezone` (optional): Timezone identifier (default: 'auto')
- `temperature_unit` (optional): 'celsius' or 'fahrenheit' (default: 'celsius')
- `windspeed_unit` (optional): 'kmh', 'ms', 'mph', 'knots' (default: 'kmh')
- `precipitation_unit` (optional): 'mm' or 'inches' (default: 'mm')

**Example**:
```bash
curl -X GET "http://localhost:8000/weather/forecast?latitude=52.52&longitude=13.405&forecast_days=3"
```

### 2. Current Weather

**Endpoint**: `GET /weather/current`

Get real-time weather conditions.

**Parameters**:
- `latitude` (required): Latitude coordinate (-90 to 90)
- `longitude` (required): Longitude coordinate (-180 to 180)
- `timezone` (optional): Timezone identifier (default: 'auto')

**Example**:
```bash
curl -X GET "http://localhost:8000/weather/current?latitude=52.52&longitude=13.405"
```

### 3. Historical Weather

**Endpoint**: `GET /weather/historical`

Get historical weather data for a specific date range.

**Parameters**:
- `latitude` (required): Latitude coordinate (-90 to 90)
- `longitude` (required): Longitude coordinate (-180 to 180)
- `start_date` (required): Start date (YYYY-MM-DD)
- `end_date` (required): End date (YYYY-MM-DD)
- `timezone` (optional): Timezone identifier (default: 'auto')

**Example**:
```bash
curl -X GET "http://localhost:8000/weather/historical?latitude=52.52&longitude=13.405&start_date=2025-09-01&end_date=2025-09-10"
```

### 4. Air Quality

**Endpoint**: `GET /weather/air-quality`

Get air quality forecast and current conditions.

**Parameters**:
- `latitude` (required): Latitude coordinate (-90 to 90)
- `longitude` (required): Longitude coordinate (-180 to 180)
- `forecast_days` (optional): Number of days (1-7, default: 7)

**Example**:
```bash
curl -X GET "http://localhost:8000/weather/air-quality?latitude=52.52&longitude=13.405&forecast_days=3"
```

### 5. Elevation

**Endpoint**: `GET /weather/elevation`

Get elevation data for coordinates.

**Parameters**:
- `latitude` (required): Latitude coordinate (-90 to 90)
- `longitude` (required): Longitude coordinate (-180 to 180)

**Example**:
```bash
curl -X GET "http://localhost:8000/weather/elevation?latitude=52.52&longitude=13.405"
```

### 6. Marine Weather

**Endpoint**: `GET /weather/marine`

Get marine weather forecast for coastal areas.

**Parameters**:
- `latitude` (required): Latitude coordinate (-90 to 90)
- `longitude` (required): Longitude coordinate (-180 to 180)
- `forecast_days` (optional): Number of days (1-7, default: 7)

**Example**:
```bash
curl -X GET "http://localhost:8000/weather/marine?latitude=52.52&longitude=13.405&forecast_days=3"
```

### 7. Geocoding

**Endpoint**: `GET /weather/geocoding`

Search for locations by name and get coordinates.

**Parameters**:
- `name` (required): Place name to search for
- `count` (optional): Number of results (1-100, default: 10)
- `language` (optional): Language code (default: 'en')

**Example**:
```bash
curl -X GET "http://localhost:8000/weather/geocoding?name=Kuala%20Lumpur&count=5"
```

### 8. Test All Endpoints

**Endpoint**: `GET /weather/test-all`

Test all weather API endpoints with Berlin, Germany coordinates.

**Example**:
```bash
curl -X GET "http://localhost:8000/weather/test-all"
```

---

## Usage Examples

### 1. Basic Weather Forecast

```php
use App\Harmony\OpenMeteoHarmony;
use Harmony\Facade\Harmony;

// Create Harmony instance
$openMeteo = new OpenMeteoHarmony();

// Get forecast collection
$collection = $openMeteo->getForecast(
    latitude: 52.52,
    longitude: 13.405,
    hourlyVariables: ['temperature_2m', 'precipitation', 'weather_code'],
    dailyVariables: ['temperature_2m_max', 'temperature_2m_min'],
    forecastDays: 7,
    timezone: 'auto'
);

// Prepare query parameters
$query = [
    'latitude' => 52.52,
    'longitude' => 13.405,
    'hourly' => 'temperature_2m,precipitation,weather_code',
    'daily' => 'temperature_2m_max,temperature_2m_min',
    'forecast_days' => 7,
    'timezone' => 'auto',
    'temperature_unit' => 'celsius',
    'windspeed_unit' => 'kmh',
    'precipitation_unit' => 'mm',
];

// Send request
$response = Harmony::send($collection, null, $query);

// Handle response
if ($response->successful()) {
    $weatherData = $response->json();
    // Process weather data
    echo "Temperature: " . $weatherData['hourly']['temperature_2m'][0] . "°C";
} else {
    echo "Error: " . $response->body();
}
```

### 2. Current Weather with Error Handling

```php
use App\Harmony\OpenMeteoHarmony;
use Harmony\Facade\Harmony;

try {
    $openMeteo = new OpenMeteoHarmony();
    $collection = $openMeteo->getCurrentWeather(
        latitude: 52.52,
        longitude: 13.405,
        currentVariables: ['temperature_2m', 'weather_code', 'wind_speed_10m']
    );

    $query = [
        'latitude' => 52.52,
        'longitude' => 13.405,
        'current' => 'temperature_2m,weather_code,wind_speed_10m',
        'timezone' => 'auto',
    ];

    $response = Harmony::send($collection, null, $query);

    if ($response->successful()) {
        $data = $response->json();
        $current = $data['current'];
        
        echo "Current Temperature: " . $current['temperature_2m'] . "°C\n";
        echo "Weather Code: " . $current['weather_code'] . "\n";
        echo "Wind Speed: " . $current['wind_speed_10m'] . " km/h\n";
    } else {
        throw new \Exception('API request failed: ' . $response->body());
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### 3. Historical Weather Data

```php
use App\Harmony\OpenMeteoHarmony;
use Harmony\Facade\Harmony;

$openMeteo = new OpenMeteoHarmony();
$collection = $openMeteo->getHistoricalWeather(
    latitude: 52.52,
    longitude: 13.405,
    startDate: '2025-09-01',
    endDate: '2025-09-10',
    dailyVariables: ['temperature_2m_max', 'temperature_2m_min', 'precipitation_sum'],
    timezone: 'auto'
);

$query = [
    'latitude' => 52.52,
    'longitude' => 13.405,
    'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum',
    'start_date' => '2025-09-01',
    'end_date' => '2025-09-10',
    'timezone' => 'auto',
];

$response = Harmony::send($collection, null, $query);

if ($response->successful()) {
    $data = $response->json();
    $daily = $data['daily'];
    
    foreach ($daily['time'] as $index => $date) {
        echo "Date: $date\n";
        echo "Max Temp: " . $daily['temperature_2m_max'][$index] . "°C\n";
        echo "Min Temp: " . $daily['temperature_2m_min'][$index] . "°C\n";
        echo "Precipitation: " . $daily['precipitation_sum'][$index] . "mm\n\n";
    }
}
```

---

## Weather Data Structure

### Forecast Response Structure

```json
{
  "success": true,
  "data": {
    "latitude": 52.52,
    "longitude": 13.4,
    "generationtime_ms": 0.19466876983642578,
    "utc_offset_seconds": 7200,
    "timezone": "Europe/Berlin",
    "timezone_abbreviation": "GMT+2",
    "elevation": 37,
    "hourly_units": {
      "time": "iso8601",
      "temperature_2m": "°C",
      "precipitation": "mm",
      "weather_code": "wmo code"
    },
    "hourly": {
      "time": ["2025-09-12T00:00", "2025-09-12T01:00", ...],
      "temperature_2m": [15.8, 15.7, 14.6, ...],
      "precipitation": [0, 0, 0, ...],
      "weather_code": [3, 2, 1, ...]
    },
    "daily_units": {
      "time": "iso8601",
      "weather_code": "wmo code",
      "temperature_2m_max": "°C",
      "temperature_2m_min": "°C"
    },
    "daily": {
      "time": ["2025-09-12", "2025-09-13", "2025-09-14"],
      "weather_code": [61, 96, 80],
      "temperature_2m_max": [20.6, 20.9, 16.5],
      "temperature_2m_min": [12.9, 11.6, 12.3]
    }
  },
  "location": {
    "latitude": 52.52,
    "longitude": 13.405,
    "timezone": "auto"
  }
}
```

### Current Weather Response Structure

```json
{
  "success": true,
  "data": {
    "latitude": 52.52,
    "longitude": 13.4,
    "timezone": "Europe/Berlin",
    "current_units": {
      "time": "iso8601",
      "temperature_2m": "°C",
      "weather_code": "wmo code",
      "wind_speed_10m": "km/h"
    },
    "current": {
      "time": "2025-09-12T05:15",
      "temperature_2m": 13.2,
      "weather_code": 2,
      "wind_speed_10m": 10.3,
      "precipitation": 0
    }
  }
}
```

### Weather Code Interpretation

| Code | Description | Icon |
|------|-------------|------|
| 0 | Clear sky | ☀️ |
| 1, 2, 3 | Mainly clear, partly cloudy, overcast | ☀️/⛅/☁️ |
| 45, 48 | Fog and depositing rime fog | 🌫️ |
| 51, 53, 55 | Drizzle: Light, moderate, dense | 🌦️ |
| 61, 63, 65 | Rain: Slight, moderate, heavy | 🌧️ |
| 71, 73, 75 | Snow fall: Slight, moderate, heavy | ❄️ |
| 80, 81, 82 | Rain showers: Slight, moderate, violent | 🌦️ |
| 95 | Thunderstorm: Slight or moderate | ⛈️ |
| 96, 99 | Thunderstorm with hail | ⛈️ |

---

## Error Handling

### Common Error Responses

#### 1. Validation Errors

```json
{
  "success": false,
  "error": "The latitude field is required."
}
```

#### 2. API Errors

```json
{
  "success": false,
  "error": "Weather API request failed",
  "status_code": 400
}
```

#### 3. Network Errors

```json
{
  "success": false,
  "error": "Connection timeout"
}
```

### Error Handling Best Practices

```php
try {
    $response = Harmony::send($collection, null, $query);
    
    if ($response->successful()) {
        $data = $response->json();
        // Process successful response
    } else {
        // Handle API errors
        switch ($response->status()) {
            case 400:
                throw new \Exception('Bad Request: Invalid parameters');
            case 404:
                throw new \Exception('Not Found: Location not found');
            case 429:
                throw new \Exception('Rate Limited: Too many requests');
            case 500:
                throw new \Exception('Server Error: API server error');
            default:
                throw new \Exception('API Error: ' . $response->body());
        }
    }
} catch (\Exception $e) {
    // Log error
    \Log::error('Weather API Error: ' . $e->getMessage());
    
    // Return user-friendly error
    return response()->json([
        'success' => false,
        'error' => 'Unable to fetch weather data. Please try again later.'
    ], 500);
}
```

---

## Best Practices

### 1. Caching Strategy

```php
// Cache weather data for 10 minutes
$cacheKey = "weather_forecast_{$latitude}_{$longitude}_{$forecastDays}";
$weatherData = Cache::remember($cacheKey, 600, function () use ($collection, $query) {
    $response = Harmony::send($collection, null, $query);
    return $response->json();
});
```

### 2. Input Validation

```php
$request->validate([
    'latitude' => 'required|numeric|between:-90,90',
    'longitude' => 'required|numeric|between:-180,180',
    'forecast_days' => 'integer|min:1|max:16',
    'timezone' => 'string|max:50',
]);
```

### 3. Rate Limiting

```php
// Implement rate limiting
RateLimiter::attempt(
    'weather-api:' . $request->ip(),
    100, // 100 requests
    function () {
        // Process weather request
    },
    3600 // per hour
);
```

### 4. Response Optimization

```php
// Only request needed variables
$hourlyVariables = ['temperature_2m', 'precipitation'];
$dailyVariables = ['temperature_2m_max', 'temperature_2m_min'];

// Use appropriate forecast length
$forecastDays = min($request->input('forecast_days', 7), 16);
```

### 5. Error Logging

```php
// Log all API errors
if (!$response->successful()) {
    \Log::error('Open-Meteo API Error', [
        'status' => $response->status(),
        'body' => $response->body(),
        'url' => $response->effectiveUri(),
        'parameters' => $query
    ]);
}
```

---

## Testing Guide

### 1. Test All Endpoints

```bash
# Test all weather endpoints
curl -X GET "http://localhost:8000/weather/test-all"
```

### 2. Individual Endpoint Testing

```bash
# Test forecast
curl -X GET "http://localhost:8000/weather/forecast?latitude=52.52&longitude=13.405&forecast_days=3"

# Test current weather
curl -X GET "http://localhost:8000/weather/current?latitude=52.52&longitude=13.405"

# Test elevation
curl -X GET "http://localhost:8000/weather/elevation?latitude=52.52&longitude=13.405"
```

### 3. Unit Testing

```php
// tests/Feature/WeatherApiTest.php
public function test_weather_forecast_endpoint()
{
    $response = $this->get('/weather/forecast?latitude=52.52&longitude=13.405');
    
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
```

### 4. Performance Testing

```bash
# Test response times
time curl -X GET "http://localhost:8000/weather/forecast?latitude=52.52&longitude=13.405"

# Test with different locations
curl -X GET "http://localhost:8000/weather/forecast?latitude=3.139&longitude=101.686" # Kuala Lumpur
curl -X GET "http://localhost:8000/weather/forecast?latitude=40.7128&longitude=-74.0060" # New York
```

---

## Troubleshooting

### Common Issues

#### 1. 404 Not Found

**Problem**: API endpoint returns 404  
**Solution**: Check if routes are properly registered
```bash
php artisan route:list | grep weather
```

#### 2. Validation Errors

**Problem**: Latitude/longitude validation fails  
**Solution**: Ensure coordinates are within valid ranges
- Latitude: -90 to 90
- Longitude: -180 to 180

#### 3. Timeout Errors

**Problem**: API requests timeout  
**Solution**: Check internet connection and increase timeout
```php
// In OpenMeteoHarmony.php
$httpRequest = Http::timeout(30)->withHeaders($harmony->connector()->header->headers);
```

#### 4. Memory Issues

**Problem**: Large responses cause memory issues  
**Solution**: Limit forecast days and variables
```php
$forecastDays = min($request->input('forecast_days', 7), 7);
$hourlyVariables = ['temperature_2m', 'precipitation']; // Limit variables
```

### Debug Mode

Enable debug mode for detailed error information:

```php
// In WeatherController.php
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

### OpenMeteoHarmony Class Methods

#### `getForecast(float $latitude, float $longitude, array $hourlyVariables, array $dailyVariables, int $forecastDays, string $timezone)`

Get weather forecast for a location.

**Parameters**:
- `$latitude`: Latitude coordinate (-90 to 90)
- `$longitude`: Longitude coordinate (-180 to 180)
- `$hourlyVariables`: Array of hourly weather variables
- `$dailyVariables`: Array of daily weather variables
- `$forecastDays`: Number of forecast days (1-16)
- `$timezone`: Timezone identifier

**Returns**: `Collection` object for Harmony::send()

#### `getCurrentWeather(float $latitude, float $longitude, array $currentVariables)`

Get current weather conditions.

**Parameters**:
- `$latitude`: Latitude coordinate (-90 to 90)
- `$longitude`: Longitude coordinate (-180 to 180)
- `$currentVariables`: Array of current weather variables

**Returns**: `Collection` object for Harmony::send()

#### `getHistoricalWeather(float $latitude, float $longitude, string $startDate, string $endDate, array $dailyVariables, string $timezone)`

Get historical weather data.

**Parameters**:
- `$latitude`: Latitude coordinate (-90 to 90)
- `$longitude`: Longitude coordinate (-180 to 180)
- `$startDate`: Start date (YYYY-MM-DD)
- `$endDate`: End date (YYYY-MM-DD)
- `$dailyVariables`: Array of daily weather variables
- `$timezone`: Timezone identifier

**Returns**: `Collection` object for Harmony::send()

### Available Weather Variables

#### Hourly Variables
- `temperature_2m`: Temperature at 2 meters
- `relative_humidity_2m`: Relative humidity at 2 meters
- `precipitation`: Total precipitation (rain + snow)
- `weather_code`: Weather condition code
- `wind_speed_10m`: Wind speed at 10 meters
- `wind_direction_10m`: Wind direction at 10 meters
- `cloud_cover`: Total cloud cover percentage

#### Daily Variables
- `weather_code`: Daily weather condition code
- `temperature_2m_max`: Maximum temperature
- `temperature_2m_min`: Minimum temperature
- `precipitation_sum`: Total daily precipitation
- `wind_speed_10m_max`: Maximum wind speed

#### Current Variables
- `temperature_2m`: Current temperature
- `relative_humidity_2m`: Current humidity
- `weather_code`: Current weather condition
- `wind_speed_10m`: Current wind speed
- `precipitation`: Current precipitation

---

## Support & Resources

### Official Documentation
- [Open-Meteo API Documentation](https://open-meteo.com/en/docs)
- [Harmony Package Documentation](https://github.com/aisyahhanifiahrv/harmony)

### Contact Information
- **Developer**: Faiz Nasir
- **Email**: faiznasir@rocketsview.com
- **Project**: E-commerce Training Application

### License
This integration is provided under the MIT License. The Open-Meteo API is free to use for both commercial and non-commercial purposes.

---

**Last Updated**: September 12, 2025  
**Documentation Version**: 1.0  
**API Version**: Open-Meteo v1
