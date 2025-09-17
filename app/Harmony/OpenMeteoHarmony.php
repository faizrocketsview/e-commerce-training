<?php

namespace App\Harmony;

use Harmony\Collection;
use Harmony\Collection\Body;
use Harmony\Collection\Query;
use Harmony\Collection\Request;
use Harmony\Connector;
use Harmony\Connector\Authorization;
use Harmony\Connector\Header;
use Harmony\Harmony;

/**
 * OpenMeteoHarmony - Weather API Integration
 * 
 * This class provides methods to interact with the Open-Meteo weather API
 * using the Harmony package for structured API requests.
 * by Faiz Nasir
 * 
 * @see https://open-meteo.com/en/docs
 */
class OpenMeteoHarmony
{
    public static $baseUrl = 'https://api.open-meteo.com/v1/';
    public static $token = null; // Open-Meteo API doesn't require authentication
    // public static $username;
    // public static $password;

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
                $header->type('User-Agent', 'Laravel-Harmony-Weather-App/1.0');
            });
        });
    }

    /**
     * Get weather forecast for a specific location
     * 
     * @param float $latitude
     * @param float $longitude
     * @param array $hourlyVariables
     * @param array $dailyVariables
     * @param int $forecastDays
     * @param string $timezone
     * @return Collection
     */
    public function getForecast(
        float $latitude, 
        float $longitude, 
        array $hourlyVariables = ['temperature_2m', 'precipitation'],
        array $dailyVariables = ['weather_code', 'temperature_2m_max', 'temperature_2m_min'],
        int $forecastDays = 7,
        string $timezone = 'auto'
    ): Collection {
        return Harmony::createCollection(function (Collection $collection) use (
            $latitude, 
            $longitude, 
            $hourlyVariables, 
            $dailyVariables, 
            $forecastDays, 
            $timezone
        ) {
            $collection->group(function (Request $request) use (
                $latitude, 
                $longitude, 
                $hourlyVariables, 
                $dailyVariables, 
                $forecastDays, 
                $timezone
            ) {
                $request->endpoint('forecast');
                $request->method('GET');

                $request->query(function (Query $query) use (
                    $latitude, 
                    $longitude, 
                    $hourlyVariables, 
                    $dailyVariables, 
                    $forecastDays, 
                    $timezone
                ) {
                    $query->field('latitude');
                    $query->field('longitude');
                    $query->field('hourly');
                    $query->field('daily');
                    $query->field('forecast_days');
                    $query->field('timezone');
                    $query->field('temperature_unit');
                    $query->field('windspeed_unit');
                    $query->field('precipitation_unit');
                });
            });
        });
    }

    /**
     * Get current weather conditions
     * 
     * @param float $latitude
     * @param float $longitude
     * @param array $currentVariables
     * @return Collection
     */
    public function getCurrentWeather(
        float $latitude, 
        float $longitude, 
        array $currentVariables = ['temperature_2m', 'relative_humidity_2m', 'weather_code', 'wind_speed_10m']
    ): Collection {
        return Harmony::createCollection(function (Collection $collection) use ($latitude, $longitude, $currentVariables) {
            $collection->group(function (Request $request) use ($latitude, $longitude, $currentVariables) {
                $request->endpoint('forecast');
                $request->method('GET');

                $request->query(function (Query $query) use ($latitude, $longitude, $currentVariables) {
                    $query->field('latitude');
                    $query->field('longitude');
                    $query->field('current');
                    $query->field('timezone');
                    $query->field('temperature_unit');
                    $query->field('windspeed_unit');
                    $query->field('precipitation_unit');
                });
            });
        });
    }

    /**
     * Get historical weather data
     * 
     * @param float $latitude
     * @param float $longitude
     * @param string $startDate
     * @param string $endDate
     * @param array $dailyVariables
     * @param string $timezone
     * @return Collection
     */
    public function getHistoricalWeather(
        float $latitude, 
        float $longitude, 
        string $startDate, 
        string $endDate, 
        array $dailyVariables = ['weather_code', 'temperature_2m_max', 'temperature_2m_min', 'precipitation_sum'],
        string $timezone = 'auto'
    ): Collection {
        return Harmony::createCollection(function (Collection $collection) use (
            $latitude, 
            $longitude, 
            $startDate, 
            $endDate, 
            $dailyVariables, 
            $timezone
        ) {
            $collection->group(function (Request $request) use (
                $latitude, 
                $longitude, 
                $startDate, 
                $endDate, 
                $dailyVariables, 
                $timezone
            ) {
                $request->endpoint('forecast');
                $request->method('GET');

                $request->query(function (Query $query) use (
                    $latitude, 
                    $longitude, 
                    $startDate, 
                    $endDate, 
                    $dailyVariables, 
                    $timezone
                ) {
                    $query->field('latitude');
                    $query->field('longitude');
                    $query->field('daily');
                    $query->field('start_date');
                    $query->field('end_date');
                    $query->field('timezone');
                    $query->field('temperature_unit');
                    $query->field('windspeed_unit');
                    $query->field('precipitation_unit');
                });
            });
        });
    }

    /**
     * Get air quality forecast
     * 
     * @param float $latitude
     * @param float $longitude
     * @param array $hourlyVariables
     * @param int $forecastDays
     * @return Collection
     */
    public function getAirQuality(
        float $latitude, 
        float $longitude, 
        array $hourlyVariables = ['pm10', 'pm2_5', 'carbon_monoxide', 'nitrogen_dioxide', 'sulphur_dioxide', 'ozone'],
        int $forecastDays = 7
    ): Collection {
        return Harmony::createCollection(function (Collection $collection) use ($latitude, $longitude, $hourlyVariables, $forecastDays) {
            $collection->group(function (Request $request) use ($latitude, $longitude, $hourlyVariables, $forecastDays) {
                $request->endpoint('air-quality');
                $request->method('GET');

                $request->query(function (Query $query) use ($latitude, $longitude, $hourlyVariables, $forecastDays) {
                    $query->field('latitude');
                    $query->field('longitude');
                    $query->field('hourly');
                    $query->field('forecast_days');
                    $query->field('timezone');
                });
            });
        });
    }

    /**
     * Get geocoding information for a location
     * 
     * @param string $name
     * @param int $count
     * @param string $language
     * @return Collection
     */
    public function getGeocoding(
        string $name, 
        int $count = 10, 
        string $language = 'en'
    ): Collection {
        return Harmony::createCollection(function (Collection $collection) use ($name, $count, $language) {
            $collection->group(function (Request $request) use ($name, $count, $language) {
                $request->endpoint('geocoding');
                $request->method('GET');

                $request->query(function (Query $query) use ($name, $count, $language) {
                    $query->field('name');
                    $query->field('count');
                    $query->field('language');
                    $query->field('format');
                });
            });
        });
    }

    /**
     * Get elevation data for coordinates
     * 
     * @param float $latitude
     * @param float $longitude
     * @return Collection
     */
    public function getElevation(
        float $latitude, 
        float $longitude
    ): Collection {
        return Harmony::createCollection(function (Collection $collection) use ($latitude, $longitude) {
            $collection->group(function (Request $request) use ($latitude, $longitude) {
                $request->endpoint('elevation');
                $request->method('GET');

                $request->query(function (Query $query) use ($latitude, $longitude) {
                    $query->field('latitude');
                    $query->field('longitude');
                });
            });
        });
    }

    /**
     * Get marine weather forecast
     * 
     * @param float $latitude
     * @param float $longitude
     * @param array $hourlyVariables
     * @param int $forecastDays
     * @return Collection
     */
    public function getMarineWeather(
        float $latitude, 
        float $longitude, 
        array $hourlyVariables = ['wave_height', 'wave_direction', 'wave_period', 'swell_wave_height', 'swell_wave_direction'],
        int $forecastDays = 7
    ): Collection {
        return Harmony::createCollection(function (Collection $collection) use ($latitude, $longitude, $hourlyVariables, $forecastDays) {
            $collection->group(function (Request $request) use ($latitude, $longitude, $hourlyVariables, $forecastDays) {
                $request->endpoint('marine');
                $request->method('GET');

                $request->query(function (Query $query) use ($latitude, $longitude, $hourlyVariables, $forecastDays) {
                    $query->field('latitude');
                    $query->field('longitude');
                    $query->field('hourly');
                    $query->field('forecast_days');
                    $query->field('timezone');
                });
            });
        });
    }
}
