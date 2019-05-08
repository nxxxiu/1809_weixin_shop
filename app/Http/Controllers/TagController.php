<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TagController extends Controller
{
    public function create(){
        $url="https://api.weixin.qq.com/cgi-bin/tags/create?access_token=".getWxAccessToken();
    }
}
