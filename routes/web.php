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
//文件上传
Route::any('api/upload', "Upload@upload");
//token检测
Route::any('api/token/get', "Token@get");
//relationship
Route::any('api/relationship/add', "Relationship@add")->name("api/relationship/add");
Route::any('api/relationship/update', "Relationship@update")->name("api/relationship/update");
Route::any('api/relationship/delete', "Relationship@delete")->name("api/relationship/delete");
Route::any('api/relationship/group', "Relationship@group")->name("api/relationship/group");
Route::any('api/relationship/detail', "Relationship@detail")->name("api/relationship/detail");
Route::any('api/relationship/search', "Relationship@search");

//category
Route::any('api/category/get', "Category@get");
//industry
Route::any('api/industry/get', "Industry@get");
//job_title
Route::any('api/job_title/get', "JobTitle@get");
//job_position
Route::any('api/job_position/get', "JobPosition@get");

//article
Route::any('api/article/add', "Article@add");
Route::any('api/article/update', "Article@update");
Route::any('api/article/pub_cancel', "Article@pub_cancel");
Route::any('api/article/delete', "Article@delete");
Route::any('api/article/get', "Article@get");
Route::any('api/article/get_publish', "Article@get_publish");
Route::any('api/article/detail', "Article@detail");

//investmentLayout
Route::any('api/investment_layout/add', "InvestmentLayout@add");
Route::any('api/investment_layout/update', "InvestmentLayout@update");
Route::any('api/investment_layout/delete', "InvestmentLayout@delete");
Route::any('api/investment_layout/get', "InvestmentLayout@get");
Route::any('api/investment_layout/get_by_category', "InvestmentLayout@get_by_category");
Route::any('api/investment_layout/get_by_alp', "InvestmentLayout@get_by_alp");

//lab
Route::post('api/lab/add', "Lab@add");
Route::post('api/lab/update', "Lab@update");
Route::any('api/lab/delete', "Lab@delete");
Route::any('api/lab/get', "Lab@get");
Route::any('api/lab/get_by_industry', "Lab@get_by_industry");
Route::any('api/lab/get_by_alp', "Lab@get_by_alp");

//project
Route::post('api/project/add', "Project@add");
Route::post('api/project/update', "Project@update");
Route::any('api/project/delete', "Project@delete");
Route::any('api/project/get', "Project@get");

//project_detail
Route::post('api/project_detail/update', "ProjectDetail@update");
Route::any('api/project_detail/get', "ProjectDetail@get");

//project_lab
Route::post('api/project_lab/update', "ProjectLab@update");