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
Route::get('generateNoBooking', 'SystemController@generateNoBooking');
Route::post('generateJadwalThisMonth', 'SystemController@generateJadwalThisMonth');
Route::post('generateJadwalNextMonth', 'SystemController@generateJadwalNextMonth');
Route::delete('clearCache', 'SystemController@clearCache');
Route::put('maintenance', 'SystemController@setMaintenance');
Route::put('allowBooking', 'SystemController@setAllowBooking');

//Jadwal
Route::get('jadwalBadminton/{tanggal}', 'BookingBadmintonController@index');
Route::get('jadwalFutsal/{tanggal}', 'BookingFutsalController@index');
Route::get('jadwalSoccer/{tanggal}', 'BookingSoccerController@index');

Route::group(['middleware' => 'auth:api'], function(){    
    //User
    Route::get('user', 'UserController@index');
    Route::get('user/{id}', 'UserController@show');
    Route::put('user/{id}', 'UserController@update');
    Route::delete('user/{id}', 'UserController@destroy');

    //Booking Badminton
    Route::get('showBookingBadminton/{id}', 'BookingBadmintonController@showBooking');
    Route::put('addBookingBadminton/{id}', 'BookingBadmintonController@addBooking');
    Route::put('cancelBookingBadminton/{id}', 'BookingBadmintonController@cancelBooking');
    
    //Booking Futsal
    Route::get('showBookingFutsal/{id}', 'BookingFutsalController@showBooking');
    Route::put('addBookingFutsal/{id}', 'BookingFutsalController@addBooking');
    Route::put('cancelBookingFutsal/{id}', 'BookingFutsalController@cancelBooking');
    
    //Booking Soccer
    Route::get('showBookingSoccer/{id}', 'BookingSoccerController@showBooking');
    Route::put('addBookingSoccer/{id}', 'BookingSoccerController@addBooking');
    Route::put('cancelBookingSoccer/{id}', 'BookingSoccerController@cancelBooking');

    //Transaksi
    Route::get('transaksi', 'TransaksiController@index');
    Route::get('transaksi/user/{id}', 'TransaksiController@indexByUser');
    Route::get('transaksi/{id}', 'TransaksiController@show');
    Route::post('transaksi', 'TransaksiController@store');
});
