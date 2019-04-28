<?php
namespace App\Admin\Controllers;
use App\Http\Controllers\Controller;
use App\Material;
use Encore\Admin\Controllers\HasResourceActions;
use GuzzleHttp\Client;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
class MaterialController extends Controller{
    public function index(Content $content){
        return $content
            ->header('素材管理')
            ->description('新增素材')
            ->body( view('material.index'));
    }

    public function material(Request $request){
        $img=$this->upload($request,'img');
//        dd($img);
        $client=new Client();
        $url='https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.getWxAccessToken().'&type=image';
        $response=$client->request('post',$url,[
            'multipart'=>[
                [
                    'name'=>'media',
                    'contents'=>fopen('../storage/app/'.$img, 'r'),
                ]
            ]
        ]);
        $json =  json_decode($response->getBody(),true);
//        var_dump($json) ;die;
        if (isset($json['media_id'])){
            Material::insert(['media_id'=>$json['media_id']]);
            echo "上传成功";
        }

    }

    //上传图片
    public function upload(Request $request,$img){
        if ($request->hasFile($img) && $request->file($img)->isValid()) {
            $photo = $request->file($img);
//            $extension = $photo->extension();
            $pre_path='uploads/';
            $store_result = $photo->store(date('Ymd'));
            return trim($store_result,$pre_path);
        }
//        exit('未获取到上传文件或上传过程出错');
    }


}