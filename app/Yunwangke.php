<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Yunwangke extends Model {

	public function custom() {
		return $this->hasOne('App\Custom', 'id', 'customid');
	}

	public function keywords() {
		return $this->hasMany('App\Keyword', 'parent');
	}

}
