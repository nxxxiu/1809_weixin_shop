<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Goods;
use Illuminate\Support\Str;

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
    public function goodsdetail($goods_id=0){
        $goods_id=intval($goods_id);
        if (!$goods_id){
            die('参数错误');
        }
        $data=Goods::where(['goods_id'=>$goods_id])->first();
        //浏览量
        $redis_view_key='count:view:goods_id:'.$goods_id;
        $redis_ss_view='ss:goods:view';//浏览量排名
        $view=Redis::incr($redis_view_key);//浏览量自增
        //        dd($history);
        Redis::zAdd($redis_ss_view,$view,$goods_id);//有序集合按浏览量排序
        //浏览历史
        $redis_history_key='history:view:'.Auth::id();
//        dd($redis_history_key);
        Redis::zAdd($redis_history_key,time(),$goods_id);
        $goods_ids=Redis::zRevRange($redis_history_key,0,100000000000,true);//倒序
//        dd($goods_id);
        $data1=[];
        foreach ($goods_ids as $k=>$v) {
            $where=[
                'goods_id'=>$k
            ];
            $data1[]=Goods::where($where)->first();
        }
        //计算签名
        $nonceStr=Str::random(10);
        $ticket=getJsapiTicket();
//        var_dump($ticket);
        $timestamp=time();
        $current_url=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $str="jsapi_ticket=$ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$current_url";
//        echo $str;
        $sign=sha1($str);
//        echo 'signature:'.$sign;die;
        $js_config=[
            'appId'=>env('APPID'),//公众号appid
            'timestamp'=>$timestamp,
            'nonceStr'=>$nonceStr,//随机字符串
            'signature'=>$sign,//签名
        ];
        $data2=[
            'jsconfig'=>$js_config
        ];
        $url_code="http://1809niqingxiu.comcto.com/goodsdetail?goods_id=".$goods_id;
        return view('goods.goodsdetail',['data'=>$data,'view'=>$view,'data1'=>$data1,'url_code'=>$url_code],$data2);
    }

    //商品浏览量排名
    public function getsort(){
        $key='ss:goods:view';
        $goods_id=Redis::zRangeByScore($key,0,10000,['withscores'=>true]);//正序
//        echo '<pre>';print_r($list);echo '</pre>';
//        $list1=Redis::zRevRange($key,0,10000,true);//倒序
//        echo '<pre>';print_r($list1);echo '</pre>';
        $data=[];
        foreach ($goods_id as $k=>$v) {
            $where=[
                'goods_id'=>$k
            ];
            $data[]=Goods::where($where)->first();
        }
//        dd($data);
        return view('goods.getsort',['data'=>$data]);
    }

}
