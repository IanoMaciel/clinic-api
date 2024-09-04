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

//Route::middleware('jwt.auth')->group(function () {
//    Route::apiResource('customer', 'CustomerController');
//    Route::apiResource('address', 'AddressController');
//    Route::apiResource('product', 'ProductController');
//});

Route::prefix('v1')->middleware('jwt.auth')->group(function () {

    Route::get('me', 'AuthController@me');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('logout', 'AuthController@logout');

    Route::apiResource('user', 'UserController');

    Route::apiResource('local', 'LocalController');
    Route::apiResource('service', 'ServiceController');

    Route::apiResource('customer', 'CustomerController');
    Route::apiResource('address', 'AddressController');

    Route::apiResource('product', 'ProductController');
    Route::get('findAll', 'ProductController@findAll');

    Route::apiResource('inventory', 'InventoryController');
    Route::apiResource('category', 'CategoryController');

    Route::apiResource('history', 'HistoryController');
    Route::get('history-attachments/{id}', 'HistoryController@findById');

    Route::apiResource('schedule', 'ScheduleController');

    Route::apiResource('payment-method', 'PaymentController');
    Route::apiResource('agreement', 'AgreementController');
    Route::apiResource('order', 'OrderController');

    // dashboard
    Route::get('gains', 'DashController@gains');
    Route::get('total-customers', 'DashController@totalCustomers');
    Route::get('total-services', 'DashController@totalServices');
    Route::get('all-schedulings', 'DashController@allSchedulings');
});

Route::post('login', 'AuthController@login');
