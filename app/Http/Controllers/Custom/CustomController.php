<?php

namespace App\Http\Controllers\Custom;

use App\Custom as Model;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

// 模型

class CustomController extends ApiController {

	// 添加数据
	public function add(Request $req) {
		if (!$req->filled('name')) {
			return $this->failed('请正确填写信息');
		}

		$name = $req->input('name'); // 客户名称

		$model = new Model;

		// 判断重复
		$res = $model->where("name", $name)->select("id")->first();
		if (isset($res->id)) {
			return $this->failed('客户已存在');
		}

		// 保存
		$model->name = $name;
		$res = $model->save();

		if ($res) {
			return $this->success($model);
		}

		return $this->failed('添加失败');
	}

	public function list() {
		if (isset($_GET["pagesize"]) && intval($_GET["pagesize"]) > 0) {
			$pagesize = intval($_GET["pagesize"]);
		} else {
			$pagesize = 100;
		}

		return Model::paginate($pagesize);
	}

	public function all(Request $req) {
		$data = Model::orderBy('id', 'desc')->get();
		return $this->success($data);
	}

}