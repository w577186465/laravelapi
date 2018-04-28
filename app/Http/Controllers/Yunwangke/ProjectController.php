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
		if (!$req->filled('name') || !$req->filled('username') || !$req->filled('password') || !$req->filled('ywkid') || !$req->filled('customid')) {
			return $this->failed('请正确填写信息');
		}

		$name = $req->input('name');
		$username = $req->input('username'); // 用户名
		$ywkid = $req->input('ywkid'); // 云网客id

		$has_ywkid = Model::where('ywkid', $ywkid)->first();
		if (!is_null($has_ywkid)) {
			return $this->failed('云网客id已存在');
		}

		$has_name = Model::where('name', $name)->first();
		if (!is_null($has_name)) {
			return $this->failed('项目名称已存在');
		}

		$model = new Model;
		// 保存
		$model->name = $name;
		$model->username = $req->input('username');
		$model->password = $req->input('password');
		$model->ywkid = $req->input('ywkid');
		$model->customid = $req->input('customid');
		$model->industry = $req->input('industry');
		$model->case = $req->input('case', false);

		$model->save();
		$res = $this->create_data($model->id);

		if ($res) {
			return $this->success('添加成功');
		}

		return $this->failed('添加失败');
	}

	// 修改
	public function edit(Request $req, $id) {
		$data = $req->only("name", "industry", "username", "password", "ywkid", "customid", "case");
		if (empty($data)) {
			return $this->failed("参数不正确");
		}

		$has_ywkid = Model::where('id', '<>', $id)->where('ywkid', $data['ywkid'])->first();
		if (!is_null($has_ywkid)) {
			return $this->failed('云网客id已存在');
		}

		$has_name = Model::where('id', '<>', $id)->where('name', $data["name"])->first();
		if (!is_null($has_name)) {
			return $this->failed('项目名称已存在');
		}

		$model = Model::find($id);

		if (is_null($model)) {
			return $this->failed('该项目不存在。');
		}

		// 保存
		foreach ($data as $key => $value) {
			$model->$key = $value;
		}

		$res = $model->save();

		if ($res) {
			return $this->message('success');
		}

		return $this->failed('修改失败，发生未知错误。');
	}

	// 创建副表
	private function create_data($id, $data = []) {
		$model = new YunwangkeData;
		$model->yunwangke_id = $id;
		foreach ($data as $key => $value) {
			$model->$key = $value;
		}
		$res = $model->save();
		return $res;
	}

	// 保存副表
	public function save_data(Request $req, $id) {
		$find = YunwangkeData::where("yunwangke_id", $id)->first();
		$data = $req->only(["cookies"]);
		if (is_null($find)) {
			$res = $this->create_data($id, $data);
			if ($res) {
				return $this->message("success");
			}
			return $this->failed("发生未知错误，保存失败。");
		}

		$res = YunwangkeData::where("yunwangke_id", $id)->update($data);
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
		$keyword = Keyword::where('parent', $id)->first();

		// 判断是否有关键词
		if (!is_null($keyword)) {
			$res = Keyword::where('parent', $id)->delete(); // 有则删除
			if (!$res) {
				return $this->failed('发生未知错误，删除失败。');
			}
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