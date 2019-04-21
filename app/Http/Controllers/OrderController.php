<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cart;
use App\Order;
use App\OrderDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    //提交订单
    public function order(){
        //购物车里的商品信息
        $cartInfo=Cart::where(['uid'=>Auth::id(),'session_id'=>Session::getId()])->get()->toArray();
//        dd($cartInfo);
        $order_amount=0;
        foreach ($cartInfo as $k=>$v){
            //计算订单金额
            $order_amount+=$v['goods_price'];
        }
        $order_sn = time().mt_rand(11111,99999).'cute';
        $orderInfo=[
            'uid'=> Auth::id(),
            'order_sn'=>$order_sn,
            'order_amount'=> $order_amount,
            'add_time'=> time()
        ];
//        dd($orderInfo);
        $order_id = Order::insertGetId($orderInfo);
        //订单详情表
        foreach($cartInfo as $k=>$v){
            $detailInfo=[
                'order_id'=> $order_id,
                'goods_id'=> $v['goods_id'],
                'goods_name'=> $v['goods_name'],
                'goods_price'=> $v['goods_price'],
                'uid'=> Auth::id()
            ];
//            dd($detailInfo);
            OrderDetail::insertGetId($detailInfo);
        }
        header('Refresh:2;url=/order/orderlist');
        echo "生成订单成功";
    }

    //订单列表
    public function orderlist(){
        $orderInfo=Order::where(['uid'=>Auth::id()])->orderBy("order_id","desc")->get()->toArray();
//        dd($orderInfo);
        $data = [
            'orderInfo'=>$orderInfo
        ];
        return view('order.orderlist',$data);
    }

    //查询订单支付状态
    public function payStatus(){
        $order_id=intval($_GET['order_id']);
        $info=Order::where(['order_id'=>$order_id])->first();
        $response=[];
        if($info){
            if($info->pay_time>0){
                $response=[
                    'status'=>1,
                    'msg'=>'ok'
                ];
            }
            echo '<pre>';print_r($info->toArray());echo '</pre>';
        }else{
            die("订单不存在");
        }
        die(json_encode($response));
    }
}
