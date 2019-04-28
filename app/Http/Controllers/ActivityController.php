<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    //获取带参数的二维码
    public function activity(){
        $client=new Client();
        $arr=[
            'expire_seconds'=>604800,
            'action_name'=>'QR_SCENE',
            'action_info'=> [
                'scene'=>[
                    'scene_id'=>'666'
                ]
            ]
        ];
        $str=json_encode($arr,JSON_UNESCAPED_UNICODE);
//        dd($str);
        $url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.getWxAccessToken();
        $response=$client->request('POST',$url,[
            'body'=>$str
        ]);
        $res=json_decode($response->getBody(),true);
        $ticket=urlencode($res['ticket']);
//        dd($ticket);
        $code='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
        return redirect($code);
    }

    public function index(){
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
        $data=[
            'jsconfig'=>$js_config
        ];
        return view('activity.index',$data);
    }
}
