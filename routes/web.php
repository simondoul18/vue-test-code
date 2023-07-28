<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\CategoriesController;
use Illuminate\Support\Facades\Route;
// use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/', [VehicleController::class, 'index'])->name('dashboard');
    Route::get('/categories', [CategoriesController::class, 'dt_categories'])->name('categories');
    Route::get('/vehicles', [VehicleController::class, 'dt_vehicles'])->name('vehicles');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/category', [CategoriesController::class, 'store'])->name('category.store');
    Route::delete('/category', [CategoriesController::class, 'destroy'])->name('category.destroy');

    Route::get('/get-vehicles', [VehicleController::class, 'getVehicles'])->name('vehicle.get');
    Route::post('/vehicle', [VehicleController::class, 'store'])->name('vehicle.store');
    Route::delete('/vehicle', [VehicleController::class, 'destroy'])->name('vehicle.destroy');
});

require __DIR__.'/auth.php';
