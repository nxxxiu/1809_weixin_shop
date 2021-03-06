<?php

namespace App\Http\Controllers;

use App\WxUser;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

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
//         dd($res);
        if($res){
            echo '欢迎回来:'.$res['nickname'];
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
            WxUser::insert($data);
            echo '欢迎:'.$userInfo['nickname'];

        }
    }

    //创建标签
    public function tag(){
        $name="enen";
        $url='https://api.weixin.qq.com/cgi-bin/tags/create?access_token='.getWxAccessToken();
        $a=[
            "tag" =>["name"=>$name ]
        ];
        $data=json_encode($a,JSON_UNESCAPED_UNICODE);
        echo $data;die;
        $client=new Client();
        $response=$client->request('post',$url,[
            'body'=>$data
        ]);
        $res=$response->getBody();
        $arr=json_decode($res,true);
        echo'<pre>';print_r($arr);echo'</pre>';
    }
}
