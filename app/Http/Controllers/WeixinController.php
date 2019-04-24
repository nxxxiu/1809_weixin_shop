<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class WeixinController extends Controller
{
    public function valid()
    {
        echo $_GET['echostr'];
    }

    //接收推送消息
    public function wxvalid(){
        $client=new Client();
        $data = file_get_contents("php://input");
//        print_r($data);die;
        $time=date('Y-m-d H:i:s');
        $str=$time.$data."\n";
        is_dir('logs') or mkdir('logs',0777,true);
        file_put_contents("logs/1809_wx_valid.log",$str,FILE_APPEND);
        $obj=simplexml_load_string($data);
//        dd($obj);
        $wx_id = $obj->ToUserName;  //开发者微信号
//        print_r($wx_id);die;
        $openid = $obj->FromUserName; //用户的openid
//        print_r($openid);die;
        $type = $obj->MsgType;
        //消息类型
        if($type=='text') {//文本
            $font = $obj->Content;
            $time = $obj->CreateTime;
            $info = [
                'type' => 'text',
                'openid' => $openid,
                'create_time' => $time,
                'font' => $font
            ];
            $id = WxText::insertGetId($info);
            dd($id);
        }
    }
}
