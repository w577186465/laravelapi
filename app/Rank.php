<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
  public function keyowrds()
  {
    return $this->belongsTo('App\Keyword');
  }
}
