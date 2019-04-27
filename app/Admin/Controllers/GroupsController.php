<?php

namespace App\Admin\Controllers;

use App\WxUser;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use GuzzleHttp\Client;

class GroupsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $data=WxUser::get();
//        dd($data);
        return $content
            ->header('用户管理')
            ->description('群发消息')
            ->body(view('groups.index',['data'=>$data]));
    }

    public function groups(){
        $client=new Client();
        $openid=$_GET['openid'];
//        echo $openid;die;
        $text=$_GET['text'];
        $openid=explode(',',$openid);
        $arr=[
            'touser' => $openid,
            'msgtype' => 'text',
            'text' => [
                'content'=>$text
            ]
        ];
//        print_r($arr);die;
        $str=json_encode($arr,JSON_UNESCAPED_UNICODE);
//        print_r($str);die;
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.getWxAccessToken();
        $response=$client->request('POST',$url,[
            'body'=>$str
        ]);
//        print_r($response->getBody());die;

    }

}
