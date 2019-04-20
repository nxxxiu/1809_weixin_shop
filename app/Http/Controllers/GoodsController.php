<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GoodsController extends Controller
{
    //商品列表
    public function goodslist(){
        $model=new \App\Goods;
        $data=$model->all();
//        dd($data);
        return view('goods.goodslist',compact('data'));
    }
}
