<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	public function friend() {
		return $this->hasMany('App\Friend');
	}
}
