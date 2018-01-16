<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Yunwangke extends Model
{

  public function custom () {
    return $this->hasOne('App\Custom', 'id', 'customid');
  }

}
