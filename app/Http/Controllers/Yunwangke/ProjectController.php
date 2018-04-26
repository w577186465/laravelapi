<?php

namespace App\Http\Controllers\Yunwangke;

use App\Http\Controllers\ApiController;
use App\Keyword;
use App\Yunwangke as Model;
use App\YunwangkeData;
use Illuminate\Http\Request;

class ProjectController extends ApiController {
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

		$data = new YunwangkeData;
		$data->yunwangke_id = $model->id;
		$res = $data->save();

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

	// 保存副表
	public function save_data(Request $req, $id) {
		$data = $req->only(["cookies"]);
		if (empty($data)) {
			return $this->failed("参数不正确");
		}

		$model = new YunwangkeData;
		$model->yunwangke_id = $id;
		$model->cookies = $data["cookies"];
		$res = $model->save();
		if ($res) {
			return $this->message("success");
		}

		return $this->failed("发生未知错误，保存失败。");
	}

	public function list(Request $req) {
		$pagesize = $req->input('pagesize', 15);

		$data = Model::with('custom')->orderBy('id', 'desc')->paginate($pagesize);

		return $this->success($data);
	}

	public function del($id) {
		$res = Keyword::where('parent', $id)->delete();
		if (!$res) {
			return $this->failed('发生未知错误，删除失败。');
		}

		$res = Model::destroy($id);
		if ($res) {
			return $this->message('success');
		}
		return $this->failed('发生未知错误，删除失败。');
	}

	public function single(Request $req, $id) {
		$pagesize = $req->input('pagesize', 10);
		$queryword = $req->input('query', false); // 搜索关键词
		$site = $req->input('site'); // 筛选站点
		$rank_column = 'first_rank_' . $site; // 排名列名

		// 排序
		$orderData = [
			'id' => 'id',
			'rank' => $rank_column,
		];
		$orderName = $req->input('orderby', 'id'); // 排序字段
		$order = $req->input('order', 'desc');
		$orderColumn = $orderData[$orderName];
		if (!array_key_exists($orderName, $orderData)) {
			$orderName = 'id';
			$order = 'desc';
		}

		$keywords = Keyword::with('ranks')->where('parent', $id)->where(function ($query) use ($req, $queryword, $rank_column) {
			$rank = $req->input('rank', 'all');

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
		})
			->orderBy('heart', 'desc')
			->orderBy($orderColumn, $order)
			->paginate($pagesize);

		return $this->success($keywords);
	}

	// 项目信息
	public function info($id) {
		$info = Model::find($id);
		return $this->success($info);
	}

}