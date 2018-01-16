<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the 'api' middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    echo 'sdfsdf';
});

// 后台需登录信息
// Route::group(['namespace' => 'Admin', 'middleware' => 'auth:api'], function () {
Route::group(['namespace' => 'Admin'], function () {
  // 小程序
  Route::post('industry/add', 'IndustryController@add'); // 添加行业
  Route::post('industry/update/{id}', 'IndustryController@update'); // 修改行业

  // 客户
  Route::post('custom/add', 'CustomController@add'); // 修改案例
  Route::get('custom/list', 'CustomController@list'); // 修改案例

  // 关键词
  Route::post('keyword/add', 'KeywordController@add');
  Route::get('keyword/first_rank_update', 'KeywordController@first_rank_update')->name('first_rank_update');
  Route::get('keyword/hash_update', 'KeywordController@hash_update')->name('hash_update'); // 更新关键词hash值
  Route::post('keyword/rank_update', 'KeywordController@rank_update')->name('rank_update'); // 更新排名
  Route::get('keyword/query', 'KeywordController@query');
  Route::get('/keyword/export/{type}/{id}', "KeywordController@export"); // 导出excel

  // 自动查词
  Route::get('rankquery/add_task', 'RankqueryController@add_task')->name('rankquery_add_task'); // 添加任务
  Route::get('rankquery/list', 'RankqueryController@list')->name('rankquery_list'); // 任务列表
  
});

// Route::group(['namespace' => 'Admin\Miniprograms', 'middleware' => 'auth:api'], function () {
Route::group(['namespace' => 'Admin\Miniprograms'], function () {
  // 小程序
  Route::post('miniprograms/add', 'ModulesController@add'); // 添加小程序模块
  Route::post('miniprograms/edit/{id}', 'ModulesController@edit'); // 修改小程序模块
  Route::get('miniprograms/delete/{id}', 'ModulesController@delete'); // 删除小程序模块

  // 小程序案例
  Route::post('miniprograms/cases/add', 'CasesController@add'); // 添加案例
  Route::get('miniprograms/cases/{id}', 'CasesController@single'); // 单个案例数据
  Route::get('miniprograms/cases', 'CasesController@list');
  Route::post('miniprograms/cases/update/{id}', 'CasesController@update'); // 修改案例
});

// 云网客
Route::group(['namespace' => 'Admin\Yunwangke'], function () {
  Route::post('yunwangke/add', 'ProjectController@add');
  Route::get('yunwangke/list', 'ProjectController@list');
  Route::get('yunwangke/single/{id}', 'ProjectController@single');
  Route::get('yunwangke/partner/list', 'PartnerController@list');
  Route::post('yunwangke/partner/add', 'PartnerController@add');
  Route::post('yunwangke/partner/delete/{id}', 'PartnerController@delete');
});

// 后台不需登录信息
Route::group(['namespace' => 'Admin'], function () {
  Route::get('miniprograms', 'Miniprograms\ModulesController@list');
  Route::get('miniprograms/selects', 'Miniprograms\ModulesController@selects');
  Route::get('miniprograms/{id}', 'Miniprograms\ModulesController@single'); // 获取单个应用数据
  Route::get('industry', 'IndustryController@all'); // 获取全部行业
  Route::get('industry/del/{id}', 'IndustryController@del'); // 获取全部行业
});

Route::middleware('auth:api')->post('upload', 'FilesystemController@upload');