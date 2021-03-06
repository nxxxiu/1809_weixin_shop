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
    public function cartlist(){
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
            return view('cart.cartlist',$data);
        }else{
            header('Refresh:2;url=/goodslist');
            die("购物车为空");
        }
    }

    //添加购物车
    public function add($goods_id){
        if (empty($goods_id)){
            header('Refresh:3;url=/cart');
            die("请选择商品");
        }
//        echo 'goods_id:'.$goods_id;
        //判断商品是否有效
        $goodsInfo=Goods::where(['goods_id'=>$goods_id])->first();
//        dd($goodsInfo);
        if ($goodsInfo){
            //商品状态为2 已经被删除
            if ($goodsInfo->goods_status==2){
                header('Refresh:2;url=/cartlist');
                echo "商品不存在";die;
            }
            //添加到购物车
            $cartInfo=[
                'goods_id'=>$goods_id,
                'goods_name'=>$goodsInfo->goods_name,
                'goods_price'=>$goodsInfo->goods_price,
                'uid'=>Auth::id(),
                'session_id'=>Session::getId()
            ];
//            dd($cartInfo);
            //入库
            $res=Cart::insertGetId($cartInfo);
//            dd($res);
            if ($res){
                header('Refresh:2;url=/cartlist');
                die("添加购物车成功");
            }else{
                header('Refresh:2;url=/cartlist');
                die("添加购物车失败");
            }
        }else{
            echo "商品不存在";
        }
    }
}
