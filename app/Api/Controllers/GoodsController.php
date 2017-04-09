<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Admin\Goods;
use Dingo\Api\Http\Response;

class GoodsController extends Controller
{
	public function search($name)
	{
		$row = Goods::where('goods_name', 'like', '%' . $name . '%')
			->orderBy('commission', 'DESC')
			->first(['goods_name', 'goods_image', 'tao_short_url', 'tao_password', 'coupon_denomination', 'coupon_password', 'coupon_short_url', 'mouth_sell_number']); //todo 计算偏移，考虑用户多次搜索情况，避免每次都返回一样的数据
		return Response::create($row);
	}
}
