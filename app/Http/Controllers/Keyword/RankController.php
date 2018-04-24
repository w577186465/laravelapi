<?php

namespace App\Http\Controllers\Keyword;

use App\Http\Controllers\ApiController;
use App\Rank;

// 模型

class RankController extends ApiController {

	public function del($id) {
		$res = Rank::destroy($id);
		if ($res) {
			return $this->message('success');
		}

		return $this->failed('发生未知错误，操作失败。');
	}

	// 关键词排名情况
	public function ranks($id) {
		$ranks = Rank::where('keyword_id', $id)->get();
		return $this->success($ranks);
	}

}