<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
class ActivityController extends Controller
{
    //获取带参数的二维码
    public function activity(){
        $client=new Client();
        $arr=[
            'expire_seconds'=>604800,
            'action_name'=>'QR_SCENE',
            'action_info'=> [
                'action_info'=>[
                    'scene'=>[
                        'scene_id'=>'666'
                    ]
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
        return view('activity.index');
    }
}
