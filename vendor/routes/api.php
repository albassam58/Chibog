<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\v1\LoginController;
use App\Http\Controllers\API\v1\RegisterController;
use App\Http\Controllers\API\v1\VendorController;
use App\Http\Controllers\API\v1\ItemController;
use App\Http\Controllers\API\v1\StoreController;
use App\Http\Controllers\API\v1\RegionController;
use App\Http\Controllers\API\v1\ProvinceController;
use App\Http\Controllers\API\v1\CityController;
use App\Http\Controllers\API\v1\BarangayController;
use App\Http\Controllers\API\v1\CuisineController;
use App\Http\Controllers\API\v1\FoodTypeController;
use App\Http\Controllers\API\v1\OrderController;
use App\Http\Controllers\API\v1\StatisticController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function() {
	// email verification vendor
	Route::get('/vendor/verify/{id}', [VendorController::class, 'verify'])->name('vendor.verify')->middleware('signed');
	Route::get('/vendor/resend/{id}/{email}', [VendorController::class, 'resend'])->name('vendor.resend');

	Route::apiResource('regions', RegionController::class);
	Route::apiResource('provinces', ProvinceController::class);
	Route::apiResource('cities', CityController::class);
	Route::get('/barangays/{provinceName}/{cityName}', [BarangayController::class, 'show']);
});

Route::group(['prefix' => 'v1/vendor'], function() {
	Route::post('/login', [LoginController::class, 'authenticate']);
	Route::post('/register', [RegisterController::class, 'register']);
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function() {
	Route::group(['prefix' => 'vendor'], function() {
		Route::get('/current', [VendorController::class, 'currentUser']);
		Route::post('/logout', [LoginController::class, 'logout']);
	});
	Route::get('/stores/vendor', [StoreController::class, 'vendor']);
	Route::post('/stores/upload', [StoreController::class, 'upload']);
	Route::apiResource('stores', StoreController::class);
	Route::apiResource('cuisines', CuisineController::class);
	Route::apiResource('food-types', FoodTypeController::class);
	Route::apiResource('items', ItemController::class);
	Route::post('/orders/update/status', [OrderController::class, 'updateStatus']);
	Route::get('/orders/vendor', [OrderController::class, 'vendor']);
	Route::apiResource('orders', OrderController::class);

	Route::get('/statistics/sales', [StatisticController::class, 'sales']);
	Route::get('/statistics/orders', [StatisticController::class, 'orders']);
	Route::get('/statistics/total-sales', [StatisticController::class, 'totalSales']);
	Route::get('/statistics/total-orders', [StatisticController::class, 'totalOrders']);
	Route::get('/statistics/orders-per-status', [StatisticController::class, 'ordersPerStatus']);
});