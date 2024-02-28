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
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');
Route::post('register', 'AuthController@register');

//Badminton
Route::get('badminton', 'BadmintonController@index');
Route::get('badminton/{id}', 'BadmintonController@show');
Route::post('badminton', 'BadmintonController@store');
Route::put('badminton/{id}', 'BadmintonController@update');
Route::delete('badminton/{id}', 'BadmintonController@destroy');

//Futsal
Route::get('futsal', 'FutsalController@index');
Route::get('futsal/{id}', 'FutsalController@show');
Route::post('futsal', 'FutsalController@store');
Route::put('futsal/{id}', 'FutsalController@update');
Route::delete('futsal/{id}', 'FutsalController@destroy');

//Soccer
Route::get('soccer', 'SoccerController@index');
Route::get('soccer/{id}', 'SoccerController@show');
Route::post('soccer', 'SoccerController@store');
Route::put('soccer/{id}', 'SoccerController@update');
Route::delete('soccer/{id}', 'SoccerController@destroy');

//System
Route::post('generateJadwalThisMonth', 'SystemController@generateJadwalThisMonth');
Route::post('generateJadwalNextMonth', 'SystemController@generateJadwalNextMonth');
Route::delete('clearCache', 'SystemController@clearCache');
Route::put('maintenance', 'SystemController@setMaintenance');
Route::put('allowBooking', 'SystemController@setAllowBooking');

Route::group(['middleware' => 'auth:api'], function(){
    
});