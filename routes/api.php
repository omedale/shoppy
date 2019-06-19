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

Route::group(['middleware' => ['api','cors']], function () {
    Route::group(['middleware' => 'jwt-auth'], function () {
        Route::put('/customers/address', 'Api\CustomerController@updateAddress');
    });
    Route::post('/customer', 'Api\CustomerController@register');
    Route::post('/customer/login', 'Api\CustomerController@login');
});
