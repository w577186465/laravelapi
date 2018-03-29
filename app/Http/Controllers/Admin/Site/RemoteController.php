<?php

namespace App\Http\Controllers\Admin\Site;

use App\Friend;
use App\FriendLink;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class RemoteController extends ApiController {

	public function test(Request $req, $id) {
		$friend = Friend::find($id); // 项目信息
		$url = $friend->home_url . "/?action=code_test";
		$res = $this->request($url, "get");

		if (isset($res->code) && $res->code == 200) {
			return $this->message("success");
		}

		return $this->failed("配置不正确");
	}

	public function install(Request $req, $id) {
		if (!$req->filled(["appid", "page_url", "home_url", "server_uri", "secret"])) {
			return $this->failed("参数不正确");
		}

		$data = $req->only(["appid", "page_url", "home_url", "server_uri", "secret"]);
		$form = json_encode($data);

		// 项目信息
		$friend = Friend::find($id);
		$url = $friend->home_url . "/pmfriends/install/index.php";

		$res = $this->request($url, "post", $form);
		if (isset($res->code) && $res->code == 200) {
			return $this->message("success");
		}

		return $this->error($res);
	}

	public function config_get($id) {
		$friend = Friend::find($id); // 项目信息
		$url = $friend->home_url . "/?action=config_get";
		$res = $this->request($url, "get", "", $friend->secret);

		if (isset($res->code) && $res->code == 200) {
			return $this->success($res->data);
		} else {
			return $this->error($res);
		}

		$friend->status = 1
		$save = $friend->save();

		if ($save) {
			return $this->message("success");
		}

		return $this->failed("发生未知错误");
	}

	public function sync(Request $req) {
		if (!$req->filled(["id"])) {
			return $this->failed("参数不正确");
		}

		$id = $req->input("id");

		$friend = Friend::find($id); // 项目信息
		$url = $friend->home_url . "/?action=update_all"; // 更新地址

		$form = json_encode(["id" => $id]);

		$res = $this->request($url, "post", $form);
		if (isset($res->code) && $res->code != 200) {
			return $this->error($res);
		}

		if (FriendLink::where("friend_id", $id)->count() == 0) {
			return $this->message("success");
		}

		$res = FriendLink::where("friend_id", $id)->update(["synced" => 1]);

		if ($res) {
			return $this->message("success");
		}

		return $this->failed("发生未知错误，状态更新失败。");
	}

	public function update(Request $req) {
		if (!$req->filled(["id", "link", "name", "spider_show"])) {
			return $this->failed("参数不正确");
		}

		$data = $req->only(["id", "link", "name", "spider_show"]);
		$form = json_encode($data);

		// 项目信息
		$friend_link = FriendLink::find($data["id"]);
		$url = $friend_link->project->home_url . "/?action=update"; // 更新地址
		$secret = $friend_link->project->secret;

		// 同步站点
		$res = $this->request($url, "post", $form, $secret);
		if (isset($res->code) && $res->code != 200) {
			return $this->error($res);
		}

		// 更新本地状态
		$friend_link = FriendLink::find($data["id"]);
		$friend_link->synced = 1;
		$res = $friend_link->save();
		if ($res) {
			return $this->message("success");
		}

		return $this->failed("发生未知错误，状态更新失败。");
	}

	public function delete($id) {
		$friend = FriendLink::find($id);
		$url = $friend->project->home_url . "/?action=delete";
		$secret = $friend->project->secret;

		$form = json_encode(["id" => $id]);

		$res = $this->request($url, "post", $form, $secret);

		if (isset($res->code) && $res->code != 200) {
			return $this->error($res);
		}

		$res = $friend->delete();

		if ($res) {
			return $this->message("success");
		}

		return $this->failed("发生未知错误，删除失败。");
	}

	public function style_set(Request $req, $id) {
		if (!$req->filled(["style", "selector", "selectorPos"])) {
			return $this->failed("参数不正确");
		}

		$friend = Friend::find($id);
		$url = $friend->home_url . "/?action=config_edit";

		$form = $req->only(["style", "selector", "selectorPos"]);
		$formData = json_encode($form);

		$res = $this->request($url, "post", $formData, $friend->secret);
		if (isset($res->code) && $res->code == 200) {
			return $this->message("success");
		}

		return $this->error($res);
	}

	private function error($res) {
		if (isset($res->message) && !empty($res->message)) {
			return $this->failed($res->message);
		}

		return $this->failed("发生未知错误，操作失败。");
	}

	private function request($url, $method, $form = '', $secret = false) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		if (strtolower($method) == "post") {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $form);
		}

		$header = array(
			'Accept: application/json, text/plain, */*',
			'Content-Type: application/json; charset=utf-8',
			'Content-Length:' . strlen($form),
		);

		if ($secret) {
			$header[] = "Authorization: Bearer {$secret}";
		}

		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		$body = curl_exec($curl);
		curl_close($curl);

		return json_decode($body);
	}

}