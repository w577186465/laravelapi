<?php

namespace App\Http\Controllers\Admin\Site;

use App\Friend;
use App\FriendLink;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class FriendController extends ApiController {

	public function friend(Request $req) {
		$pagesize = $req->input('pagesize', 10);
		$data = Friend::with('site')->orderBy('id', 'desc')->paginate($pagesize);

		return $this->success($data);
	}

	public function info($id) {
		$data = Friend::find($id);
		$data->server_uri = env("APP_URL");
		return $this->success($data);
	}

	public function all(Request $req) {
		$data = Friend::orderBy('id', 'desc')->get();
		return $this->success($data);
	}

	public function add(Request $req) {
		if (!$req->filled(['home_url', 'page_url', 'site_id'])) {
			return $this->failed('网站首页、所属站点为必填项');
		}
		$form = $req->only(['home_url', 'site_id', 'page_url']);

		// 重复判断
		$has_site = Friend::where('home_url', $form['home_url'])->count();
		if ($has_site) {
			return $this->failed('站点地址已存在');
		}

		$model = new Friend;
		$model->home_url = $form['home_url'];
		$model->page_url = $form['page_url'];
		$model->site_id = $form['site_id'];
		$model->secret = bcrypt(time());
		$model->status = 0;

		$res = $model->save();
		if ($res) {
			return $this->success($model);
		}

		return $this->failed('添加失败，发生未知错误。');
	}

	public function edit(Request $req, $id) {
		$form = $req->only(['home_url', 'site_id', 'status', 'syncstatus']);

		// 重复判断
		if (isset($form['home_url'])) {
			$has_site = Friend::where('id', '<>', $id)->where('home_url', $form['home_url'])->count();
			if ($has_site) {
				return $this->failed('站点地址已存在');
			}
		}

		$model = Friend::find($id);
		foreach ($form as $key => $value) {
			$model->$key = $value;
		}

		$res = $model->save();
		if ($res) {
			return $this->success($model);
		}

		return $this->failed('添加失败，发生未知错误。');
	}

	public function delete($id) {
		$count = FriendLink::where("friend_id", $id)->count();
		if ($count > 0) {
			$res = FriendLink::where("friend_id", $id)->delete();
			if (!$res) {
				return $this->failed('链接删除失败，请重试。');
			}
		}

		$res = Friend::destroy($id);
		if ($res) {
			return $this->message('success');
		}
		return $this->failed('发生未知错误，删除失败。');
	}

}