<?php

namespace App\Http\Controllers\Admin\Task;

use App\Http\Controllers\ApiController;
use App\Http\Resources\RankqueryGettask;
use App\WordTask;

// 模型
use App\Yunwangke;
use Illuminate\Http\Request;

class WordTaskController extends ApiController {

	// 添加数据
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
		if ($req->filled('site')) {
			$model->site = implode(",", $req->input("site"));
		}

		// 查询范围
		if ($req->filled('range')) {
			$model->query_range = $req->input('range');
		}

		// 重点词
		if ($req->filled('important')) {

		}

		$res = $model->save();

		if ($res) {
			return $this->message("保存成功");
		}

		return $this->failed("保存失败");

		// if ($model->id) {
		// 	$taskurl = sprintf("%s/addtask?id=%u", env('CONSOLE_PATH', 'http://127.0.0.1:3000'), $model->id); // 任务提交地址

		// 	$ch = curl_init();

		// 	// 设置选项，包括URL
		// 	curl_setopt($ch, CURLOPT_URL, $taskurl);
		// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// 	curl_setopt($ch, CURLOPT_HEADER, 0);

		// 	// 执行并获取HTML文档内容
		// 	$output = curl_exec($ch);

		// 	//释放curl句柄
		// 	curl_close($ch);
		// 	$res = json_decode($output);

		// 	if (isset($res->code) && $res->code == 1) {
		// 		return $this->success('添加成功');
		// 	}
		// }

		return $this->failed('添加失败');
	}

	public function delete($id) {
		$partner = WordTask::destroy($id);
		print_r($partner);
	}

	public function list(Request $req) {
		$pagesize = $req->input('pagesize', 10);

		$data = WordTask::orderBy('id', 'desc')->paginate($pagesize);
		return $this->success($data);
	}

	// 获取进行中的列队
	public function rank() {
		$tasks = WordTask::select('id', 'task_type')->where('state', '0')->orWhere('state', '1')->get();
		return $this->success($tasks);
	}

	public function get_task($id) {
		$task = WordTask::find($id);
		if ($task->task_type == 'ywk') {
			$data = new RankqueryGettask(Yunwangke::find($task->pid));
			$data->site = $task->site;
			$data->task_type = $task->task_type;
			return $this->success($data);
		}
	}

}