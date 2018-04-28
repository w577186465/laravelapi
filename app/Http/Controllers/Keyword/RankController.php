<?php

namespace App\Http\Controllers\Keyword;

use App\Http\Controllers\ApiController;
use App\Keyword;
use App\Rank;
use Illuminate\Http\Request;

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
		$ranks = Rank::where('keyword_id', $id)->orderBy('rank', 'asc')->get();
		return $this->success($ranks);
	}

	// 保存排名
	public function save_rank(Request $req) {
		if (!$req->filled(["id", "site", "rank"])) {
			return $this->failed("参数不正确");
		}

		$data = $req->only(["id", "site", "rank"]);

		// 获取原始排名信息
		$rankData = $data["rank"]; // 排名信息
		$keyword = Keyword::find($data["id"]); // 获取关键词信息
		$oldRank = Rank::where("keyword_id", $data["id"])->where("site", $data["site"])->orderBy("rank", "asc")->get(); // 获取原始排名信息

		$minRank = 0;
		if (!empty($oldRank)) {
			$minRank = $oldRank[0]->rank;
		}

		if (empty($rankData)) {
			return $this->failed("排名数据不能为空");
		}

		foreach ($rankData as $key => $rank) {
			$hash = md5($data["id"] . $rank["Url"]); // 生成排名标识
			$getrank = Rank::where("hash", $hash)->first(); // 旧的排名信息

			// 保存排名信息
			if ($getrank) {
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
		if ($minRank > $rankData[0]["Rank"] || $minRank != 0) {
			$columnName = "first_rank_" . $data["site"]; // 获取索引列名
			$kwmodel = Keyword::find($data["id"]);
			$kwmodel->$columnName = $rankData[0]["Rank"];
			$kwmodel->save();
		}

		return $this->message("message");
	}

}