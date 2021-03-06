<?php

namespace App\Http\Controllers\Site;

use App\FriendLink;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendLinkController extends ApiController {
	public function list(Request $req, $id) {
		$pagesize = $req->input('pagesize', 10);
		$data = FriendLink::where("friend_id", $id)->orderBy('id', 'desc')->paginate($pagesize);

		return $this->success($data);
	}

	public function all($id) {
		$data = FriendLink::where("friend_id", $id)->orderBy('id', 'desc')->get();
		return $this->success($data);
	}

	public function add(Request $req) {
		if (!$req->filled(['friend_id', 'name', 'link'])) {
			return $this->failed('参数不正确');
		}
		$form = $req->only(['friend_id', 'name', 'link', 'spider_show']);

		// 重复判断
		$has_link = FriendLink::where('friend_id', $form['friend_id'])->where('link', $form['link'])->count();
		$has_name = FriendLink::where('friend_id', $form['friend_id'])->where('name', $form['name'])->count();
		if ($has_link) {
			return $this->failed('链接地址已存在');
		}
		if ($has_name) {
			return $this->failed('网站名称已存在');
		}

		$model = new FriendLink;
		foreach ($form as $key => $value) {
			$model->$key = $value;
		}

		$res = $model->save();
		if ($res) {
			return $this->success($model);
		}

		return $this->failed('添加失败，发生未知错误。');
	}

	public function add_multiple(Request $req) {
		if (!$req->filled(['friend_id', 'data'])) {
			return $this->failed('参数不正确');
		}

		$friendid = $req->input('friend_id');
		$data = $req->input('data');

		$names = [];
		$links = [];
		foreach ($data as $key => $value) {
			$names[] = $value["name"];
			$links[] = $value["link"];
		}

		// 查询重复
		$name_repeat = FriendLink::where('friend_id', $friendid)->whereIn("name", $names)->select("name")->get()->toArray();
		$link_repeat = FriendLink::where('friend_id', $friendid)->whereIn("link", $links)->select("link")->get()->toArray();

		// 过滤重复
		$upRepeat = []; // 非重复数据
		foreach ($data as $key => $value) {
			if (!in_array(["name" => $value["name"]], $name_repeat) && !in_array(["link" => $value["link"]], $link_repeat)) {
				$value["friend_id"] = $friendid;
				$upRepeat[] = $value;
			}
		}

		$res = DB::table("friend_links")->insert($upRepeat);

		if ($res) {
			return $this->message("success");
		}
		return $this->failed("发生未知错误，保存失败。");
	}

	public function edit(Request $req, $id) {
		$form = $req->only(['friend_id', 'name', 'link', 'spider_show', 'synced']);

		$model = FriendLink::find($id);

		// 获取项目id
		if ($req->filled('friend_id')) {
			$friendid = $req->input('friend_id');
		} else {
			$friendid = $model->friend_id;
		}

		// 重复判断
		if ($req->filled('link')) {
			$has_link = FriendLink::where('id', '<>', $id)->where('friend_id', $friendid)->where('link', $form['link'])->count();
			if ($has_link) {
				return $this->failed('链接已存在');
			}
		}

		if ($req->filled('name')) {
			$has_name = FriendLink::where('id', '<>', $id)->where('friend_id', $friendid)->where('name', $form['name'])->count();
			if ($has_link) {
				return $this->failed('网站名称已存在');
			}
		}

		$model->synced = 0;

		foreach ($form as $key => $value) {
			$model->$key = $value;
		}

		$res = $model->save();
		if ($res) {
			return $this->success($model);
		}

		return $this->failed('修改失败，发生未知错误。');
	}

	public function delete($id) {
		$res = FriendLink::destroy($id);
		if ($res) {
			return $this->message('success');
		}
		return $this->failed('发生未知错误，删除失败。');
	}

}
