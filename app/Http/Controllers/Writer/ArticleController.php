<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\ApiController;
use App\WriterArticle;
use Illuminate\Http\Request;

class ArticleController extends ApiController {

	public function add(Request $req) {
		if (!$req->filled(["title", "content", "keywords", "parent"])) {
			return $this->failed("参数不正确");
		}

		$data = $req->only("title", "content", "keywords", "parent", "input_at");

		$article = new WriterArticle;
		foreach ($data as $key => $value) {
			$article->$key = $value;
		}
		$res = $article->save();
		if ($res) {
			return $this->message("success");
		}

		return $this->failed("发生未知错误，添加失败。");
	}

	public function list(Request $req, $parent) {
		$pagesize = $req->input("pagesize", 10);
		$data = WriterArticle::where("parent", $parent)->paginate($pagesize);
		return $this->success($data);
	}

}