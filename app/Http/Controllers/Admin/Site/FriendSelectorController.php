<?php

namespace App\Http\Controllers\Admin\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FriendSelectorController extends Controller {

	public function index(Request $req) {
		if (!$req->filled('uri')) {
			return '参数不正确';
		}

		$uri = $req->input('uri');
		$handle = fopen($uri, "rb");
		$contents = "";
		while (!feof($handle)) {
			$contents .= fread($handle, 8192);
		}

		$contents = str_replace("<head>", "<head>\n<base href=\"http://www.dloushang.com/\" />", $contents);

		fclose($handle);
		echo view('friend-selector');
		return $contents;
	}

}