<?php

use App\Http\Controllers\API\v1\AuthenticateController;
use App\Http\Controllers\API\v1\BarangayController;
use App\Http\Controllers\API\v1\ChatController;
use App\Http\Controllers\API\v1\CityController;
use App\Http\Controllers\API\v1\CuisineController;
use App\Http\Controllers\API\v1\FoodTypeController;
use App\Http\Controllers\API\v1\ItemController;
use App\Http\Controllers\API\v1\OrderController;
use App\Http\Controllers\API\v1\OrderNotificationController;
use App\Http\Controllers\API\v1\OtpController;
use App\Http\Controllers\API\v1\ProvinceController;
use App\Http\Controllers\API\v1\RegionController;
use App\Http\Controllers\API\v1\RegisterController;
use App\Http\Controllers\API\v1\StatisticController;
use App\Http\Controllers\API\v1\StoreController;
use App\Http\Controllers\API\v1\StoreReviewController;
use App\Http\Controllers\API\v1\VendorController;
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

Route::group(['prefix' => 'v1'], function() {
	Route::apiResource('regions', RegionController::class);
	Route::apiResource('provinces', ProvinceController::class);
	Route::apiResource('cities', CityController::class);
	Route::get('/barangays/{provinceName}/{cityName}', [BarangayController::class, 'show']);

	// otp
	Route::post('/otp/send', [OtpController::class, 'send'])->middleware('auth:sanctum', 'throttle:5,1');
	Route::post('/otp/verify', [OtpController::class, 'verify'])->middleware('auth:sanctum');
});

Route::group(['prefix' => 'v1/vendor'], function() {
	Route::post('/login', [AuthenticateController::class, 'login']);
	Route::post('/register', [RegisterController::class, 'register']);

	Route::get('/current', [VendorController::class, 'currentUser'])->middleware(['auth:sanctum']);
	Route::put('/update', [VendorController::class, 'update'])->middleware(['auth:sanctum']);
	Route::get('/tokens', [VendorController::class, 'tokens'])->middleware(['auth:sanctum', 'vendor_is_verified']);
	Route::post('/logout', [AuthenticateController::class, 'logout'])->middleware('auth:sanctum');
	Route::post('/logout/device/{id}', [AuthenticateController::class, 'logoutDevice'])->middleware('auth:sanctum');
	Route::post('/logout/all', [AuthenticateController::class, 'logoutAll'])->middleware('auth:sanctum');

	// email verification vendor
	Route::get('/verify/{id}', [VendorController::class, 'verify'])->name('vendor.verify')->middleware('signed');
	Route::post('/resend', [VendorController::class, 'resend'])->name('vendor.resend')->middleware('throttle:5,1');

	// forgot password
	Route::post('/forgot-password', [AuthenticateController::class, 'forgotPassword']);
	Route::post('/reset-password', [AuthenticateController::class, 'resetPassword'])->name('password.update');
});

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum', 'vendor_is_verified']], function() {

	// chat
	Route::get('/chats/customers', [ChatController::class, 'customer']);
	Route::apiResource('chats', ChatController::class);

	// stores
	Route::post('/stores/upload', [StoreController::class, 'upload']);
	Route::apiResource('stores', StoreController::class);

	//store reviews
	Route::apiResource('/store-reviews', StoreReviewController::class);

	Route::apiResource('cuisines', CuisineController::class);
	Route::apiResource('food-types', FoodTypeController::class);
	Route::apiResource('items', ItemController::class);

	// orders
	Route::put('/orders/update/status', [OrderController::class, 'updateStatus']);
	Route::put('/orders/update/paid/{transactionId}', [OrderController::class, 'updatePaid']);
	Route::get('/orders/vendor', [OrderController::class, 'vendor']);
	Route::apiResource('orders', OrderController::class);

	// order notifications
	Route::get('/order-notifications/vendor', [OrderNotificationController::class, 'vendor']);
	Route::get('/order-notifications/vendor/popup', [OrderNotificationController::class, 'popup']);
	Route::get('/order-notifications/count/unread', [OrderNotificationController::class, 'countUnread']);
	Route::put('/order-notifications/read/checked/{ids}', [OrderNotificationController::class, 'readChecked']);
	Route::delete('/order-notifications/delete/checked/{ids}', [OrderNotificationController::class, 'destroyChecked']);
	Route::apiResource('order-notifications', OrderNotificationController::class);

	// statistics
	Route::get('/statistics/sales', [StatisticController::class, 'sales']);
	Route::get('/statistics/orders', [StatisticController::class, 'orders']);
	Route::get('/statistics/total-sales', [StatisticController::class, 'totalSales']);
	Route::get('/statistics/total-orders', [StatisticController::class, 'totalOrders']);
	Route::get('/statistics/orders-per-status', [StatisticController::class, 'ordersPerStatus']);
});