<?php 

namespace App\Http\Controllers\Admin\Miniprograms;

use App\Http\Controllers\ApiController;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// 模型
use App\MiniprogramCases as Model;

// 资源类型
use App\Http\Resources\Miniprogram;

class CasesController extends ApiController {

  public function list (Request $req) {
    $where = $req->only('industry');
    $appid = $req->input('app', false);
    $where = array_filter($where);
    if ($appid) {
      return Model::where($where)->whereRaw('FIND_IN_SET('.$appid.',modules)')->paginate(20);
    }
    return Model::where($where)->paginate(20);
  }

  // 添加数据
  public function add (Request $req) {
    if (!$req->filled('industry') || !$req->filled('name') || !$req->filled('thumb') || !$req->filled('modules') || !$req->filled('codeimg')) {
       return $this->failed('请正确填写信息');
    }

    $model = new Model;
    $model->name = $req->input('name');
    $model->thumb = $req->input('thumb');
    $model->codeimg = $req->input('codeimg'); // 二维码
    $model->industry = $req->input('industry');

    if (!$req->filled('modules')) {
      $model->modules = 'null';
    } else {
      $model->modules = implode(',', $req->input('modules'));
    }

    $res = $model->save();

    if ($res) {
      return $this->success(['id' => $model->id]); // 返回数据id
    }

    return $this->failed('添加失败');
  }

  public function update (Request $req, $id) {
    if (!$req->filled('industry') || !$req->filled('name') || !$req->filled('thumb') || !$req->filled('modules') || !$req->filled('codeimg')) {
       return $this->failed('请正确填写信息');
    }

    $model = Model::find($id);
    $model->name = $req->input('name');
    $model->thumb = $req->input('thumb');
    $model->codeimg = $req->input('codeimg'); // 二维码
    $model->industry = $req->input('industry');

    if (!$req->filled('modules')) {
      $model->modules = 'null';
    } else {
      $model->modules = implode(',', $req->input('modules'));
    }

    $res = $model->save();

    if ($res) {
      return $this->success(['id' => $model->id]); // 返回数据id
    }

    return $this->failed('修改失败');
  }

  public function single ($id) {
    $data = Model::find($id);
    
    // 获取模块数据
    if ($data->modules != null) {
      $module_ids = explode(',', $data->modules);
      $data->modules = DB::table('miniprograms')->whereIn('id', $module_ids)->select('id', 'name')->get(); // 获取行业信息
    } else {
      $data->modules = [];
    }

    $data->industry = DB::table('industry')->where('id', $data->industry)->first(); // 获取行业信息
    return $data;
  }

}