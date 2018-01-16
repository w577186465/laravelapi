<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
  public function ranks () {
    return $this->hasMany('App\Rank');
  }
}
