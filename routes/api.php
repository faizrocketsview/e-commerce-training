<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Formation\Http\Controllers\ApiController;
use App\Http\Controllers\MockDataController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\HarmonyTestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});

// ========================================
// CRUD API Routes (No CSRF Protection)
// ========================================

// Users CRUD
Route::get('/users', [MockDataController::class, 'getUsers']);
Route::get('/users/{id}', [MockDataController::class, 'getUser']);
Route::post('/users', [MockDataController::class, 'createUser']);
Route::put('/users/{id}', [MockDataController::class, 'updateUser']);
Route::delete('/users/{id}', [MockDataController::class, 'deleteUser']);

// Products CRUD
Route::get('/products', [MockDataController::class, 'getProducts']);
Route::get('/products/{id}', [MockDataController::class, 'getProduct']);
Route::post('/products', [MockDataController::class, 'createProduct']);
Route::put('/products/{id}', [MockDataController::class, 'updateProduct']);
Route::delete('/products/{id}', [MockDataController::class, 'deleteProduct']);

// Orders CRUD
Route::get('/orders', [MockDataController::class, 'getOrders']);
Route::get('/orders/{id}', [MockDataController::class, 'getOrder']);
Route::post('/orders', [MockDataController::class, 'createOrder']);
Route::put('/orders/{id}', [MockDataController::class, 'updateOrder']);
Route::delete('/orders/{id}', [MockDataController::class, 'deleteOrder']);

// Statistics
Route::get('/stats', [MockDataController::class, 'getStats']);
Route::post('/reset', [MockDataController::class, 'resetData']);

// Weather API
Route::get('/weather/forecast', [WeatherController::class, 'getForecast']);
Route::get('/weather/current', [WeatherController::class, 'getCurrentWeather']);
Route::get('/weather/test-all', [WeatherController::class, 'testAllEndpoints']);

// Harmony Test API
Route::get('/harmony-test/workflow', [HarmonyTestController::class, 'testWorkflow']);

// Test route to verify API middleware
Route::get('/test', function () {
    return response()->json(['message' => 'API route working!', 'middleware' => 'api']);
});

// Test POST route
Route::post('/test-post', function (Request $request) {
    return response()->json([
        'message' => 'POST API route working!', 
        'data' => $request->all(),
        'middleware' => 'api'
    ]);
});

// ========================================
// Project Routes
// ========================================


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('/{moduleSection}/{moduleGroup}/{modules}', ApiController::class)->only(['index', 'store']);
    Route::get('/{moduleSection}/{moduleGroup}/{modules}/{id}', [ApiController::class, 'show']);
    Route::put('/{moduleSection}/{moduleGroup}/{modules}/{id}', [ApiController::class, 'update']);
    Route::delete('/{moduleSection}/{moduleGroup}/{modules}/{id}', [ApiController::class, 'destroy']);
});
