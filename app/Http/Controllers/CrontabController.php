<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Order;
class CrontabController extends Controller
{
    public function delorders()
    {
        $time=time();
        $data=Order::where(['is_del'=>1,'pay_status'=>2])->get();
//        dd($data);
        foreach ($data as $k=>$v){
            if ($time-$v->add_time>1800){
                Order::where(['order_id'=>$v->order_id])->update(['is_del'=>2]);
            }
        }
    }
}
