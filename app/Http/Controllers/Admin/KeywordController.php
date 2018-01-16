<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// 模型
use App\Keyword as Model;
use App\Yunwangke;
use App\Rank;

use Maatwebsite\Excel\Facades\Excel;

class KeywordController extends ApiController {

  // 添加数据
  public function add (Request $req) {
    if (!$req->filled('type') || !$req->filled('parent')) {
      return $req->failed('请正确填写信息');
    }

    $keywords = $req->input('keywords', []);
    if (count($keywords) == 0) {
      return $this->failed('关键词为空');
    }

    $parent = $req->input('parent');
    $project_type = $req->input('type'); // 云网客或seo
    $data = [];
    foreach ($keywords as $key => $value) {
      $data[$key]['project_type'] = $project_type;
      $data[$key]['parent'] = $parent;
      $data[$key]['Keyword'] = $value;
      $data[$key]['hash'] = md5($value . $parent . $project_type);
    }

    $model = new Model;
    $res = $model->insert($data);
    print_r($res);
    if ($res) {
      return $this->success('添加成功');
    }

    return $this->failed('添加失败');
  }

  public function list () {
    if (isset($_GET['pagesize']) && intval($_GET['pagesize']) > 0) {
      $pagesize = intval($_GET['pagesize']);
    } else {
      $pagesize = 100;
    }

    return DB::table('customs')->paginate($pagesize);
  }

  public function first_rank_update (Request $req) {
    $pagesize = $req->input('pagesize', 1000);
    $keyword = Model::paginate($pagesize);

    foreach ($keyword as $key => $value) {
      // 获取第一排名位置
      $rank = Rank::where('keyword_id', $value->id)->where('rank', '>', 0)->select('rank')->orderBy('rank', 'asc')->first();
      if (isset($rank->rank)) {
        $value->first_rank_bd = $rank->rank;
        $value->save();
      }
    }

    if ($keyword->lastPage() != $keyword->currentPage()) {
      echo '<meta http-equiv="Refresh" content="0; url='. $keyword->nextPageUrl() .'" />';
    }

  }

  public function hash_update (Request $req) {
    $pagesize = $req->input('pagesize', 1000);
    $keyword = Model::paginate($pagesize);

    foreach ($keyword as $key => $value) {
      $value->hash = md5($value->keyword . $value->parent . $value->project_type);
      $value->save();
    }

    if ($keyword->lastPage() != $keyword->currentPage()) {
      echo '<meta http-equiv="Refresh" content="0; url='. $keyword->nextPageUrl() .'" />';
    }

  }

  public function query (Request $req) {
    if (!$req->filled('query')) {
      return $this->failed('query cannot null');
    }
    $pagesize = $req->input('pagesize', 10);
    $type = $req->input('type', false);
    
    $parent = false;
    if ($type) {
      $parent = $req->input('parent_id', false);
    }

    $queryword = $req->input('query', false);

    $where = [];
    $where[] = ['keyword', 'like', '%' . $queryword . '%'];
    if ($parent) {
      $where[] = ['project_type', $type];
      $where[] = ['parent', $parent];
    }

    $data = Model::where($where)->paginate($pagesize);

    return $this->success($data);
  }

  public function rank_update (Request $req) {
    if (!$req->filled('keyword_id') || !$req->filled('ranks') || !$req->filled('site')) {
      $this->failed('参数错误');
    }

    $site = $req->input('site');

    $keyword_id = $req->input('keyword_id');
    $keyword = Model::find($keyword_id);

    // 解析排名
    $ranks = $req->input('ranks');
    $hash = [];
    $rankhash = []; // 排名哈希键名
    foreach ($ranks as $key => $value) {
      $thehash = md5($value['url'] . $keyword_id . $site);
      $hash[] = $thehash;
      $rankhash[$thehash] = $value;
    }

    // 更新对应哈希排名
    $getranks = Rank::whereIn('hash', $hash)->get();
    $first_rank = 0;
    foreach($getranks as $v) {
      // 获取排名第一
      if ($v->rank > 0) {
        if ($v->rank < $first_rank || $first_rank == 0) {
          $first_rank = $v->rank;
        }
      }

      $newrank = $rankhash[$v->hash];
      $v->rankchange = $v->rank - $newrank['rank'];
      $v->rank = $newrank['rank'];

      $res = $v->save();
      if (!$res) {
        return $this->failed('保存失败');
      }

      unset($rankhash[$v->hash]); // 删除以保存数据
    }

    // 保存新排名
    $newrank = new Rank;
    $insertdata = [];
    foreach ($rankhash as $key => $value) {
      $insertdata[] = [
        'rank' => $value['rank'],
        'url' => $value['url'],
        'rankchange' => 0,
        'hash' => $key,
        'project_type' => $keyword->project_type,
        'keyword_id' => $keyword_id,
        'keyword' => $keyword->keyword
      ];
    }
    $newrank->insert($insertdata);

    // 保存关键第一排名
    $first_rank_table = 'first_rank_' . $site;
    $keyword->$first_rank_table = $first_rank;
    $res = $keyword->save();
    if (!$res) {
      return $this->failed('保存失败');
    }

    return $this->success('保存失败');
  }

  public function export(Request $req, $type, $pid) {
    if (!$req->filled('site')) {
      return $this->failed('参数不正确');
    }

    $site = $req->input('site');
    $info = Yunwangke::find($pid);

    if (!isset($info->custom->name)) {
        return "信息不能存在";
    }
    
    $top = 1;
    if(isset($_GET["top"]) && intval($_GET["top"])) {
        $top = intval($_GET["top"]);
    }
    $rn = $top*10; // 前N条结果

    $first_table = 'first_rank_' . $site;
    $words = Model::where($first_table, '<=', 10)->where($first_table, '>', 0)->orderBy($first_table, 'desc')->get();

    $rows = array();
    $rows[] = array("前" .$rn. "页关键词排名情况");
    $rows[] = array("关键词", "排名", "查询时间");
    // $wordbyid[$v->kwid]->homenum = 100;
    foreach ($words as $key => $value) {
      $rows[] = [
        $value->keyword,
        $value->$first_table,
        $value->updated_at
      ];
    }

    $filename = $info->custom->name . date("Y-m-d");

    Excel::create($filename, function($excel) use ($rows){
        $excel->sheet('排名概况', function($sheet) use ($rows){
          $sheet->rows($rows);
          $sheet->getColumnDimension('A')->setWidth(200);
        });
    })->export('xls');
  }

}