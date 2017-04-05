<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    public function goodsType()
    {
    	return $this->belongsTo('App\Model\Admin\GoodsType', 'id', 'type_id');
    }
}
