<?php

namespace App\Http\Controllers\Admin\Miniprograms;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Miniprogram;
use App\Miniprograms as Model;

// 模型
use Illuminate\Http\Request;

// 资源类型
use Illuminate\Support\Facades\DB;

class ModulesController extends ApiController {

	public function list() {
		$res = DB::table('miniprograms')->orderBy('id', 'desc')->paginate(50);
		return $res;
	}

	public function single($id) {
		$res = Model::find($id);
		$module_ids = explode(',', $res->modules);
		$modules = Model::whereIn('id', $module_ids)->select('id', 'name')->get();
		$res->modules = $modules;
		$res->images = explode(',', $res->images);
		return new Miniprogram($res);
	}

	// 模块选择数据
	public function selects() {
		return DB::table('miniprograms')->select('id', 'name')->get();
	}

	// 添加数据
	public function add(Request $req) {
		if (!$req->filled('images') || !$req->filled('name') || !$req->filled('thumb') || !$req->filled('description') || !$req->filled('content')) {
			return $this->failed('请正确填写信息');
		}

		$model = new Model;
		$model->name = $req->input('name');
		$model->thumb = $req->input('thumb');
		$model->modules = implode(',', $req->input('modules'));
		$model->description = $req->input('description');
		$model->content = $req->input('content');
		$model->images = implode(',', $req->input('images'));

		if ($req->filled('modules')) {
			$model->modules = 'null';
		} else {
			$model->modules = $req->input('modules');
		}

		$res = $model->save();

		if ($res) {
			return $this->success(['id' => $model->id]); // 返回数据id
		}

		return $this->failed('添加失败');
	}

	// 删除
	public function delete($id) {
		$res = Model::destroy($id);
		if ($res) {
			return $this->message('删除成功');
		}
	}

	public function edit(Request $req, $id) {
		if (!$req->filled('images') || !$req->filled('name') || !$req->filled('thumb') || !$req->filled('description') || !$req->filled('content')) {
			return $this->failed('请正确填写信息');
		}

		$data = $req->only('name', 'thumb', 'modules', 'description', 'content');
		if (!$req->filled('modules')) {
			$data['modules'] = 'null'; // 搭配模块 默认空
		}
		$data['images'] = implode(',', $req->input('images')); // 多图数据转换

		$res = DB::table('miniprograms')->where('id', $id)->update($data);

		if ($res) {
			return $this->message('success', $res);
		}

		return $this->failed('添加失败');
	}

}