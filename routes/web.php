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

Route::options('/{all}', function() {
    return 200;
})->where(['all' => '([a-zA-Z0-9-]|/)+']);

Route::get('/', function () {
    return view('welcome');
});
//login 控制器
//发送验证码
Route::any('api/send_sms', "Login@send_sms");
//注册
Route::post('api/admin/register', "Login@register");

//绑定邮箱
Route::post('api/admin/bind_email', "Login@bind_email");
//登录
Route::post('api/admin/login', "Login@login");

//获取验证码
Route::get('api/capt/get', "Capt@get");
//检验验证码
Route::post('api/capt/validate', "Capt@validate_capt");

//前端不需要token验证的接口*************开始******************
//投资布局
Route::get('front/investment_layout/get', "FrontCommon@inv_layout_get");
Route::get('front/investment_layout/get_by_category', "FrontCommon@inv_layout_get_by_category");
Route::get('front/investment_layout/get_by_alp', "FrontCommon@inv_layout_get_by_alp");

//文章
Route::get('front/article/get_publish', "FrontCommon@article_get_publish");
Route::get('front/article/detail', "FrontCommon@article_detail");

//成员
Route::get('front/member/get', "FrontCommon@get_members");
//前端不需要token验证的接口*************结束******************
//文件上传
Route::any('api/upload', "Upload@upload");
//获取微信接口注入权限验证配置
Route::any('api/wx_auth', "Upload@get_wx_auth_config");
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
Route::get('api/project/get', "Project@get");
Route::get('api/project/get_ioc', "Project@get_ioc");

//project_detail
Route::post('api/project_detail/update', "ProjectDetail@update");
Route::any('api/project_detail/get', "ProjectDetail@get");
//project_lab
Route::post('api/project_lab/update', "ProjectLab@update");

//user_center
Route::get('api/user_center/get', "UserCenter@get");
Route::post('api/user_center/update_info', "UserCenter@update_info");
Route::post('api/user_center/update_password', "UserCenter@update_password");

//member
Route::get('api/member/get', "Member@get");
Route::post('api/member/update', "Member@update");
Route::post('api/member/add', "Member@add");
Route::get('api/member/delete', "Member@delete");

//Discussion
Route::get('api/discussion/get', "Discussion@get");
Route::post('api/discussion/update', "Discussion@update");
Route::get('api/discussion/detail', "Discussion@detail");
Route::post('api/discussion/add', "Discussion@add");
Route::get('api/discussion/delete', "Discussion@delete");

//DiscussionComment
Route::post('api/discussion_comment/add', "DiscussionComment@add");
Route::get('api/discussion_comment/delete', "DiscussionComment@delete");