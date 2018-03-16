<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FriendLink extends Model {
	public function project() {
		return $this->hasOne('App\Friend', 'id', 'friend_id');
	}
}
