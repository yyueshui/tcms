<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class GoodsType extends Model
{
    protected $table = 'goods_types';

    public function goods()
    {
    	return $this->belongsToMany('App\Model\Admin\Goods');
    }

    /*public function getStatusAttribute()
    {
    	return isset($this->attributes['status'])
		    ? ($this->attributes['status'] == 1 ? '可用' : '禁用')
		    : 1
		;
    }*/
}
