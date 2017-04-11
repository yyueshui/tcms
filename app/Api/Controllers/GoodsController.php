<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Admin\Goods;
use Dingo\Api\Http\Response;

class GoodsController extends Controller
{
	public function search($name)
	{
		$row = Goods::getWechatGoodsInfos($name);
		return Response::create($row);
	}
}
