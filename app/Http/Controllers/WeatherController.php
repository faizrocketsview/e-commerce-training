<?php

namespace App\Http\Controllers;

use App\Harmony\OpenMeteoHarmony;
use Harmony\Facade\Harmony;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * WeatherController - Open-Meteo API Integration
 * 
 * This controller demonstrates how to use the Harmony package
 * with the Open-Meteo weather API for various weather data requests.
 * 
 * @author Faiz Nasir
 */
class WeatherController extends Controller
{
    protected $openMeteoHarmony;

    public function __construct(OpenMeteoHarmony $openMeteoHarmony)
    {
        $this->openMeteoHarmony = $openMeteoHarmony;
    }

    /**
     * Get weather forecast for a location
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getForecast(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'forecast_days' => 'integer|min:1|max:16',
                'timezone' => 'string|max:50',
                'temperature_unit' => 'string|in:celsius,fahrenheit',
                'windspeed_unit' => 'string|in:kmh,ms,mph,knots',
                'precipitation_unit' => 'string|in:mm,inches',
            ]);

            $latitude = (float) $request->input('latitude');
            $longitude = (float) $request->input('longitude');
            $forecastDays = $request->input('forecast_days', 7);
            $timezone = $request->input('timezone', 'auto');
            $temperatureUnit = $request->input('temperature_unit', 'celsius');
            $windspeedUnit = $request->input('windspeed_unit', 'kmh');
            $precipitationUnit = $request->input('precipitation_unit', 'mm');

            // Default weather variables
            $hourlyVariables = [
                'temperature_2m',
                'relative_humidity_2m',
                'precipitation',
                'weather_code',
                'wind_speed_10m',
                'wind_direction_10m'
            ];

            $dailyVariables = [
                'weather_code',
                'temperature_2m_max',
                'temperature_2m_min',
                'precipitation_sum',
                'wind_speed_10m_max'
            ];

            $collection = $this->openMeteoHarmony->getForecast(
                $latitude,
                $longitude,
                $hourlyVariables,
                $dailyVariables,
                $forecastDays,
                $timezone
            );

            $query = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'hourly' => implode(',', $hourlyVariables),
                'daily' => implode(',', $dailyVariables),
                'forecast_days' => $forecastDays,
                'timezone' => $timezone,
                'temperature_unit' => $temperatureUnit,
                'windspeed_unit' => $windspeedUnit,
                'precipitation_unit' => $precipitationUnit,
            ];

            $response = Harmony::send($collection, null, $query);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                    'location' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'timezone' => $timezone
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Weather API request failed',
                'status_code' => $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current weather conditions
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getCurrentWeather(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'timezone' => 'string|max:50',
            ]);

            $latitude = (float) $request->input('latitude');
            $longitude = (float) $request->input('longitude');
            $timezone = $request->input('timezone', 'auto');

            $currentVariables = [
                'temperature_2m',
                'relative_humidity_2m',
                'weather_code',
                'wind_speed_10m',
                'wind_direction_10m',
                'precipitation',
                'cloud_cover'
            ];

            $collection = $this->openMeteoHarmony->getCurrentWeather(
                $latitude,
                $longitude,
                $currentVariables
            );

            $query = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => implode(',', $currentVariables),
                'timezone' => $timezone,
                'temperature_unit' => 'celsius',
                'windspeed_unit' => 'kmh',
                'precipitation_unit' => 'mm',
            ];

            $response = Harmony::send($collection, null, $query);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                    'location' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'timezone' => $timezone
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Current weather API request failed',
                'status_code' => $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get historical weather data
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getHistoricalWeather(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'start_date' => 'required|date|before_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:today',
                'timezone' => 'string|max:50',
            ]);

            $latitude = (float) $request->input('latitude');
            $longitude = (float) $request->input('longitude');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $timezone = $request->input('timezone', 'auto');

            $dailyVariables = [
                'weather_code',
                'temperature_2m_max',
                'temperature_2m_min',
                'precipitation_sum',
                'wind_speed_10m_max',
                'wind_direction_10m_dominant'
            ];

            $collection = $this->openMeteoHarmony->getHistoricalWeather(
                $latitude,
                $longitude,
                $startDate,
                $endDate,
                $dailyVariables,
                $timezone
            );

            $query = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'daily' => implode(',', $dailyVariables),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'timezone' => $timezone,
                'temperature_unit' => 'celsius',
                'windspeed_unit' => 'kmh',
                'precipitation_unit' => 'mm',
            ];

            $response = Harmony::send($collection, null, $query);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                    'location' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'timezone' => $timezone
                    ],
                    'period' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Historical weather API request failed',
                'status_code' => $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get air quality forecast
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAirQuality(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'forecast_days' => 'integer|min:1|max:7',
            ]);

            $latitude = (float) $request->input('latitude');
            $longitude = (float) $request->input('longitude');
            $forecastDays = $request->input('forecast_days', 7);

            $hourlyVariables = [
                'pm10',
                'pm2_5',
                'carbon_monoxide',
                'nitrogen_dioxide',
                'sulphur_dioxide',
                'ozone',
                'aerosol_optical_depth',
                'dust',
                'uv_index'
            ];

            $collection = $this->openMeteoHarmony->getAirQuality(
                $latitude,
                $longitude,
                $hourlyVariables,
                $forecastDays
            );

            $query = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'hourly' => implode(',', $hourlyVariables),
                'forecast_days' => $forecastDays,
                'timezone' => 'auto',
            ];

            $response = Harmony::send($collection, null, $query);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                    'location' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Air quality API request failed',
                'status_code' => $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get geocoding information for a location
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getGeocoding(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|min:2|max:100',
                'count' => 'integer|min:1|max:100',
                'language' => 'string|max:10',
            ]);

            $name = $request->input('name');
            $count = $request->input('count', 10);
            $language = $request->input('language', 'en');

            $collection = $this->openMeteoHarmony->getGeocoding($name, $count, $language);

            $query = [
                'name' => $name,
                'count' => $count,
                'language' => $language,
                'format' => 'json',
            ];

            $response = Harmony::send($collection, null, $query);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                    'search' => [
                        'name' => $name,
                        'count' => $count,
                        'language' => $language
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Geocoding API request failed',
                'status_code' => $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get elevation data for coordinates
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getElevation(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            $latitude = (float) $request->input('latitude');
            $longitude = (float) $request->input('longitude');

            $collection = $this->openMeteoHarmony->getElevation($latitude, $longitude);

            $query = [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];

            $response = Harmony::send($collection, null, $query);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                    'location' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Elevation API request failed',
                'status_code' => $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get marine weather forecast
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getMarineWeather(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'forecast_days' => 'integer|min:1|max:7',
            ]);

            $latitude = (float) $request->input('latitude');
            $longitude = (float) $request->input('longitude');
            $forecastDays = $request->input('forecast_days', 7);

            $hourlyVariables = [
                'wave_height',
                'wave_direction',
                'wave_period',
                'swell_wave_height',
                'swell_wave_direction',
                'swell_wave_period',
                'wind_wave_height',
                'wind_wave_direction',
                'wind_wave_period'
            ];

            $collection = $this->openMeteoHarmony->getMarineWeather(
                $latitude,
                $longitude,
                $hourlyVariables,
                $forecastDays
            );

            $query = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'hourly' => implode(',', $hourlyVariables),
                'forecast_days' => $forecastDays,
                'timezone' => 'auto',
            ];

            $response = Harmony::send($collection, null, $query);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                    'location' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Marine weather API request failed',
                'status_code' => $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test all weather API endpoints
     * 
     * @return JsonResponse
     */
    public function testAllEndpoints(): JsonResponse
    {
        try {
            // Test coordinates (Kuchai Lama, Malaysia)
            $latitude = 3.1390;
            $longitude = 101.6869;

            $results = [];

            // Test forecast
            try {
                $collection = $this->openMeteoHarmony->getForecast($latitude, $longitude);
                $query = [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'hourly' => 'temperature_2m,precipitation',
                    'daily' => 'weather_code,temperature_2m_max,temperature_2m_min',
                    'forecast_days' => 3,
                    'timezone' => 'auto',
                    'temperature_unit' => 'celsius',
                    'windspeed_unit' => 'kmh',
                    'precipitation_unit' => 'mm',
                ];
                $response = Harmony::send($collection, null, $query);
                $results['forecast'] = $response->successful() ? 'SUCCESS' : 'FAILED';
            } catch (\Exception $e) {
                $results['forecast'] = 'ERROR: ' . $e->getMessage();
            }

            // Test current weather
            try {
                $collection = $this->openMeteoHarmony->getCurrentWeather($latitude, $longitude);
                $query = [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'current' => 'temperature_2m,weather_code,wind_speed_10m',
                    'timezone' => 'auto',
                ];
                $response = Harmony::send($collection, null, $query);
                $results['current_weather'] = $response->successful() ? 'SUCCESS' : 'FAILED';
            } catch (\Exception $e) {
                $results['current_weather'] = 'ERROR: ' . $e->getMessage();
            }

            // Test geocoding
            try {
                $collection = $this->openMeteoHarmony->getGeocoding('Berlin');
                $query = [
                    'name' => 'Berlin',
                    'count' => 5,
                    'language' => 'en',
                    'format' => 'json',
                ];
                $response = Harmony::send($collection, null, $query);
                $results['geocoding'] = $response->successful() ? 'SUCCESS' : 'FAILED';
            } catch (\Exception $e) {
                $results['geocoding'] = 'ERROR: ' . $e->getMessage();
            }

            // Test elevation
            try {
                $collection = $this->openMeteoHarmony->getElevation($latitude, $longitude);
                $query = [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ];
                $response = Harmony::send($collection, null, $query);
                $results['elevation'] = $response->successful() ? 'SUCCESS' : 'FAILED';
            } catch (\Exception $e) {
                $results['elevation'] = 'ERROR: ' . $e->getMessage();
            }

            return response()->json([
                'success' => true,
                'message' => 'Open-Meteo API Integration Test Results',
                'test_location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'name' => 'Kuchai Lama, Malaysia'
                ],
                'results' => $results,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
