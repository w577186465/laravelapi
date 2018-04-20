<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WordTask extends Model {

	public function keywords() {
		return $this->hasMany('App\Keyword', 'parent', 'pid');
	}

	public function getSiteAttribute($value) {
		return explode(",", $value);
	}

	public function getTaskTypeAttribute($value) {
		return explode(",", $value);
	}

}
