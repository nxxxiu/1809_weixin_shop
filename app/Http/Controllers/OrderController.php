<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    //生成订单
    public function order(){
        //购物车里的商品信息
        $cartInfo=Cart::where(['uid'=>Auth::id(),'session_id'=>Session::getId()])->get()->toArray();
//        dd($cartInfo);

    }
}
