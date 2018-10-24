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

//relationship
Route::any('api/relationship/add', "Relationship@add")->name("api/relationship/add");
Route::any('api/relationship/update', "Relationship@update")->name("api/relationship/update");
Route::any('api/relationship/delete', "Relationship@delete")->name("api/relationship/delete");
Route::any('api/relationship/group', "Relationship@group")->name("api/relationship/group");
Route::any('api/relationship/detail', "Relationship@detail")->name("api/relationship/detail");

//category
Route::any('api/category/get', "Category@get");

//industry
Route::any('api/industry/get', "Industry@get");
