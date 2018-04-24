<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Friend extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	public function site() {
		return $this->hasOne('App\Site', 'id', 'site_id');
	}
}
