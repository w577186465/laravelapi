<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RankQuery extends Model
{
  public function getSiteAttribute($value)
  {
    $data = [];
    $data['bd'] = '百度';
    return $data[$value];
  }

  public function getTaskTypeAttribute($value)
  {
    $data = [];
    $data['ywk'] = '云网客';
    return $data[$value];
  }
}
