<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WechatTips;
use App\Model\Admin\Goods;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManagerStatic as Image;

class AttachmentController extends Controller
{
	const FILE_PATH = 'taoke';

	/**@var array $defaultMapping 普通商品*/
	protected $defaultMapping = array(
		'A' =>'goods_id',
		'B' =>'goods_name',
		'C' =>'goods_image',
		'D' =>'goods_url',
		'E' =>'shop_name',
		'F' =>'price',
		'G' =>'mouth_sell_number',
		'H' =>'income_ratio',
		'I' =>'commission',
		'J' =>'wang_wang',
		'K' =>'tao_short_url',
		'L' =>'tao_url',
		'M' =>'tao_password',
		'N' =>'coupon_number',
		'O' =>'coupon_surplus_number',
		'P' =>'coupon_denomination',
		'Q' =>'coupon_start_time',
		'R' =>'coupon_end_time',
		'S' =>'coupon_url',
		'T' =>'coupon_password',
		'U' =>'coupon_short_url',
		'V' =>'is_marketing_plan',
	);

	/***/
	protected $highCommissionMapping = array(
		'A' =>'goods_id',
		'B' =>'goods_name',
		'C' =>'goods_image',
		'D' =>'goods_url',
		'E' =>'shop_name',
		'F' =>'price',
		'G' =>'mouth_sell_number',
		'H' =>'income_ratio',
		'I' =>'commission',
		'J' =>'activity_status', //活动状态
		'K' =>'activity_income_ratio', //活动收入比率(%)
		'L' =>'activity_commission', //活动佣金
		'M' =>'activity_start_time',
		'N' =>'activity_end_time',
		'O' =>'wang_wang',
		'P' =>'tao_short_url',
		'Q' =>'tao_url',
		'R' =>'tao_password',
		'S' =>'coupon_number',
		'T' =>'coupon_surplus_number',
		'U' =>'coupon_denomination',
		'V' =>'coupon_start_time',
		'W' =>'coupon_end_time',
		'X' =>'coupon_url',
		'Y' =>'coupon_password',
		'Z' =>'coupon_short_url',
	);

    public function import(Request $request)
    {
	    $typeId =(int) $request->input('type_id');

	    $file = $request->file('import_goods');

	    $message = "操作失败";
	    $alertType = 'error'; //success  info warning error

		try {
			$message = "操作成功";
			$alertType = 'success';
			$this->exec($file, $typeId);
		} catch (\Exception $e) {

		}

	    return redirect()
		    ->route("voyager.goods.index")
		    ->with([
			    'message'    => $message,
			    'alert-type' => $alertType,
		    ]);

    }
    
    protected function exec(UploadedFile $file, $typeId)
    {
	    $filePath = $file->store(self::FILE_PATH);
	    $fullFilePath = storage_path('app').'/'.$filePath;
	    $objReader = \PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
	    $objPHPExcel = $objReader->load($fullFilePath); //$filename可以是上传的文件，或者是指定的文件
	    $userId = \Auth::user()->id;

	    foreach($objPHPExcel->getWorksheetIterator() as $sheet) {
		    foreach($sheet->getRowIterator() as $row)  {
			    if($row->getRowIndex()<2) {
				    continue;
			    }
				$tmp = [];
			    $type = $this->getGoodsType($sheet->getHighestColumn());
			    $func = 'format'. $type .'Goods';

			    foreach($row->getCellIterator() as $key => $cell) {
			    	$data = $cell->getValue();
					$this->$func($key, $data, $tmp);
			    }

			    $tmp['status'] = '1';
			    $tmp['user_id'] = $userId;
			    $tmp['type_id'] = $typeId;

			    if($tmp['activity_status'] == 1  && !$tmp['activity_commission']) {
				    $tmp['activity_status'] = 0;
				    $tmp['activity_start_time'] = $tmp['activity_end_time'] = $tmp['activity_commission'] = $tmp['activity_income_ratio'] = null;
			    }

			    Goods::updateOrCreate(['goods_id' => $tmp['goods_id']], $tmp);
		    }
	    }
    }

    protected function formatDefaultGoods($key, $data, &$tmp)
    {
	    //处理时间

	    if(in_array($key, ['Q',  'R'])
		    && $data ) {
		    $data = Carbon::parse($data)->format('Y-m-d H:i:s');
	    }
	    $tmp[$this->defaultMapping[$key]] = $data; //获取cell中数据
	    $tmp['activity_status'] = 0; //标记为非推广活动商品
    }

    protected function formatHighCommissionGoods($key, $data, &$tmp)
    {
	    //处理时间
	    if(in_array($key, ['V',  'W', 'M', 'N'])
		    && $data ) {
		    $data = Carbon::parse($data)->format('Y-m-d H:i:s');
	    }
	    $tmp[$this->highCommissionMapping[$key]] = $data; //获取cell中数据
	    $tmp['activity_status'] = 1; //标记为推广活动商品
    }

	/**
	 * 判读商品类型，Z是高佣活动商品， V是普通商品
	 * @param $highColunm
	 *
	 * @return string
	 */
    protected function getGoodsType($highColunm)
    {
    	return strtolower($highColunm) === 'z' ? 'highCommission' : 'default';
    }

    public function test()
    {
	   // \DB::connection()->enableQueryLog();
	   // $goodsInfo = Goods::getWechatGoodsInfos('套装');
	   // //$goodsInfo = Goods::getWechatGoodsInfos('童鞋');
	   // $queries = \DB::getQueryLog();
	   // $a = end($queries);
	   // var_dump($goodsInfo);
		//dd($queries);
	   // $tmp = str_replace('?', '"'.'%s'.'"', $a["query"]);
	   //
	   //
	   //
	   //d(vsprintf($tmp, $a['bindings']));
	   // //$path = 'http://img02.taobaocdn.com/bao/uploaded/i2/TB1UrvjLpXXXXb0XVXXXXXXXXXX_!!0-item_pic.jpg';
	    //$filename = '123.jpg';
	    //$p =  storage_path('taoke/images').'/'.$filename;
	    //$file = Image::make($path)->resize(300, 200)
		 //   ->save($p);
	    //echo base_path();
	    //$name = '天猫淘宝 优惠购';
	    //$msg = '@天猫淘宝 优惠购 找商品 机器人';
	    //$msg = wechat_at($msg, $name);
	    //var_dump($msg);
	    //$rs = \Mail::to('18676756298@163.com')->send(new WechatTips());
	    //dd($rs);
//;
//	    dd($file);
    }
	
}
