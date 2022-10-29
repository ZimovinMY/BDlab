<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'MainController@main');
Route::get('/ShowUnitedTable', 'MainController@ShowUnitedTable','ShowUnitedTable');
Route::post('/ChangeData','MainController@ChangeData','ChangeData');
Route::post('/DeleteData','MainController@DeleteData','DeleteData');
Route::post('/AddData','MainController@AddData','AddData');
Route::post('/AddDataF','MainController@AddDataF','AddDataF');
Route::post('/KodCheck','MainController@KodCheck','KodCheck');
