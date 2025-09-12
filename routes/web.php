<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Resource;
use App\Http\Livewire\Catalog\Resource as CatalogResource;
use App\Http\Controllers\HarmonyTestController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\MockDataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Harmony Package Test Routes
Route::prefix('harmony-test')->group(function () {
    Route::get('/articles', [HarmonyTestController::class, 'testGetArticles']);
    Route::post('/articles', [HarmonyTestController::class, 'testCreateArticle']);
    Route::get('/articles/{articleId}', [HarmonyTestController::class, 'testGetArticle']);
    Route::put('/articles/{articleId}', [HarmonyTestController::class, 'testUpdateArticle']);
    Route::delete('/articles/{articleId}', [HarmonyTestController::class, 'testDeleteArticle']);
    Route::get('/articles/search', [HarmonyTestController::class, 'testSearchArticles']);
    Route::get('/workflow', [HarmonyTestController::class, 'testCompleteWorkflow']);
    Route::get('/errors', [HarmonyTestController::class, 'testErrorHandling']);
});

// Open-Meteo Weather API Routes
Route::prefix('weather')->group(function () {
    Route::get('/forecast', [WeatherController::class, 'getForecast']);
    Route::get('/current', [WeatherController::class, 'getCurrentWeather']);
    Route::get('/historical', [WeatherController::class, 'getHistoricalWeather']);
    Route::get('/air-quality', [WeatherController::class, 'getAirQuality']);
    Route::get('/geocoding', [WeatherController::class, 'getGeocoding']);
    Route::get('/elevation', [WeatherController::class, 'getElevation']);
    Route::get('/marine', [WeatherController::class, 'getMarineWeather']);
    Route::get('/test-all', [WeatherController::class, 'testAllEndpoints']);
});

// Mock Data CRUD API Routes - Moved to api.php to avoid CSRF issues

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    // Catalog (custom frontend) using Formation data table under ecommerce/managements/products
    Route::get('/catalog/ecommerce/managements/products', CatalogResource::class);

    // Specific route for products module to use Product Resource
    Route::get('/ecommerce/managements/products', App\Http\Livewire\Product\Resource::class);
    
    // Specific route for users module to use User Resource
    Route::get('/ecommerce/managements/users', App\Http\Livewire\User\Resource::class);
    
    // Specific route for orders module to use Order Resource
    Route::get('/ecommerce/managements/orders', App\Http\Livewire\Order\Resource::class);
    
    // General routes for other modules
    Route::get('/{moduleSection}/{moduleGroup}/{module}', Resource::class);
    Route::get('/{moduleSection}/{moduleGroup}/{module}/import', App\Http\Livewire\ImportResource::class);
    Route::get('/{moduleSection}/{moduleGroup}/{module}/import-errors', App\Http\Livewire\ImportErrorResource::class);
});
