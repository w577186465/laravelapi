<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\ApiController;
use App\Http\Resources\RankqueryGettask;
use App\Http\Resources\WordTaskKeywords;
use App\Keyword;
use App\Rank;
use App\WordTask;
use App\Yunwangke;
use Illuminate\Http\Request;

class WordTaskController extends ApiController {

	// 添加任务
	public function add_task(Request $req) {
		if (!$req->filled('id') || !$req->filled('task_type')) {
			return $this->failed('请正确填写信息');
		}

		$id = $req->input('id');
		$ywk = Yunwangke::find($id);
		$name = $ywk->custom->name;

		// 保存
		$model = new WordTask;
		$model->pid = $req->input('id');
		$model->name = $name;
		$model->task_type = $req->input('task_type');
		$model->state = 0;

		// 查询站点
		$site = $req->input("site", ["all"]);
		$sites = ["all", "bd"]; // 所有支持的站点
		$siteData = array_intersect($sites, $site); // 需求站点与支持站点的交集 防止意外参数
		$model->site = implode(",", $siteData);

		// 查询范围
		$range = $req->input('query_range', 5);
		if ($range > 50) {
			return $this->failed("查询范围不能超过50页");
		}
		$model->query_range = $req->input('query_range', 5);

		// 重点词
		$important = $req->input('important', 1);
		$model->important = $important;

		$res = $model->save();

		if ($res) {
			return $this->success($model);
		}

		return $this->failed('添加失败');
	}

	public function rank(Request $req) {
		$type = $req->input("task_type");
		$data = WordTask::where('state', '0')->orWhere('state', '1')->get();
		return $this->success($data);
	}

	public function task($id) {
		$task = WordTask::find($id);
		if ($task->task_type == 'ywk') {
			$data = new RankqueryGettask(Yunwangke::find($task->pid));
			$data->site = $task->site;
			$data->task_type = $task->task_type;
			return $this->success($data);
		}
	}

	public function get_task_data(Request $req, $id) {
		$where = [];
		$task = WordTask::find($id);
		$important = $task->important;
		$keywords = $task->load(["keywords" => function ($query) use ($important) {
			$query->where("heart", $important);
		}]);
		$info = Yunwangke::select("username", "password", "ywkid")->find($task->pid);

		// $data = new RankqueryGettask($task->keywords);
		$data = [];
		$data["keywords"] = WordTaskKeywords::collection($task->keywords);
		$data["info"] = $info;
		return $this->success($data);
	}

	public function save_rank(Request $req) {
		if (!$req->filled(["id", "site", "rank"])) {
			return $this->failed("参数不正确");
		}

		$data = $req->only(["id", "site", "rank"]);

		$rankData = $data["rank"]; // 排名信息
		$keyword = Keyword::find($data["id"]); // 获取关键词信息

		if (empty($rankData)) {
			return $this->failed("排名数据不能为空");
		}

		foreach ($rankData as $key => $rank) {
			$hash = md5($data["id"] . $rank["Url"]); // 生成排名标识
			$getrank = Rank::where("hash", $hash)->first(); // 旧的排名信息

			// 保存排名信息
			if ($getrank) {
				// 排名相同跳过
				if ($rank["Rank"] == $getrank->rank) {
					continue;
				}

				$rank["rankchange"] = $getrank->rank - $rank["Rank"];
				Rank::where("hash", $hash)->update($rank);
			} else {
				$newrank = new Rank;
				$newrank->keyword_id = $data["id"];
				$newrank->keyword = $keyword->keyword;
				$newrank->site = $data["site"];
				$newrank->rank = $rank["Rank"];
				$newrank->url = $rank["Url"];
				$newrank->hash = $hash;
				$newrank->save();
			}
		}

		// 保存第一排名索引
		$columnName = "first_rank_" . $data["site"]; // 获取索引列名
		$kwmodel = Keyword::find($data["id"]);
		if ($kwmodel->$columnName != $rankData[0]["Rank"]) {
			$kwmodel->$columnName = $rankData[0]["Rank"];
			$kwmodel->save();
		}

		return $this->message("message");
	}

	// 设置任务状态
	public function task_state(Request $req, $id) {
		if (!$req->filled("state")) {
			return $this->failed("参数不正确");
		}
		// 状态信息
		$stateData = [
			"failed" => -1, // 失败
			"waiting" => 0, // 列队中
			"doing" => 1, // 进行中
			"ok" => 11, // 完成
		];

		$stateName = $req->input("state");

		$task = WordTask::find($id);
		$task->state = $stateData[$stateName];
		$res = $task->save();

		if ($res) {
			return $this->message("success");
		}

		return $this->failed("发生未知错误，任务状态更新失败");
	}

}