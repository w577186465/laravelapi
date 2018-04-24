<?php

namespace App\Http\Controllers\Site;

use App\Friend;
use App\Http\Controllers\ApiController;
use App\Site;
use Illuminate\Http\Request;

class SiteController extends ApiController {

	public function index(Request $req) {
		$pagesize = $req->input('pagesize', 10);
		$data = [];
		$data['site'] = Site::orderBy('id', 'desc')->paginate($pagesize);

		return $this->success($data);
	}

	public function all(Request $req) {
		$data = Site::orderBy('id', 'desc')->get();
		return $this->success($data);
	}

	public function add(Request $req) {
		if (!$req->filled(['name', 'domain'])) {
			return $this->failed('站点名称、站点域名为必填项');
		}
		$form = $req->only(['name', 'domain', 'admin_url', 'admin_username', 'admin_password']);

		// 重复判断
		$has_name = Site::where('name', $form['name'])->count();
		if ($has_name) {
			return $this->failed('站点名称已存在');
		}
		$has_domain = Site::where('domain', $form['domain'])->count();
		if ($has_domain) {
			return $this->failed('站点域名已存在');
		}

		$site = new Site;
		foreach ($form as $key => $v) {
			$site->$key = $v;
		}

		$res = $site->save();
		if ($res) {
			return $this->success($site);
		}
		return $this->failed('添加失败，发生未知错误。');
	}

	public function edit(Request $req) {
		if (!$req->filled(['id', 'name', 'domain'])) {
			return $this->failed('站点名称、站点域名为必填项');
		}
		$id = $req->input('id');
		$form = $req->only(['name', 'domain', 'admin_url', 'admin_username', 'admin_password']);

		// 判断重复
		$has_name = Site::where('name', $form['name'])->where('id', '<>', $id)->count();
		if ($has_name) {
			return $this->failed('站点名称已存在');
		}
		$has_domain = Site::where('domain', $form['domain'])->where('id', '<>', $id)->count();
		if ($has_domain) {
			return $this->failed('站点域名已存在');
		}

		$site = Site::find($id);
		foreach ($form as $key => $v) {
			$site->$key = $v;
		}

		$res = $site->save();
		if ($res) {
			return $this->message('success');
		}
		return $this->failed('添加失败，发生未知错误。');
	}

	public function del($id) {
		$res = Friend::where("site_id", $id)->delete();
		if (!$res) {
			return $this->failed('发生未知错误，删除失败。');
		}

		$res = Site::destroy($id);
		if ($res) {
			return $this->message('success');
		}
		return $this->failed('发生未知错误，删除失败。');
	}

}