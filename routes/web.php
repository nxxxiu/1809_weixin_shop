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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//商品
Route::get('/goodslist', 'GoodsController@goodslist');
Route::get('/goodsdetail/{id}', 'GoodsController@goodsdetail');

//购物车
Route::get('/cartlist', 'CartController@cartlist');
Route::get('/cart/add/{goods_id?}', 'CartController@add');//购物车添加

//订单
Route::get('/order/order', 'OrderController@order');//提交订单
Route::get('/order/orderlist', 'OrderController@orderlist');//订单列表
Route::get('/order/payStatus', 'OrderController@payStatus');      //查询订单支付状态

//微信支付
Route::get('/wxpay/pay', 'WxpayController@pay');      //微信支付
Route::post('/wxpay/notify', 'WxpayController@notify');      //微信支付通知回调
Route::post('/wxpay/paySuccess', 'WxpayController@paySuccess');      //微信支付成功