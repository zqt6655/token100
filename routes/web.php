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

Route::get('/', function () {
    return view('welcome');
});
Route::any('api/get_industry/{id}', "Api@get_industry")->name("api/get_industry");
Route::any('api/upload', "Upload@upload_img")->name("api/upload");
Route::any('api/get_industry_list', "Industry@get_industry_list")->name("api/get_industry_list");
