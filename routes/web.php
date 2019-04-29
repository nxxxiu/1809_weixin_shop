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

Route::get('/test/urlencode', function () {
    echo urlencode($_GET['url']);
});

Route::get('/home', 'HomeController@index')->name('home');

//商品
Route::get('/goodslist', 'GoodsController@goodslist');
Route::get('/goodsdetail/{goods_id?}', 'GoodsController@goodsdetail');//商品详情
Route::get('/getsort', 'GoodsController@getsort');//根据商品点击量排序

//购物车
Route::get('/cartlist', 'CartController@cartlist');
Route::get('/cart/add/{goods_id?}', 'CartController@add');//购物车添加

//订单
Route::get('/order/order', 'OrderController@order');//提交订单
Route::get('/order/orderlist', 'OrderController@orderlist');//订单列表
Route::get('/order/payStatus', 'OrderController@payStatus');      //查询订单支付状态

//计划任务
Route::get('/Crontab/delorders', 'CrontabController@delorders');//删除未支付的订单

//微信支付
Route::get('/wxpay/pay', 'WxpayController@pay');      //微信支付
Route::post('/wxpay/notify', 'WxpayController@notify');      //微信支付通知回调
Route::post('/wxpay/paySuccess', 'WxpayController@paySuccess');      //微信支付成功

//微信接口
Route::get('/weixin/valid','WeixinController@valid');//原样返回echostr 第一次get请求
Route::post('/weixin/valid','WeixinController@wxvalid');//接收微信的推送事件 post
Route::get('/weixin/callback', 'WeixinController@callback');//微信网页授权回调地址
Route::get('/weixin/create_menu', 'WeixinController@create_menu');//创建菜单 最新福利  签到

//生成带参数的二维码
Route::get('/activity/activity','ActivityController@activity');
Route::get('/activity/index','ActivityController@index');

//JS-SDK
Route::get('/jssdk/jstest','JssdkController@jstest');
Route::get('/jssdk/getimg', 'JssdkController@getimg');