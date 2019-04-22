<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Goods;
class GoodsController extends Controller
{
    //商品列表
    public function goodslist(){
        $model=new \App\Goods;
        $data=$model->all();
//        dd($data);
        return view('goods.goodslist',compact('data'));
    }

    //商品详情
    public function goodsdetail($id){
        $data=Goods::where(['goods_id'=>$id])->first();
        $history=Redis::incr($id);
//        dd($history);
        return view('goods.goodsdetail',['data'=>$data,'history'=>$history]);
    }
}
