<?php

namespace App\Http\Controllers;
use App\Cart;
use App\Goods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
class CartController extends Controller
{
    //购物车列表页面
    public function index(){
        $cart_list=Cart::where(['uid'=>Auth::id(),'session_id'=>Session::getId()])->get()->toArray();
//        dd($cart_list);
        if ($cart_list){
            $goods_amount=0;
            foreach ($cart_list as $k=>$v){
                $goods=Goods::where(['goods_id'=>$v['goods_id']])->first()->toArray();
//                print_r($goods);die;
                $goods_amount+=$goods['goods_price'];
                $goods_list[]=$goods;
            }
            //展示购物车
            $data=[
                'goods_list'=>$goods_list,
                'goods_amount'=>$goods_amount/100
            ];
//            dd($data);
            return view('cart.index',$data);
        }else{
            header('Refresh:2;url=/');
            die("购物车为空,2秒后跳转至首页");
        }
    }

    //添加购物车
    public function add($goods_id){
        if (empty($goods_id)){
            header('Refresh:3;url=/cart');
            die("请选择商品，3秒后自动跳转至购物车");
        }
//        echo 'goods_id:'.$goods_id;
        //判断商品是否有效
        $goodsInfo=Goods::where(['goods_id'=>$goods_id])->first();
//        dd($goodsInfo);
        if ($goodsInfo){
            //商品状态为2 已经被删除
            if ($goodsInfo->goods_status==2){
                header('Refresh:2;url=/');
                echo "商品不存在，2秒后自动跳到首页";die;
            }
            //添加到购物车
            $cartInfo=[
                'goods_id'=>$goods_id,
                'goods_name'=>$goodsInfo->goods_name,
                'uid'=>Auth::id(),
                'session_id'=>Session::getId()
            ];
//            dd($cartInfo);
            //入库
            $res=Cart::insertGetId($cartInfo);
//            dd($res);
            if ($res){
                header('Refresh:2;url=/');
                die("添加购物车失败");
            }
        }else{
            echo "商品不存在";
        }
    }
}
