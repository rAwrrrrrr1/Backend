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
Route::get('users', 'AuthController@listUsers');

//Badminton
Route::get('badminton', 'BadmintonController@index');
Route::get('badminton/{id}', 'BadmintonController@show');
Route::post('badminton', 'BadmintonController@store');
Route::post('badminton/{id}', 'BadmintonController@update');
Route::delete('badminton/{id}', 'BadmintonController@destroy');

//Futsal
Route::get('futsal', 'FutsalController@index');
Route::get('futsal/{id}', 'FutsalController@show');
Route::post('futsal', 'FutsalController@store');
Route::post('futsal/{id}', 'FutsalController@update');
Route::delete('futsal/{id}', 'FutsalController@destroy');

//Soccer
Route::get('soccer', 'SoccerController@index');
Route::get('soccer/{id}', 'SoccerController@show');
Route::post('soccer', 'SoccerController@store');
Route::post('soccer/{id}', 'SoccerController@update');
Route::delete('soccer/{id}', 'SoccerController@destroy');

//System
Route::get('generateNoBooking', 'SystemController@generateNoBooking');
Route::post('generateJadwalThisMonth', 'SystemController@generateJadwalThisMonth');
Route::post('generateJadwalNextMonth', 'SystemController@generateJadwalNextMonth');
Route::delete('clearCache', 'SystemController@clearCache');
Route::get('maintenance/getStatus', 'SystemController@getStatusMaintenance');
Route::put('maintenance', 'SystemController@setMaintenance');
Route::get('allowBooking/getStatus', 'SystemController@getStatusAllowBooking');
Route::put('allowBooking', 'SystemController@setAllowBooking');

//Jadwal
Route::get('jadwalBadminton/{tanggal}', 'BookingBadmintonController@index');
Route::get('jadwalFutsal/{tanggal}', 'BookingFutsalController@index');
Route::get('jadwalSoccer/{tanggal}', 'BookingSoccerController@index');

Route::group(['middleware' => 'auth:api'], function(){    
    //User
    Route::get('user', 'UserController@index');
    Route::get('user/{id}', 'UserController@show');
    Route::put('userPassword/{id}', 'UserController@update');
    Route::put('userUpdate/{id}', 'UserController@updateUser');
    Route::delete('user/{id}', 'UserController@destroy');

    //Booking Badminton
    Route::get('showBookingBadminton/{id}', 'BookingBadmintonController@showBooking');
    Route::get('detailBookingBadminton/{id}', 'BookingBadmintonController@detailBooking');
    Route::put('addBookingBadminton/{id}', 'BookingBadmintonController@addBooking');
    Route::put('cancelBookingBadminton/{id}', 'BookingBadmintonController@cancelBooking');
    
    //Booking Futsal
    Route::get('showBookingFutsal/{id}', 'BookingFutsalController@showBooking');
    Route::get('detailBookingFutsal/{id}', 'BookingFutsalController@detailBooking');
    Route::put('addBookingFutsal/{id}', 'BookingFutsalController@addBooking');
    Route::put('cancelBookingFutsal/{id}', 'BookingFutsalController@cancelBooking');
    
    //Booking Soccer
    Route::get('showBookingSoccer/{id}', 'BookingSoccerController@showBooking');
    Route::get('detailBookingSoccer/{id}', 'BookingSoccerController@detailBooking');
    Route::put('addBookingSoccer/{id}', 'BookingSoccerController@addBooking');
    Route::put('cancelBookingSoccer/{id}', 'BookingSoccerController@cancelBooking');

    //Transaksi
    Route::get('transaksi', 'TransaksiController@index');
    Route::get('transaksi/user/{id}', 'TransaksiController@indexByUser');
    Route::get('transaksi/detail/{type}/{no_booking}', 'TransaksiController@show');
    Route::post('transaksi/payment/{id}', 'TransaksiController@payment');
    Route::put('transaksi/confirmation/{id}', 'TransaksiController@confirmation');
    
});
