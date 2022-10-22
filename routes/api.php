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

Route::post('calculate/single', 'CalculateController@single');
Route::post('calculate/multi', 'CalculateController@multi');
Route::post('calculate/multi_queue', 'CalculateController@multi_queue');
Route::post('calculate/multi_child', 'CalculateController@multi_child');
Route::get('calculate/progress/{token}', 'CalculateController@progress');
Route::post('linear/comparison/calculate', 'Linear\ComparisonController@calculate');
Route::post('non_linear/comparison/calculate', 'NonLinear\ComparisonController@calculate');
Route::post('participants_simulate/calculate', 'ParticipantsSimulateController@calculate');
Route::post('lottery/calculate', 'LotteryController@calculate');
Route::post('preset/save', 'PresetController@save');
Route::post('preset/delete', 'PresetController@delete');
Route::get('preset/find/{id}', 'PresetController@find');
