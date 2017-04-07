<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Admin\Goods;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class AttachmentController extends Controller
{
	const FILE_PATH = 'taoke';

	protected $mapping = array(
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

    public function import(Request $request)
    {
	    $typeId =(int) $request->input('type_id');

	    $file = $request->file('import_goods');
	    $this->exec($file, $typeId);

	    return redirect()
		    ->route("voyager.goods.index")
		    ->with([
			    'message'    => "操作成功",
			    'alert-type' => 'success',
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
			    foreach($row->getCellIterator() as $key => $cell) {
			    	$data = $cell->getValue();
			    	if(($key == 'Q'|| $key == 'R')
				        && $data ) {
					    $data = Carbon::parse($data)->format('Y-m-d H:i:s');
				    }
				    $tmp[$this->mapping[$key]] = $data; //获取cell中数据
			    }
			    $tmp['status'] = '1';
			    $tmp['user_id'] = $userId;
			    $tmp['type_id'] = $typeId;
			    Goods::updateOrCreate(['goods_id' => $tmp['goods_id']], $tmp);

		    }
	    }

    }
	
}
