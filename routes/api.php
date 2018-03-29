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
	return $request->user();
});

// 后台需登录信息
// Route::group(['namespace' => 'Admin', 'middleware' => 'auth:api'], function () {
Route::group(['namespace' => 'Admin'], function () {
	// 小程序
	Route::post('industry/add', 'IndustryController@add'); // 添加行业
	Route::post('industry/update/{id}', 'IndustryController@update'); // 修改行业

	// 客户
	Route::post('admin/custom/add', 'CustomController@add'); // 修改案例
	Route::get('admin/custom/list', 'CustomController@list'); // 修改案例
	Route::get('admin/custom/all', 'CustomController@all'); // 搜索

	// 站点
	Route::group(['namespace' => 'Site'], function () {
		Route::get('admin/website', 'SiteController@index');
		Route::get('admin/website/all', 'SiteController@all');
		Route::post('admin/website/add', 'SiteController@add');
		Route::post('admin/website/edit', 'SiteController@edit');
		Route::get('admin/website/delete/{id}', 'SiteController@del');

		// 友情链接 项目
		Route::get('admin/website/friend', 'FriendController@friend');
		Route::post('admin/website/friend/add', 'FriendController@add');
		Route::post('admin/website/friend/edit/{id}', 'FriendController@edit');
		Route::get('admin/website/friend/delete/{id}', 'FriendController@delete');
		Route::get('admin/website/friend/info/{id}', 'FriendController@info');

		// 友情链接 链接
		Route::get('admin/website/friend_link/list/{id}', 'FriendLinkController@list')->name('friend_link_list'); // 友情链接列表
		Route::get('admin/website/friend_link/info/{id}', 'FriendLinkController@info')->name('friend_link_info');
		Route::post('admin/website/friend_link/add', 'FriendLinkController@add')->name('friend_link_add');
		Route::post('admin/website/friend_link/edit/{id}', 'FriendLinkController@edit')->name('friend_link_edit');
		Route::post('admin/website/friend_link/sync_success', 'FriendLinkController@sync_success')->name('friend_link_sync_success');
		Route::get('admin/website/friend_link/delete/{id}', 'FriendLinkController@delete')->name('friend_link_delete');

		// 友情链接 同步
		Route::post('admin/website/friend_remote/sync', 'RemoteController@sync'); // 同步所有
		Route::post('admin/website/friend_remote/update', 'RemoteController@update'); // 更改链接
		Route::get('admin/website/friend_remote/delete/{id}', 'RemoteController@delete'); // 更改链接
		Route::post('admin/website/friend_remote/install/{id}', 'RemoteController@install'); // 安装
		Route::get('admin/website/friend_remote/test/{id}', 'RemoteController@test'); // 请求站点
		Route::get('admin/website/friend_remote/config_get/{id}', 'RemoteController@config_get'); // 获取config
		Route::post('admin/website/friend_remote/style_set/{id}', 'RemoteController@style_set'); // 请求站点
	});

	// 关键词
	Route::post('admin/keyword/add', 'KeywordController@add');
	Route::post('admin/keyword/edit', 'KeywordController@edit');
	Route::get('admin/keyword/delete/{id}', 'KeywordController@delete');
	Route::get('admin/keyword/first_rank_update', 'KeywordController@first_rank_update')->name('first_rank_update');
	Route::get('admin/keyword/hash_update', 'KeywordController@hash_update')->name('hash_update'); // 更新关键词hash值
	Route::post('admin/keyword/rank_update', 'KeywordController@rank_update')->name('rank_update'); // 更新排名
	Route::get('admin/keyword/query', 'KeywordController@query');
	Route::get('admin/keyword/export/{type}/{id}', "KeywordController@export"); // 导出excel

	// 任务管理
	Route::group(['namespace' => 'Task'], function () {
		// 关键词任务
		Route::get('admin/task/wordrank/list', 'WordTaskController@list')->name('admin_word_task_list'); // 任务列表
		Route::post('admin/task/wordrank/add_task', 'WordTaskController@add_task')->name('admin_wordrank_task_add'); // 添加任务
	});

	// 小程序
	Route::group(["namespace" => "Miniprograms"], function () {
		Route::get('admin/miniprograms/list', 'ModulesController@list');
		Route::post('admin/miniprograms/add', 'ModulesController@add'); // 添加小程序模块
		Route::post('admin/miniprograms/edit/{id}', 'ModulesController@edit'); // 修改小程序模块
		Route::get('admin/miniprograms/delete/{id}', 'ModulesController@delete'); // 删除小程序模块
	});

	// 案例
	Route::group(['namespace' => 'Case'], function () {
		// 小程序
		Route::get('admin/cases/miniprograms', 'MiniprogramsController@list'); // 列表
		Route::post('admin/cases/miniprograms/add', 'MiniprogramsController@add'); // 添加
		Route::get('admin/cases/miniprograms/{id}', 'MiniprogramsController@single'); // 详情
		Route::post('admin/cases/miniprograms/update/{id}', 'MiniprogramsController@update'); // 修改
	});
});

// 开放接口
Route::middleware([])->group(function () {
	// 云网客
	Route::group(['namespace' => 'Admin\Yunwangke'], function () {
		Route::get('yunwangke/partner/all', 'PartnerController@all'); // 获取所有合作网站域名
	});

	// 任务
	Route::group(['namespace' => 'Task'], function () {
		// 关键词任务
		Route::get('task/wordrank/rank', 'WordTaskController@rank')->name('word_task_rank'); //
		Route::get('task/wordrank/{id}', 'WordTaskController@task')->name('word_task_info'); // 任务信息
	});
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
	Route::post('admin/yunwangke/add', 'ProjectController@add');
	Route::post('admin/yunwangke/edit', 'ProjectController@edit');
	Route::get('admin/yunwangke/list', 'ProjectController@list');
	Route::get('admin/yunwangke/delete/{id}', 'ProjectController@del');
	Route::get('admin/yunwangke/single/{id}', 'ProjectController@single');
	Route::get('admin/yunwangke/partner/list', 'PartnerController@list');
	Route::post('admin/yunwangke/partner/add', 'PartnerController@add');
	Route::post('admin/yunwangke/partner/delete/{id}', 'PartnerController@delete');
});

// 后台不需登录信息
Route::group(['namespace' => 'Admin'], function () {
	Route::get('admin/website/friend_link/all/{id}', 'Site\FriendLinkController@all')->name('friend_link_all'); // 友情链接获取全部链接

	Route::get('miniprograms', 'Miniprograms\ModulesController@list');
	Route::get('miniprograms/selects', 'Miniprograms\ModulesController@selects');
	Route::get('miniprograms/{id}', 'Miniprograms\ModulesController@single'); // 获取单个应用数据
	Route::get('admin/industry', 'IndustryController@all'); // 获取全部行业
	Route::get('admin/industry/del/{id}', 'IndustryController@del'); // 获取全部行业
});

Route::middleware('auth:api')->post('upload', 'FilesystemController@upload');
