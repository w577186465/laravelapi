<?php

namespace App\Http\Controllers\Admin\Yunwangke;

use App\Http\Controllers\ApiController;
use App\YunwangkePartner as Model;
use Illuminate\Http\Request;

// 模型

class PartnerController extends ApiController {

	// 添加数据
	public function add(Request $req) {
		if (!$req->filled('name') || !$req->filled('domain')) {
			return $this->failed('请正确填写信息');
		}

		$domain = $req->input('domain'); // 用户名

		$find = Model::where('domain', $domain)->first();
		if (isset($find->id)) {
			return $this->failed('该网站已存在');
		}

		$model = new Model;

		// 保存
		$model->name = $req->input('name');
		$model->domain = $domain;

		$res = $model->save();

		if ($res) {
			return $this->success('添加成功');
		}

		return $this->failed('添加失败');
	}

	public function delete($id) {
		$partner = Model::destroy($id);
		print_r($partner);
	}

	public function list(Request $req) {
		$pagesize = $req->input('pagesize', 10);

		$data = Model::orderBy('id', 'desc')->paginate($pagesize);
		return $this->success($data);
	}

	public function all() {
		return Model::select('id', 'domain')->get();
	}

}