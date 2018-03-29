<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\ApiController;
use App\Http\Resources\RankqueryGettask;
use App\WordTask;
use App\Yunwangke;
use Illuminate\Http\Request;

class WordTaskController extends ApiController {

	public function rank(Request $req) {
		if (!$req->filled("task_type")) {
			return $this->failed("参数错误");
		}

		$type = $req->input("task_type");
		$data = WordTask::select('id', 'name')->where("task_type", $type)->where('state', '0')->orWhere('state', '1')->get();
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

}