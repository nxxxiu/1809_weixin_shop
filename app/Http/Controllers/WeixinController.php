<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\WxText;
use App\WxUser;
use App\Goods;
use Illuminate\Support\Facades\Redis;
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
//            dd($id);
            if ($obj->Content=='最新商品'){
                $goodsInfo=Goods::orderBy('add_time','desc')->first();
                echo '<xml>
                          <ToUserName><![CDATA['.$openid.']]></ToUserName>
                          <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                          <CreateTime>'.time().'</CreateTime>
                          <MsgType><![CDATA[news]]></MsgType>
                          <ArticleCount>1</ArticleCount>
                          <Articles>
                            <item>
                              <Title><![CDATA['.$goodsInfo->goods_name.']]></Title>
                              <Description><![CDATA['.$goodsInfo->goods_desc.']]></Description>
                              <PicUrl><![CDATA['.'http://1809niqingxiu.comcto.com/img/ok.jpg'.']]></PicUrl>
                              <Url><![CDATA['.'http://1809niqingxiu.comcto.com/goodsdetail/'.$goodsInfo->goods_id.']]></Url>
                            </item>
                          </Articles>
                        </xml>';
            }
        }elseif($type == 'event') {
            $event = $obj->Event; //事件类型
            if ($event == 'subscribe') {
                $userInfo = wxUser::where(['openid'=>$openid])->first();
                if ($userInfo) {
//                    dd('m');
                    echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wx_id.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎回来 '. $userInfo['nickname'] .']]></Content></xml>';
                } else {
//                    dd('hh');
                    $u = $this->WxUserTail($openid);
//                    dd($u);
                    //用户信息入库
                    $data=[
                        'openid'=>$u['openid'],
                        'nickname'=>$u['nickname'],
                        'sex'=>$u['sex'],
                        'city'=>$u['city'],
                        'province'=>$u['province'],
                        'country'=>$u['country'],
                        'headimgurl'=>$u['headimgurl'],
                        'subscribe_time'=>$u['subscribe_time'],
                        'subscribe_scene'=>$u['subscribe_scene']
                    ];
                    $res = wxUser::insert($data);
                    echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wx_id.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎关注 '. $u['nickname'] .']]></Content></xml>';
                }
            }
        }
    }

    /**获取微信 AccessToren */
    public function accessToken()
    {
        //先获取缓存，如果不存在请求接口
        $redis_key='wx_access_token';
        $token=Redis::get($redis_key);
        if (!$token){
            $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . env('APPID') . '&secret=' . env('APPSECRET');
//        echo $url;die;
            $json_str=file_get_contents($url);
//        print_r($json_str);die;
            $arr=json_decode($json_str,true);
//            print_r($arr);die;
            $redis_key='wx_access_token';
            Redis::set($redis_key,$arr['access_token']);
            Redis::expire($redis_key,3600);
        }
        return $token;
    }

    public function WxUserTail($openid)
    {
        $data = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $this->accessToken() . "&openid=" . $openid . "&lang=zh_CN");
//        print_r("https://api.weixin.qq.com/cgi-bin/user/info?accessToken=" . $this->accessToken() . "&openid=" . $openid . "&lang=zh_CN");die;
        $arr = json_decode($data, true);
        return $arr;
    }

    //微信网页授权回调地址
    public function callback(){
        var_dump($_GET['code']);
    }
}
