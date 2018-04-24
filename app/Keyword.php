<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Keyword extends Model {
	use SoftDeletes;
	public function ranks() {
		return $this->hasMany('App\Rank');
	}
}
