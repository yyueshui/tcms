<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    //添加自定义admin路由
	$namespacePrefix = '\\App\\Http\\Controllers\\Admin\\';
	Route::group([
		'as'     => 'goods.',
		'prefix' => 'goods',
	], function () use ($namespacePrefix) {
		Route::get('/', ['uses' => $namespacePrefix.'GoodsController@index', 'as' => 'index']);
		Route::get('builder', ['uses' => $namespacePrefix.'GoodsController@builder', 'as' => 'builder']);
		Route::post('order', ['uses' => $namespacePrefix.'GoodsController@orderItem', 'as' => 'order']);
		Route::delete('/delete/{id}', ['uses' => $namespacePrefix.'GoodsController@delete', 'as' => 'destroy']);
		Route::post('/add', ['uses' => $namespacePrefix.'GoodsController@add', 'as' => 'add']);
		Route::put('/update', ['uses' => $namespacePrefix.'GoodsController@update', 'as' => 'update']);
	});

	Route::group([
		'as'     => 'goods.type.',
		'prefix' => 'goods/type',
	], function () use ($namespacePrefix) {
		Route::get('/', ['uses' => $namespacePrefix.'GoodsTypeController@index', 'as' => 'index']);
		Route::get('builder', ['uses' => $namespacePrefix.'GoodsTypeController@builder', 'as' => 'builder']);
		Route::post('order', ['uses' => $namespacePrefix.'GoodsTypeController@orderItem', 'as' => 'order']);
		Route::delete('/delete/{id}', ['uses' => $namespacePrefix.'GoodsTypeController@delete', 'as' => 'destroy']);
		Route::post('/add', ['uses' => $namespacePrefix.'GoodsTypeController@add', 'as' => 'add']);
		Route::put('/update', ['uses' => $namespacePrefix.'GoodsTypeController@update', 'as' => 'update']);
	});
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
