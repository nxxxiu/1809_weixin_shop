<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use App\WxText;
use App\WxUser;
use App\Goods;
use App\Activity;
use App\Signin;
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
                              <PicUrl><![CDATA['.'http://www.cccute.com/img/ok.jpg'.']]></PicUrl>
                              <Url><![CDATA['.'http://www.cccute.com/goodsdetail/'.$goodsInfo->goods_id.']]></Url>
                            </item>
                          </Articles>
                        </xml>';
            }else{
                $goods=$this->seachgoods($obj);
                echo $goods;
            }
        }else if($type='event'){
            $event=$obj->Event;
            switch($event){
                case 'SCAN':
                    if(isset($obj->EventKey)){
                        $this->qrcode($obj);//扫带参数二维码
                    }
                    break;
                case 'subscribe':
                    $this->subscribe($obj);//扫码关注
                    break;
                default:
                    $response_xml = 'success';
            }
            echo $response_xml;
        }
    }

    //搜索商品数据
    public function seachgoods($obj){
        $openid=$obj->FromUserName;
        $wx_id=$obj->ToUserName;
        $where=[
            'goods_name'=>$obj->Content
        ];
        $goodsInfo=Goods::where($where)->first();
        if ($goodsInfo){
            //有 推送商品
            $goods= '<xml>
                      <ToUserName><![CDATA['.$openid.']]></ToUserName>
                      <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                      <CreateTime>'.time().'</CreateTime>
                      <MsgType><![CDATA[news]]></MsgType>
                      <ArticleCount>1</ArticleCount>
                      <Articles>
                        <item>
                          <Title><![CDATA['.$goodsInfo->goods_name.']]></Title>
                          <Description><![CDATA['.$goodsInfo->goods_desc.']]></Description>
                          <PicUrl><![CDATA[http://www.cccute.com/img/'.$goodsInfo->goods_img.']]></PicUrl>
                          <Url><![CDATA[http://www.cccute.com/goodsdetail/'.$goodsInfo->goods_id.']]></Url>
                        </item>
                      </Articles>
                    </xml>';
        }else{
            $data=Goods::get()->toArray();
            $num=array_rand($data,1);
            $goods='<xml>
                      <ToUserName><![CDATA['.$openid.']]></ToUserName>
                      <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                      <CreateTime>'.time().'</CreateTime>
                      <MsgType><![CDATA[news]]></MsgType>
                      <ArticleCount>1</ArticleCount>
                      <Articles>
                        <item>
                          <Title><![CDATA['.$data[$num]['goods_name'].']]></Title>
                          <Description><![CDATA['.$data[$num]['goods_desc'].']]></Description>
                          <PicUrl><![CDATA[http://www.cccute.com/img/'.$data[$num]['goods_img'].']]></PicUrl>
                          <Url><![CDATA[http://www.cccute.com/goodsdetail/'.$data[$num]['goods_id'].']]></Url>
                        </item>
                      </Articles>
                    </xml>';
        }
        return $goods;
    }

    //扫描带参数二维码
    public function qrcode($obj){
        $wx_id=$obj->ToUserName;
        $openid=$obj->FromUserName;
        $EventKey=$obj->EventKey;
        //验证用户是否存在
        $res=WxUser::where(['openid'=>$openid,'event_key'=>$EventKey])->first();
        if($res){
            $response_xml= '<xml>
                      <ToUserName><![CDATA['.$openid.']]></ToUserName>
                      <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                      <CreateTime>'.time().'</CreateTime>
                      <MsgType><![CDATA[news]]></MsgType>
                      <ArticleCount>1</ArticleCount>
                      <Articles>
                        <item>
                          <Title><![CDATA[最新活动]]></Title>
                          <Description><![CDATA[come]]></Description>
                          <PicUrl><![CDATA['.'http://www.cccute.com/img/ok.jpg'.']]></PicUrl>
                          <Url><![CDATA['.'http://www.cccute.com/activity/index'.']]></Url>
                        </item>
                      </Articles>
                    </xml>';
        }else{
            $response_xml= '<xml>
                      <ToUserName><![CDATA['.$openid.']]></ToUserName>
                      <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                      <CreateTime>'.time().'</CreateTime>
                      <MsgType><![CDATA[news]]></MsgType>
                      <ArticleCount>1</ArticleCount>
                      <Articles>
                        <item>
                          <Title><![CDATA[最新活动]]></Title>
                          <Description><![CDATA[come]]></Description>
                          <PicUrl><![CDATA['.'http://www.cccute.com/img/ok.jpg'.']]></PicUrl>
                          <Url><![CDATA['.'http://www.cccute.com/activity/index'.']]></Url>
                        </item>
                      </Articles>
                    </xml>';
            $data=[
                'openid'=>$openid,
                'event_key'=>$EventKey,
                'create_time'=>$obj->CreateTime
            ];
            $res1=Activity::insert($data);
//            dd($res1);
        }
        die($response_xml);
    }

    //扫码关注
    public function subscribe($obj){
        $wx_id=$obj->ToUserName;
        $openid=$obj->FromUserName;
        $userInfo=wxUser::where('openid',$openid)->first();
        if ($userInfo) {
//                    dd('m');
            $response_xml= '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wx_id.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎回来 '. $userInfo['nickname'] .']]></Content></xml>';
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
            $response_xml= '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wx_id.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎关注 '. $u['nickname'] .']]></Content></xml>';
        }
        die($response_xml);
    }

    //查询用户信息
    public function WxUserTail($openid)
    {
        $data = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . getWxAccessToken() . "&openid=" . $openid . "&lang=zh_CN");
//        print_r("https://api.weixin.qq.com/cgi-bin/user/info?accessToken=" . $this->accessToken() . "&openid=" . $openid . "&lang=zh_CN");die;
        $arr = json_decode($data, true);
        return $arr;
    }

    //创建菜单 福利 签到
    public function create_menu(){
        $redirect_url=urlencode('http://www.cccute.com/exam/callback');
//        dd($redirect_url);
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('APPID').'&redirect_uri='.$redirect_url.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
//        dd($url);
        $redirect_signin=urlencode('http://www.cccute.com/weixin/signin');
        $url_signin='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('APPID').'&redirect_uri='.$redirect_signin.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        $arr=[
            'button'=>[
                [
                    'type'=>'view',
                    'name'=>'最新福利',
                    'url'=> $url,
                ],
                [
                    'type'=>'view',
                    'name'=>'签到',
                    'url'=>$url_signin,
                ]
            ]
        ];
        $str=json_encode($arr,JSON_UNESCAPED_UNICODE);
        $client=new Client();
        $response=$client->request('POST','https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.getWxAccessToken(),[
            'body'=>$str
        ]);
        $res=json_decode($response->getBody(),true);
//        dd($res);
        if($res['errcode']>0){
            echo "创建菜单失败";
        }else{
            echo "创建菜单成功";
        }
    }

    //最新福利回调
    public function callback(){
        $code=$_GET['code'];
        $access_token=json_decode(file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('APPID').'&secret='.env('APPSECRET').'&code='.$code.'&grant_type=authorization_code'),true);
//        print_r($access_token);
        //用户信息
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token['access_token'].'&openid='.$access_token['openid'].'&lang=zh_CN';
//        dd($url);
        $userInfo=json_decode(file_get_contents($url),true);
//        print_r($userInfo);
        echo '<h1>欢迎:'.$userInfo['nickname'].'，正在跳转福利页面！</h1>';
        header('Refresh:3;url=http://www.cccute.com/goodsdetail/6');
    }

    //签到回调
    public function signin(){
        $code=$_GET['code'];
        $access_token=json_decode(file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('APPID').'&secret='.env('APPSECRET').'&code='.$code.'&grant_type=authorization_code'),true);
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token['access_token'].'&openid='.$access_token['openid'].'&lang=zh_CN';
//        dd($url);
        $open_id=$access_token['openid'];
        $userInfo=json_decode(file_get_contents($url),true);
        $res=Signin::where(['open_id'=>$access_token])->first();
        if ($res){
            echo "签到成功";
        }else{
            Signin::insert(['open_id'=>$open_id]);
            echo "欢迎:".$userInfo['nickname'].'首次签到';
        }
        $signin_key='signin:key:'.$userInfo['openid'];
        $num=Redis::incr($signin_key);
        $time_key='time:'.$userInfo['openid'];
        $date=date('Y-m-d H:i:s');
        $time=Redis::zAdd($time_key,time(),$date);
        $date_time=Redis::zRevRange($time_key,0,10000000000);
        return view('weixin.signin',['num'=>$num,'date_time'=>$date_time]);
    }
}
