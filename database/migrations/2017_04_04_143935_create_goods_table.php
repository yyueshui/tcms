<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');
	        $table->tinyInteger('type', false, true)->comment('商品类型')->nullable(); //用于区分推广位
	        $table->bigInteger('goods_id', false, true)->comment('商品ID');
	        $table->string('goods_name', 255)->comment('商品名称');
	        $table->string('goods_image', 255)->comment('商品主图');
	        $table->string('goods_url', 255)->comment('商品链接');
	        $table->string('shop_name', 50)->comment('店铺名称')->nullable();
	        $table->decimal('price', 20, 3)->comment('商品价格(单位：元)');
	        $table->integer('mouth_sell_number', false, true)->comment('商品月销量')->nullable();
	        $table->decimal('income_ratio')->comment('收入比率(%)')->nullable();
	        $table->decimal('commission')->comment('佣金')->nullable();
	        $table->string('wang_wang', 50)->comment('卖家旺旺')->nullable();
	        $table->string('tao_short_url', 50)->comment('淘宝客短链接(300天内有效)');
	        $table->string('tao_url', 255)->comment('商品链接');
	        $table->string('tao_password', 50)->comment('淘口令(300天内有效)');
	        $table->integer('coupon_number', false, true)->comment('优惠券总量')->nullable();
	        $table->integer('coupon_surplus_number', false, true)->comment('优惠券剩余量')->nullable();
	        $table->decimal('coupon_denomination')->comment('优惠券面额')->nullable();
	        $table->integer('coupon_start_time')->comment('优惠券开始时间')->nullable();
	        $table->integer('coupon_end_time')->comment('优惠券结束时间')->nullable();
	        $table->string('coupon_url', 255)->comment('优惠券链接')->nullable();
	        $table->string('coupon_password', 50)->comment('优惠券淘口令(300天内有效)')->nullable();
	        $table->string('coupon_short_url', 50)->comment('优惠券短链接(300天内有效)')->nullable();
	        $table->tinyInteger('is_marketing_plan', false, true)->comment('是否为营销计划商品')->nullable();
	        $table->enum('status', [0, 1, 2])->comment('商品状态 0: 不可用， 1：可用商品，2：手动禁用商品')->default(1);
	        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods');
    }
}
