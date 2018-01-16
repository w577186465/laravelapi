<?php 

namespace App\Http\Controllers\Admin\Yunwangke;

use App\Http\Controllers\ApiController;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// 模型
use App\Yunwangke as Model;
use App\Keyword;
use App\Rank;

class ProjectController extends ApiController {

  // 添加数据
  public function add (Request $req) {
    if (!$req->filled('username') || !$req->filled('password') || !$req->filled('ywkid') || !$req->filled('customid')) {
      return $this->failed('请正确填写信息');
    }

    $username = $req->input('username'); // 用户名
    $ywkid = $req->input('ywkid'); // 云网客id

    $find = Model::where('username', $username)->where('ywkid', $ywkid)->first();

    $model = new Model;

    // 保存
    $model->username = $req->input('username');
    $model->password = $req->input('password');
    $model->ywkid = $req->input('ywkid');
    $model->customid = $req->input('customid');
    $model->industry = $req->input('industry');
    $model->case = $req->input('case', false);

    $res = $model->save();

    if ($res->id) {
      return $this->success('添加成功');
    }

    return $this->failed('添加失败');
  }

  public function list (Request $req) {
    $pagesize = $req->input('pagesize', 15);

    $data = DB::table('yunwangkes')
                ->leftJoin('customs', 'customs.id', 'yunwangkes.customid')
                ->leftJoin('industry', 'industry.id', 'yunwangkes.industry')
                ->orderBy('yunwangkes.id', 'desc')
                ->select('yunwangkes.id', 'customs.name', 'yunwangkes.ywkid', 'yunwangkes.industry', 'yunwangkes.case')
                ->paginate($pagesize);
    return $this->success($data);
  }

  public function single (Request $req, $id) {
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

}