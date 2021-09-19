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



Route::group(['middleware' => ['cors', 'json.response']], function () {
    Route::get( '/all-data', 'App\Http\Controllers\SportsFeedController@getAllData')->name('get-all-data');
    Route::get( '/meeting-data', 'App\Http\Controllers\SportsFeedController@getMeetingData')->name('get-meeting-data');
    Route::get( '/race-data', 'App\Http\Controllers\SportsFeedController@getRaceData')->name('get-race-data');
    Route::get( '/all-horses-data', 'App\Http\Controllers\SportsFeedController@getAllHorsesData')->name('get-all-horses-data');
    Route::get( '/horse-data', 'App\Http\Controllers\SportsFeedController@getHorseData')->name('get-horse-data');
});
