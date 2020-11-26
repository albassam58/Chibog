<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\SendMailController;
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

Route::group(['prefix' => 'auth'], function() {
	Route::get('user', function() {
		return Auth::user();
	});

    Route::get('logout', function() {
        Auth::logout();
        return redirect('/');
    });

    Route::get('{provider}/callback', [SocialiteController::class, 'handleSocialiteCallback']);
	Route::get('{provider}', [SocialiteController::class, 'socialiteRedirect']);
});

Route::get('/{vue}', function() {
	return view('spa');
})->where('vue', '.*');
