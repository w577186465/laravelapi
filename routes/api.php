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
	// 任务管理
	Route::group(['namespace' => 'Task'], function () {
		// 关键词任务
		Route::get('admin/task/wordrank/list', 'WordTaskController@list')->name('admin_word_task_list'); // 任务列表
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

// 工具接口
Route::middleware([])->group(function () {
	// 云网客
	Route::group(['namespace' => 'Yunwangke'], function () {
		Route::get('tool/yunwangke/partner/all', 'PartnerController@all'); // 获取所有合作网站域名
	});

	// 任务
	Route::group(['namespace' => 'Task'], function () {
		Route::get('tool/task/wordrank/rank', 'WordTaskController@rank')->name('word_task_rank'); // 获取进行中的任务
		Route::get('tool/task/wordrank/data/{id}', 'WordTaskController@get_task_data')->name('word_task_data'); // 获取任务关键词
		Route::get('tool/task/wordrank/task_state/{id}', 'WordTaskController@task_state')->name('word_set_task_state'); // 任务完成更新状态
	});

	// 关键词
	Route::group(['namespace' => 'Keyword'], function () {
		Route::post('tool/keyword/rank/saverank', 'RankController@save_rank')->name('word_task_save_rank'); // 获取任务关键词
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

// 后台
// Route::group(['middleware' => 'auth:api'], function () {
Route::group([], function () {
	// 客户
	Route::group(['namespace' => 'Custom'], function () {
		Route::post('admin/custom/add', 'CustomController@add'); // 修改案例
		Route::get('admin/custom/list', 'CustomController@list'); // 修改案例
		Route::get('admin/custom/all', 'CustomController@all'); // 所有客户
	});

	// 行业
	Route::group(['namespace' => 'Industry'], function () {
		Route::get('admin/industry/all', 'IndustryController@all'); // 获取全部行业
		Route::post('admin/industry/add', 'IndustryController@add'); // 添加行业
		Route::post('admin/industry/update/{id}', 'IndustryController@update'); // 修改行业
	});

	Route::group(['namespace' => 'Keyword'], function () {
		// 关键词
		Route::post('admin/keyword/add', 'KeywordController@add');
		Route::post('admin/keyword/edit', 'KeywordController@edit');
		Route::get('admin/keyword/delete/{id}', 'KeywordController@delete');

		// Route::get('keyword/first_rank_update', 'KeywordController@first_rank_update')->name('first_rank_update');
		// Route::get('keyword/hash_update', 'KeywordController@hash_update')->name('hash_update'); // 更新关键词hash值
		// Route::post('keyword/rank_update', 'KeywordController@rank_update')->name('rank_update'); // 更新排名
		// Route::get('keyword/query', 'KeywordController@query');
		// Route::get('keyword/export/{type}/{id}', "KeywordController@export"); // 导出excel

		// 关键词排名
		Route::get('admin/keyword/ranks/{id}', 'RankController@ranks')->name('keyword-ranks'); // 项目详情页
		Route::get('admin/keyword/rank/delete/{id}', 'RankController@del')->name('keyword-rank-delete'); // 项目详情页
	});

	// 云网客
	Route::group(['namespace' => 'Yunwangke'], function () {
		// 项目
		Route::post('admin/yunwangke/project/add', 'ProjectController@add')->name('yunwangke-project-add'); // 添加项目
		Route::post('admin/yunwangke/project/edit/{id}', 'ProjectController@edit')->name('yunwangke-project-edit'); // 修改项目
		Route::get('admin/yunwangke/project/delete/{id}', 'ProjectController@del')->name('yunwangke-project-delete'); // 删除项目
		// Route::get('yunwangke', 'ProjectController@index')->name('yunwangke-project-index'); // 项目列表
		Route::get('admin/yunwangke/project/list', 'ProjectController@list')->name('yunwangke-project-list'); // 项目列表
		Route::get('admin/yunwangke/project/info/{id}', 'ProjectController@info')->name('yunwangke-project-info'); // 项目信息
		Route::get('admin/yunwangke/single/{id}', 'ProjectController@single')->name('yunwangke-project-single'); // 项目详情页
		Route::post('admin/yunwangke/save_data/{id}', 'ProjectController@save_data')->name('yunwangke-project-savedata');

		// 文章
		Route::get('admin/yunwangke/article/project', 'ArticleController@list')->name('yunwangke-article-project');

		// 合作网站
		Route::get('admin/yunwangke/partner/list', 'PartnerController@list')->name('yunwangke-partner-list'); // 合作网站列表
		Route::post('admin/yunwangke/partner/add', 'PartnerController@add')->name('yunwangke-partner-add'); // 添加合作网站
		Route::post('admin/yunwangke/partner/edit/{id}', 'PartnerController@edit')->name('yunwangke-partner-edit'); // 修改合作网站
		Route::get('admin/yunwangke/partner/delete/{id}', 'PartnerController@delete')->name('yunwangke-partner-delete');
	});

	// 自动发文
	Route::group(['namespace' => 'Writer'], function () {
		Route::post('admin/writer/article/add', 'ArticleController@add')->name('writer-article-add');
		Route::get('admin/yunwangke/article/list/{parent}', 'ArticleController@list')->name('yunwangke-article-list'); // 云网客文章列表
	});

	// 任务
	Route::group(['namespace' => 'Task'], function () {
		Route::post('admin/task/wordtask/add_task', 'WordTaskController@add_task')->name('admin_wordtask_task_add'); // 添加任务
	});

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
		Route::post('admin/website/friend_link/add_multiple', 'FriendLinkController@add_multiple')->name('friend_link_add_multiple');
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
});

// 开放接口
Route::group([], function () {
	Route::group(['namespace' => 'Site'], function () {
		Route::get('website/friend_link/all/{id}', 'FriendLinkController@all')->name('friend_link_all'); // 友情链接获取全部链接
	});
});

// 后台不需登录信息
Route::group(['namespace' => 'Admin'], function () {
	Route::get('miniprograms', 'Miniprograms\ModulesController@list');
	Route::get('miniprograms/selects', 'Miniprograms\ModulesController@selects');
	Route::get('miniprograms/{id}', 'Miniprograms\ModulesController@single'); // 获取单个应用数据
	Route::get('industry/del/{id}', 'IndustryController@del'); // 获取全部行业
});

Route::middleware('auth:api')->post('upload', 'FilesystemController@upload');
