<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Yunwangke extends Model {
	use SoftDeletes;

	public function custom() {
		return $this->hasOne('App\Custom', 'id', 'customid');
	}

	public function keywords() {
		return $this->hasMany('App\Keyword', 'parent');
	}

}
