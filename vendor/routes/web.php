<?php

use App\Http\Controllers\Auth\FacebookController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/artisan-migrate', function() {
	try {
        \Artisan::call('migrate', [
            '--force' => true
        ]);
        return "Success";
        // \Artisan::call('migrate --path=database/migrations/jobs');
        // \Artisan::call('migrate --path=database/migrations/webhooks');
        // \Artisan::call('migrate --path=database/migrations/purls');
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});

Route::get('/artisan-seed', function() {
    try {
        // \Artisan::call('composer dump-autoload');
        return \Artisan::call('db:seed', [
            '--force' => true
        ]);

        return 'Success';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});

Route::group(['prefix' => 'auth'], function() {
	Route::get('user', function() {
		return Auth::user();
	});

	Route::get('facebook', [FacebookController::class, 'redirectToFacebook']);
	Route::get('facebook/callback', [FacebookController::class, 'handleFacebookCallback']);

	Route::get('logout', function() {
		Auth::logout();
		return redirect('/');
	});
});

Route::get('/{vue}', function() {
	return view('spa');
})->where('vue', '.*');
