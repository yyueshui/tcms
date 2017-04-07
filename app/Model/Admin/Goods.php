<?php

namespace App\Model\Admin;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
	protected $table = 'goods';

	protected $fillable = ['goods_id', 'goods_name', 'goods_image', 'goods_url', 'shop_name', 'price', 'mouth_sell_number', 'income_ratio', 'commission', 'wang_wang', 'tao_short_url', 'tao_url', 'tao_password', 'coupon_number', 'coupon_surplus_number', 'coupon_denomination', 'coupon_start_time', 'coupon_end_time', 'coupon_url', 'coupon_password', 'coupon_short_url', 'is_marketing_plan', 'status', 'user_id', 'type_id'];


	public function typeId()
    {
    	return $this->belongsTo('App\Model\Admin\GoodsType', 'type_id');
    }

    public function setCouponStartTimeAttribute($value)
    {
	    $this->attributes['coupon_start_time'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function setCouponEndTimeAttribute($value)
    {
	    $this->attributes['coupon_end_time'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

	/*public function getStatusAttribute()
	{
		return isset($this->attributes['status'])
			? ($this->attributes['status'] == 1 ? '可用' : '禁用')
			: 1
			;
	}

	public function setStatusAttribute($value)
	{
		return $this->attributes['status'];
	}*/
}
