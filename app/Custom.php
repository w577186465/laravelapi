<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Custom extends Model {
	public function yunwangke() {
		return $this->hasOne('App\Custom', 'id', 'customid');
	}
}
