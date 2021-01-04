<?php

use App\Http\Controllers\API\v1\BarangayController;
use App\Http\Controllers\API\v1\ChatController;
use App\Http\Controllers\API\v1\CityController;
use App\Http\Controllers\API\v1\CuisineController;
use App\Http\Controllers\API\v1\FoodTypeController;
use App\Http\Controllers\API\v1\ItemController;
use App\Http\Controllers\API\v1\LoginController;
use App\Http\Controllers\API\v1\OrderController;
use App\Http\Controllers\API\v1\ProvinceController;
use App\Http\Controllers\API\v1\RegionController;
use App\Http\Controllers\API\v1\RegisterController;
use App\Http\Controllers\API\v1\StoreController;
use App\Http\Controllers\API\v1\StoreReviewController;
use App\Http\Controllers\API\v1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'v1/user'], function() {
	Route::post('/login', [LoginController::class, 'login']);
	Route::post('/register', [RegisterController::class, 'register']);
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function() {
	Route::group(['prefix' => 'user'], function() {
		Route::get('/current', [UserController::class, 'currentUser']);
		Route::post('/logout', [LoginController::class, 'logout']);
	});

	Route::get('/orders/user', [OrderController::class, 'user']);

	// chat
	Route::get('/chats/customers', [ChatController::class, 'customer']);
	Route::apiResource('chats', ChatController::class);

	// Orders
	// Route::get('/orders/check', [OrderController::class, 'check']);
	Route::get('/orders/search/{query}', [OrderController::class, 'search']);
	// Route::put('/orders/update/status', [OrderController::class, 'updateStatus']);
	Route::post('/orders/store', [OrderController::class, 'storeByStore']);
	// Route::put('/orders/store', [OrderController::class, 'updateByStore']);
	// Route::delete('/orders/store', [OrderController::class, 'destroyByStore']);
	Route::apiResource('orders', OrderController::class);

	Route::apiResource('store-reviews', StoreReviewController::class);
});

Route::group(['prefix' => 'v1'], function() {
	Route::get('/vendor-url', function() {
		return config('app.vendor_url');
	});

	// Stores
	Route::get('/stores/city', [StoreController::class, 'city']);
	Route::get('/stores/search/{query}', [StoreController::class, 'search']);
	Route::apiResource('stores', StoreController::class);

	Route::apiResource('cuisines', CuisineController::class);
	Route::apiResource('food-types', FoodTypeController::class);
	Route::apiResource('items', ItemController::class);

	// Address
	Route::apiResource('regions', RegionController::class);
	Route::apiResource('provinces', ProvinceController::class);
	Route::apiResource('cities', CityController::class);
	Route::get('/barangays/{provinceName}/{cityName}', [BarangayController::class, 'show']);
});