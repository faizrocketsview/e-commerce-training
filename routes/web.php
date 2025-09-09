<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Resource;

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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Specific route for users module to use User Resource
    Route::get('/ecommerce/managements/users', App\Http\Livewire\User\Resource::class);
    
    // Specific route for orders module to use Order Resource
    Route::get('/ecommerce/managements/orders', App\Http\Livewire\Order\Resource::class);
    
    // General routes for other modules
    Route::get('/{moduleSection}/{moduleGroup}/{module}', Resource::class);
    Route::get('/{moduleSection}/{moduleGroup}/{module}/import', App\Http\Livewire\ImportResource::class);
    Route::get('/{moduleSection}/{moduleGroup}/{module}/import-errors', App\Http\Livewire\ImportErrorResource::class);
});
