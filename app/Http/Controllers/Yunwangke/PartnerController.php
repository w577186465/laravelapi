<?php

namespace App\Http\Controllers\Yunwangke;

use App\Http\Controllers\ApiController;
use App\YunwangkePartner as Model;
use Illuminate\Http\Request;

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

	public function edit(Request $req, $id) {
		$data = $req->only("name", "domain");
		if (empty($data)) {
			return $this->failed("参数不正确");
		}

		// 重复判断
		if (Model::where('name', $data["name"])->where('id', '<>', $id)->count() > 0) {
			return $this->failed("网站名称已存在");
		}
		if (Model::where('domain', $data["domain"])->where('id', '<>', $id)->count() > 0) {
			return $this->failed("域名已存在");
		}

		$model = Model::find($id);
		foreach ($data as $key => $value) {
			$model->$key = $value;
		}
		$res = $model->save();
		if ($res) {
			return $this->message("success");
		}
		return $this->failed("发生未知错误，修改失败。");
	}

	public function delete($id) {
		$res = Model::destroy($id);
		if ($res) {
			return $this->message("success");
		}
		return $this->failed("发生未知错误，操作失败");
	}

	public function list(Request $req) {
		$pagesize = $req->input('pagesize', 10);

		$data = Model::orderBy('id', 'desc')->paginate($pagesize);
		return $this->success($data);
	}

	public function all() {
		return $this->success(Model::select('id', 'domain')->get());
	}

}