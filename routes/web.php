<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'IndexController@index');
Route::get('/coin-tossing', 'CoinTossingController@index');
Route::get('/lottery', 'LotteryController@index');
Route::get('/document', 'DocumentController@index');
Route::get('non-linear/', 'NonLinearController@index');
