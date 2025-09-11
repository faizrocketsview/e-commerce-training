<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Resource;
use App\Http\Livewire\Catalog\Resource as CatalogResource;

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
