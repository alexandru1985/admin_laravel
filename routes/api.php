<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources([
    'user' => 'API\UserController',
]);

Route::get('profile','API\UserController@profile')->name('profile');
Route::get('findUser/{param}','API\UserController@findUser')->name('findUser');
Route::put('profile','API\UserController@updateProfile')->name('updateProfile');

// Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('user')->group(function () {
    Route::post('login','API\LoginController@login');
});