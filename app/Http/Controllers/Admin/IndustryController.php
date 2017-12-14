<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class IndustryController extends ApiController {

    public function all () {
        return DB::table("industry")->orderBy("id", "desc")->get();
    }

    // 删除行业
    public function del ($id) {
        $res = DB::table("industry")->where("id", $id)->delete();
        if ($res) {
            return $this->message("删除成功");
        }
    }

    public function add (Request $req) {
        if (!$req->filled('name')) {
          return $this->failed('请输入行业名称');
        }
        $name = $req->input('name');
        $res = DB::table("industry")->where("name", $name)->select("id")->first();
        if (isset($res->id)) {
            return $this->failed('该行业已存在');
        }

        $data = array();
        $data["name"] = $name;
        $res = DB::table("industry")->insert($data);

        if ($res) {
            return $this->message('添加成功');
        } else {
            return $this->failed('添加失败');
        }
    }

    public function update (Request $req, $id) {
        if (!$req->filled('name')) {
          return $this->failed('请输入行业名称');
        }
        $name = $req->input('name');
        $res = DB::table("industry")->where("id", "<>", $id)->where("name", $name)->select("id")->first();
        if (isset($res->id)) {
            return $this->failed('该行业已存在');
        }

        $data = array();
        $data["name"] = $name;
        $res = DB::table("industry")->where("id", $id)->update($data);

        if ($res) {
            return $this->message('修改成功');
        } else {
            return $this->failed('添加失败');
        }
    }

    public function data () {
        if (isset($_GET["pagesize"]) && intval($_GET["pagesize"]) > 0) {
            $pagesize = intval($_GET["pagesize"]);
        } else {
            $pagesize = 10;
        }
      return DB::table("industry")->paginate($pagesize);
    }

    public function query () {
      if (!isset($_GET["q"]) || $_GET["q"] == "") {
        return prompt(0, "请输入关键字");
      }
      $data = DB::table("industry")->where("name","like","%" . $_GET["q"] . "%")->get();
      return prompt(1, "success", $data);
    }


    public function edit ($id) {
        $data = array();
        $data["name"] = $_GET["name"];
        $res = DB::table("industry")->where("id", $id)->update($data);
        if ($res) {
            return prompt(1, "修改成功");
        } else {
            return prompt(0, "修改失败");
        }
    }

    public function delete ($id) {
        return prompt(1, "修改成功");
        // $res = DB::table("industry")->where("id", $id)->delete();
        // if ($res) {
        //     return prompt(1, "修改成功");
        // } else {
        //     return prompt(0, "修改成功");
        // }
    }

    public function getindustry ($id) {
        $res = DB::table('industry')->where('id', $id)->first();
        if ($res) {
            return prompt(1, "获取成功", $res);
        } else {
            return prompt(1, "获取失败");
        }
    }

}