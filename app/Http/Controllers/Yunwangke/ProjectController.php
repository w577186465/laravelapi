<?php

namespace App\Http\Controllers\Yunwangke;

use App\Http\Controllers\ApiController;
use App\Keyword;

// 模型
use App\Yunwangke as Model;
use Illuminate\Http\Request;

class ProjectController extends ApiController {
	public function update() {
		$data = Model::with('custom')->get();
		foreach ($data as $key => $value) {
			$m = Model::find($value->id);
			$m->name = $value->custom->name;
			$m->save();
		}
	}

	// 添加数据
	public function add(Request $req) {
		if (!$req->filled('username') || !$req->filled('password') || !$req->filled('ywkid') || !$req->filled('customid')) {
			return $this->failed('请正确填写信息');
		}

		$username = $req->input('username'); // 用户名
		$ywkid = $req->input('ywkid'); // 云网客id

		$find = Model::where('ywkid', $ywkid)->count();
		if ($find > 0) {
			return $this->failed('云网客id已存在');
		}

		$model = new Model;

		// 保存
		$model->username = $req->input('username');
		$model->password = $req->input('password');
		$model->ywkid = $req->input('ywkid');
		$model->customid = $req->input('customid');
		$model->industry = $req->input('industry');
		$model->case = $req->input('case', false);

		$res = $model->save();

		if ($res) {
			return $this->success('添加成功');
		}

		return $this->failed('添加失败');
	}

	// 修改
	public function edit(Request $req) {
		if (!$req->filled(['id', 'username', 'password', 'ywkid', 'customid'])) {
			return $this->failed('请正确填写信息');
		}

		$id = $req->input('id');
		$ywkid = $req->input('ywkid'); // 云网客id

		$find = Model::where('id', '<>', $id)->where('ywkid', $ywkid)->count();
		if ($find > 0) {
			return $this->failed('云网客id已存在');
		}

		$model = Model::find($id);

		if (empty($model)) {
			return $this->failed('该项目不存在。');
		}

		// 保存
		$model->username = $req->input('username');
		$model->password = $req->input('password');
		$model->ywkid = $ywkid;
		$model->customid = $req->input('customid');
		$model->industry = $req->input('industry');
		$model->case = $req->input('case', false);

		$res = $model->save();

		if ($res) {
			return $this->success('success');
		}

		return $this->failed('修改失败，发生未知错误。');
	}

	public function list(Request $req) {
		$pagesize = $req->input('pagesize', 15);

		$data = Model::with('custom')->orderBy('id', 'desc')->paginate($pagesize);

		return $this->success($data);
	}

	public function del($id) {
		$res = Model::destroy($id);
		if ($res) {
			return $this->message('success');
		}
		return $this->failed('发生未知错误，删除失败。');
	}

	public function single(Request $req, $id) {
		$pagesize = $req->input('pagesize', 10);
		$queryword = $req->input('query', false);
		$keywords = Keyword::with('ranks')->where('parent', $id)->where(function ($query) use ($req, $queryword) {
			$site = $req->input('site');
			$rank = $req->input('rank', 'all');
			$rank_column = 'first_rank_' . $site;

			if ($queryword) {
				$queryword = '%' . $queryword . '%';
				$query->where('keyword', 'like', $queryword);
			}

			if ($rank == 'f10') {
				$query->where($rank_column, '<', 10)->where('first_rank_bd', '>', 0);
			}

			if ($rank == 'f50') {
				$query->where($rank_column, '<', 50)->where('first_rank_bd', '>', 0);
			}

			if ($rank == 'b50') {
				$query->where($rank_column, 0);
			}
		})->paginate($pagesize);

		return $this->success($keywords);
	}

	// 项目信息
	public function info($id) {
		$info = Model::find($id);
		return $this->success($info);
	}

}