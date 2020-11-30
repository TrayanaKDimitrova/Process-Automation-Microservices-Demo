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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'db'], function () {
    Route::post('/migrate', '\App\Http\Controllers\DatabaseMigrationsController@migrate');
});

Route::group(['prefix' => 'cars'], function () {
    Route::get('/', '\App\Http\Controllers\CarsController@getAll');
});

Route::group(['prefix' => 'car'], function () {
    Route::post('/', '\App\Http\Controllers\CarController@create');
});
