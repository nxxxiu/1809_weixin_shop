<?php

namespace App\Http\Controllers;

use App\WxUser;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    //网页授权回调
    public function callback(){
        $code=$_GET['code'];
        $access_token=json_decode(file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('APPID').'&secret='.env('APPSECRET').'&code='.$code.'&grant_type=authorization_code'),true);
//        print_r($access_token);
        //用户信息
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token['access_token'].'&openid='.$access_token['openid'].'&lang=zh_CN';
        $userInfo=json_decode(file_get_contents($url),true);
        // echo "<pre>";print_r($userInfo);echo "</pre>";
        // 用户信息入库
        $openid=$userInfo['openid'];
        $res=WxUser::where('openid',$openid)->first();
         dd($res);
        if($res){
            echo '欢迎回来:'.$res['user_name'];
        }else{
            $data=[
                'openid'=>$openid,
                'nickname'=>$userInfo['nickname'],
                'sex'=>$userInfo['sex'],
                'country'=>$userInfo['country'],
                'province'=>$userInfo['province'],
                'city'=>$userInfo['city'],
                'headimgurl'=>$userInfo['headimgurl']
            ];
            User::insert($data);
            echo '欢迎:'.$userInfo['nickname'];

        }
    }
}
