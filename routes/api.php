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

        Route::get('/products', 'Api\ProductController@index');
        Route::get('/products/search', 'Api\ProductController@search');

        Route::post('/shoppingcart/add', 'Api\ShoppingCartController@add');
        Route::put('/shoppingcart/update/{item_id}', 'Api\ShoppingCartController@update');
        Route::delete('/shoppingcart/removeProduct/{item_id}', 'Api\ShoppingCartController@removeProduct');
    });
    Route::post('/customer', 'Api\CustomerController@register');
    Route::post('/customer/login', 'Api\CustomerController@login');
});
