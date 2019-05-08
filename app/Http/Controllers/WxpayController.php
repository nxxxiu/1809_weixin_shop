<?php

namespace App\Http\Controllers;
use App\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WXBizDataCryptController;

class WxpayController extends Controller
{

    public $wx_unifiedorder_url="https://api.mch.weixin.qq.com/pay/unifiedorder";   //统一下单接口
    public $notify_url='http://weixin.cccute.top/wxpay/notify';         //支付回调
    //微信支付测试
    public function pay(){
//        echo Str::random(16);die;
        $order_id = intval($_GET['order_id']);    // 订单id
        $orderInfo = Order::where(['order_id'=>$order_id])->first();
        if(!$orderInfo){
            die("订单不存在");
        }
        $total_fee=1;   //用户要支付的金额
        $order_sn=time().mt_rand(11111,99999);//随机生成订单号
        $order_info=[
            'appid'=>env('WEIXIN_APPID_0'),//微信支付绑定的服务号APPID
            'mch_id'=>env('WEIXIN_MCH_ID'),//商户ID
            'nonce_str'=>Str::random(16),//随机字符串
            'sign_type'=>'MD5',//签名加密方式
            'body'=>'测试订单-'.mt_rand(1111,9999).Str::random(6),
            'out_trade_no'=>$orderInfo->order_sn,//本地订单号
            'total_fee'=>$total_fee,//付款金额
            'spbill_create_ip'=>$_SERVER['REMOTE_ADDR'],//客户端IP
            'notify_url'=>$this->notify_url,//回调地址
            'trade_type'=>'NATIVE',//交易类型
        ];
        $this->values=[];
        $this->values=$order_info;
        $this->SetSign();
        $xml=$this->ToXml();
        $rs=$this->postXmlCurl($xml,$this->wx_unifiedorder_url,$useCert=false,$second=30);
        $data=simplexml_load_string($rs);
//      var_dump($data);echo '<hr>';
//      echo 'return_code: '.$data->return_code;echo '<br>';
//		echo 'return_msg: '.$data->return_msg;echo '<br>';
//		echo 'appid: '.$data->appid;echo '<br>';
//		echo 'mch_id: '.$data->mch_id;echo '<br>';
//		echo 'nonce_str: '.$data->nonce_str;echo '<br>';
//		echo 'sign: '.$data->sign;echo '<br>';
//		echo 'result_code: '.$data->result_code;echo '<br>';
//		echo 'prepay_id: '.$data->prepay_id;echo '<br>';
//		echo 'trade_type: '.$data->trade_type;echo '<br>';
//      echo 'code_url: '.$data->code_url;echo '<br>';
        $data=[
            'code_url'=>$data->code_url,
            'order_id'=>$order_id
        ];
        return view('wxpay.pay',$data);

    }

    //将数据转化为XML
    protected function ToXml(){
        if (!is_array($this->values)||count($this->values)<=0) {
            die("数组数据异常");
        }
        $xml="<xml>";
        foreach ($this->values as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    public function SetSign(){
        $sign=$this->makeSign();
        $this->values['sign']=$sign;
        return $sign;
    }

    private function makeSign()
    {
        //签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->ToUrlParams();
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".env('WEIXIN_MCH_KEY');
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    //格式化参数
    protected function ToUrlParams()
    {
        $buff="";
        foreach ($this->values as $k=>$v)
        {
            if($k!="sign"&&$v!=""&&!is_array($v)){
                $buff.= $k."=".$v."&";
            }
        }
        $buff= rtrim($buff,"&");
        return $buff;
    }

    private  function postXmlCurl($xml, $url, $useCert = false, $second = 30){
        $ch=curl_init();//curl_init — 初始化 cURL 会话
        //设置超时 curl_setopt — 设置 cURL 传输选项
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch,CURLOPT_HEADER,FALSE);//设置header
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_POST,TRUE);//post提交方式
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        //运行curl
        $data=curl_exec($ch);
        //返回结果
        if ($data){
            curl_close($ch);
            return $data;
        }else{
            $error=curl_close($ch);
            curl_close($ch);
            die("curl出错，错误码：$error");
        }
    }

    //微信支付回调
    public function notify(){
//        echo "111";
        $data=file_get_contents("php://input");
//        dd($data);
        //日志
        $log_str=date('Y-m-d H:i:s')."\n".$data."\n";
        file_put_contents('logs/wx_pay_notice.log',$log_str,FILE_APPEND);
        $xml=simplexml_load_string($data);
//        dd($xml);
        if ($xml->result_code=='SUCCESS'&&$xml->return_code=='SUCCESS'){
            //验证签名
            $sign=true;
            if ($sign){
//                echo "22";
                //验签成功
                //TODO 逻辑处理  订单状态更新
                $pay_time = strtotime($xml->time_end);
//                echo $pay_time;
//                $res=Order::where(['order_id'=>4])->update(['pay_time'=>time()]);
                $res=Order::where(['order_sn'=>$xml->out_trade_no])->update(['order_amount'=>$xml->cash_fee,'pay_time'=>$pay_time]);
//                dd($res);
            }else{
                //验签失败
                echo "验证签名失败，IP：".$_SERVER['REMOTE_ADDR'];
            }
        }
        $response='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
//        dd($response);
        return $response;
    }

    //支付成功
    public function paySuccess(){
        $order_id=$_GET['order_id'];
        echo '订单：'.$order_id.",支付成功";
    }
}
