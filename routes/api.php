<?php

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
Route::group(['prefix' => 'v1'], function () {
    Route::post('userregister', [App\Http\Controllers\Api\LoginController::class, 'register']);
    Route::post('gardner/register', [App\Http\Controllers\Api\G_LoginController::class, 'register']);
    Route::post('login', [App\Http\Controllers\Api\LoginController::class, 'login'])->name('user.login');
    Route::post('gardner/login', [App\Http\Controllers\Api\G_LoginController::class, 'login'])->name('gardner.login');
    Route::post('create/otp/{again?}', [App\Http\Controllers\Api\LoginController::class, 'createOtp'])->name('user.create.token');
    Route::post('verify/otp', [App\Http\Controllers\Api\LoginController::class, 'verifyOtp'])->name('user.verify.token');
    Route::get('google/redirect', [App\Http\Controllers\Api\LoginController::class, 'googleRedirect'])->name('user.google.redirect');
    Route::get('googleAuthenticate', [App\Http\Controllers\Api\LoginController::class, 'handleGoogleCallback'])->name('user.login.google');


    Route::get('search', [App\Http\Controllers\Api\SearchController::class, 'index']);
    Route::post('search/result', [App\Http\Controllers\Api\SearchController::class, 'searchResult']);
    Route::match(array('GET', 'POST'), 'properties/{slug}', [App\Http\Controllers\Api\PropertyController::class, 'single'])->name('property.single');
});
Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {
    Route::get('accessUser', [App\Http\Controllers\Api\LoginController::class, 'index']);
    Route::get('profile/{id}', [App\Http\Controllers\Api\CustomerController::class, 'profileView']);
    Route::POST('updateProfile', [App\Http\Controllers\Api\CustomerController::class, 'updateProfile']);
    Route::POST('createBooking', [App\Http\Controllers\Api\CustomerController::class, 'createBooking']);
    Route::GET('MyBooking', [App\Http\Controllers\Api\CustomerController::class, 'MyBooking']);
    Route::GET('searchGardner/{id}', [App\Http\Controllers\Api\CustomerController::class, 'searchGardner']);
    Route::post('AwardTo', [App\Http\Controllers\Api\CustomerController::class, 'AwardTo']);
    Route::GET('logout', [App\Http\Controllers\Api\LoginController::class, 'logout']);

});
// Gardner route
    Route::group(['prefix' => 'v1', 'middleware' => ['auth:gardner']], function () {
        Route::get('accessGardner', [App\Http\Controllers\Api\GardnerController::class, 'index']);
        Route::GET('GardnerBooking', [App\Http\Controllers\Api\GardnerController::class, 'MyBooking']);
        Route::GET('logout/gardner', [App\Http\Controllers\Api\GardnerController::class, 'logout']);

    });


