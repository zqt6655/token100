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

//Route::options('/{all}', function(\Illuminate\Http\Request $request) {
//    $origin = $request->header('ORIGIN', '*');
//    header("Access-Control-Allow-Origin: $origin");
//    header("Access-Control-Allow-Credentials: true");
//    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
//    header('Access-Control-Allow-Headers: Origin, Access-Control-Request-Headers, SERVER_NAME, Access-Control-Allow-Headers, cache-control,token, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie');
//})->where(['all' => '([a-zA-Z0-9-]|/)+']);

Route::get('/', function () {
    return view('welcome');
});
//前端不需要token验证的接口*************开始******************
//投资布局
Route::prefix('front/investment_layout')->group(function() {
    Route::get('get', "FrontCommon@inv_layout_get");
    Route::get('get_by_category', "FrontCommon@inv_layout_get_by_category");
    Route::get('get_by_alp', "FrontCommon@inv_layout_get_by_alp");
});
Route::prefix('front/')->group(function() {
    //文章
    Route::get('article/get_publish', "FrontCommon@article_get_publish");
    Route::get('article/detail', "FrontCommon@article_detail");
    //成员
    Route::get('member/get', "FrontCommon@get_members");
    //添加项目
    Route::post('project/add', "FrontCommon@add_project");
    //修改项目
    Route::post('project_detail/update', "FrontCommon@update_project");
});
//前端不需要token验证的接口*************结束******************


//后端api接口*************开始******************
Route::prefix('api/')->group(function() {
//发送验证码
    Route::any('send_sms', "Login@send_sms");
//注册
    Route::post('admin/register', "Login@register");
//绑定邮箱
    Route::post('admin/bind_email', "Login@bind_email");
//登录
    Route::post('admin/login', "Login@login");
//获取验证码
    Route::get('capt/get', "Capt@get");
//检验验证码
    Route::post('capt/validate', "Capt@validate_capt");
//文件上传
    Route::any('upload', "Upload@upload");
    //文件预览
    Route::any('preshow', "Upload@preshow");
    //文件下载
    Route::any('download', "Upload@download");
//获取微信接口注入权限验证配置
    Route::any('wx_auth', "Upload@get_wx_auth_config");
    //category
    Route::any('category/get', "Category@get");
//industry
    Route::any('industry/get', "Industry@get");
//job_title
    Route::any('job_title/get', "JobTitle@get");
//job_position
    Route::any('job_position/get', "JobPosition@get");
//project_lab
    Route::post('project_lab/update', "ProjectLab@update");
});

//relationship
Route::prefix('api/relationship/')->group(function() {
    Route::any('add', "Relationship@add");
    Route::any('update', "Relationship@update");
    Route::any('delete', "Relationship@delete");
    Route::any('group', "Relationship@group");
    Route::any('detail', "Relationship@detail");
    Route::any('search', "Relationship@search");
});

//article
Route::prefix('api/article/')->group(function() {
    Route::any('add', "Article@add");
    Route::any('update', "Article@update");
    Route::any('pub_cancel', "Article@pub_cancel");
    Route::any('delete', "Article@delete");
    Route::any('get', "Article@get");
    Route::any('get_publish', "Article@get_publish");
    Route::any('detail', "Article@detail");
});
//investmentLayout
Route::prefix('api/investment_layout/')->group(function() {
    Route::any('add', "InvestmentLayout@add");
    Route::any('update', "InvestmentLayout@update");
    Route::any('delete', "InvestmentLayout@delete");
    Route::any('get', "InvestmentLayout@get");
    Route::any('get_by_category', "InvestmentLayout@get_by_category");
    Route::any('get_by_alp', "InvestmentLayout@get_by_alp");
});
//lab
Route::prefix('api/lab/')->group(function() {
    Route::post('add', "Lab@add");
    Route::post('update', "Lab@update");
    Route::any('delete', "Lab@delete");
    Route::any('get', "Lab@get");
    Route::any('get_by_industry', "Lab@get_by_industry");
    Route::any('get_by_alp', "Lab@get_by_alp");
});

//project
Route::prefix('api/project/')->group(function() {
    Route::post('add', "Project@add");
    Route::post('update', "Project@update");
    Route::any('delete', "Project@delete");
    Route::get('get', "Project@get");
    Route::get('get_front', "Project@get_front");
    Route::get('get_back', "Project@get_back");
    Route::get('get_system', "Project@get_system");
    Route::get('get_grade', "Project@get_grade");
    Route::get('search', "Project@search");
    Route::get('get_ico', "Project@get_ico");
    Route::get('search_ico', "Project@search_ico");
    Route::get('get_invest', "Project@get_invest");
    Route::get('invest_on', "Project@invest_on");
    Route::get('invest_off', "Project@invest_off");
    Route::get('get_wait', "Project@get_wait");
    Route::get('get_continue', "Project@get_continue");
    Route::get('get_hatch', "Project@get_hatch");
    Route::get('get_reject', "Project@get_reject");
});

//project_detail
Route::prefix('api/project_detail/')->group(function() {
    Route::post('update', "ProjectDetail@update");
    Route::any('get', "ProjectDetail@get")->name('project_detail/get');
    Route::any('get_price', "ProjectDetail@get_price");
});
//project_team
Route::prefix('api/project_team/')->group(function() {
    Route::post('update', "ProjectTeam@update");
    Route::post('add', "ProjectTeam@add");
    Route::any('delete', "ProjectTeam@delete");
    Route::get('get', "ProjectTeam@get");
});

//user_center
Route::prefix('api/user_center/')->group(function() {
    Route::get('get', "UserCenter@get");
    Route::post('update_info', "UserCenter@update_info");
    Route::post('update_password', "UserCenter@update_password");
});
//member
Route::prefix('api/member/')->group(function() {
    Route::get('get', "Member@get");
    Route::post('update', "Member@update");
    Route::post('add', "Member@add");
    Route::get('delete', "Member@delete");
});
//Discussion
Route::prefix('api/discussion/')->group(function() {
    Route::get('get', "Discussion@get");
    Route::post('update', "Discussion@update");
    Route::get('detail', "Discussion@detail");
    Route::post('add', "Discussion@add");
    Route::get('delete', "Discussion@delete");

});
//DiscussionComment
Route::prefix('api/discussion_comment/')->group(function() {
    Route::post('add', "DiscussionComment@add");
    Route::get('delete', "DiscussionComment@delete");
});
//清洗入库爬取的项目data
Route::prefix('api/data/')->group(function() {
    Route::any('add', "Data@add");
    Route::any('rating', "Data@add_ratingToken");
    Route::any('exchange', "Data@add_exchange");
});
//基金交易详情,回币、购买、卖出
Route::prefix('api/found_project/')->group(function() {
    Route::get('get', "FoundProject@get_by_project_id");
    Route::post('back', "FoundProject@add_back");
    Route::post('buy', "FoundProject@add_buy");
    Route::post('sell', "FoundProject@add_sell");
    Route::any('delete', "FoundProject@delete");
    Route::get('back_info', "FoundProject@get_back_info");
    Route::get('buy_info', "FoundProject@get_buy_info");
    Route::get('sell_info', "FoundProject@get_sell_info");
    Route::post('update_back', "FoundProject@update_back");
    Route::post('update_buy', "FoundProject@update_buy");
    Route::post('update_sell', "FoundProject@update_sell");
});

//项目尽调
Route::prefix('api/project_survey/')->group(function() {
    Route::any('add', "ProjectSurvey@add");
    Route::any('get', "ProjectSurvey@get");
    Route::any('update', "ProjectSurvey@update");
    Route::any('delete', "ProjectSurvey@delete");
});