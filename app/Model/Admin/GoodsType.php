<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class GoodsType extends Model
{
    protected $table = 'goods_types';

    public function goods()
    {
    	return $this->hasMany('App\Model\Admin\Goods', 'type_id', 'id');
    }
}
