<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model {
	public function site() {
		return $this->hasOne('App\Site', 'id', 'site_id');
	}
}
