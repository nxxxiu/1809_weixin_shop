<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use App\WxText;
use App\WxUser;
use App\Goods;
use Illuminate\Support\Facades\Redis;
class WeixinController extends Controller
{
    //第一次调用接口
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
                    $res = WxUser::insert($data);
                    echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wx_id.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎关注 '. $u['nickname'] .']]></Content></xml>';
                }
            }
        }elseif (isset($obj->EventKey)){
            if ($obj->EventKey==666){
                echo '<xml>
                  <ToUserName><![CDATA['.$openid.']]></ToUserName>
                      <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                      <CreateTime>'.time().'</CreateTime>
                      <MsgType><![CDATA[news]]></MsgType>
                      <ArticleCount>1</ArticleCount>
                      <Articles>
                        <item>
                          <Title><![CDATA[最新活动]]></Title>
                          <Description><![CDATA[description1]]></Description>
                          <PicUrl><![CDATA['.'http://1809niqingxiu.comcto.com/img/okk.jpg'.']]></PicUrl>
                          <Url><![CDATA['.'http://1809niqingxiu.comcto.com/activity/index'.']]></Url>
                        </item>
                      </Articles>
                </xml>';
            }
        }
    }

    //微信网页授权回调地址
    public function callback(){
        $code=$_GET['code'];
        $access_token=json_decode(file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx11c143836e27ac69&secret=f13ee305431b7450e43a982f3263968e&code=".$code."&grant_type=authorization_code"),true);
//        print_r($access_token);
        //用户信息
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token['access_token'].'&openid='.$access_token['openid'].'&lang=zh_CN';
        $userInfo=json_decode(file_get_contents($url),true);
//        print_r($userInfo);
        // 用户信息入库
        $openid=$userInfo['openid'];
//        dd($openid);
        $res=WxUser::where('openid',$openid)->first();
//        dd($res);
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
//            dd($data);
//            DB::table('wx_user')->insertGetId($data);
            WxUser::insert($data);
            echo '欢迎:'.$userInfo['nickname'].'关注';
        }
    }

}
