<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// 模型
use App\Custom as Model;

class CustomController extends ApiController {

  // 添加数据
  public function add (Request $req) {
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
      return $this->success('添加成功');
    }

    return $this->failed('添加失败');
  }

  public function list () {
    if (isset($_GET["pagesize"]) && intval($_GET["pagesize"]) > 0) {
      $pagesize = intval($_GET["pagesize"]);
    } else {
      $pagesize = 100;
    }

    return DB::table("customs")->paginate($pagesize);
  }

}