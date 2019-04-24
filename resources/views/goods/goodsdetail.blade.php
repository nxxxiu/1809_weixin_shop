<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }
        .full-height {
            height: 100vh;
        }
        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }
        .position-ref {
            position: relative;
        }
        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }
        .content {
            text-align: center;
        }
        .title {
            font-size: 84px;
        }
        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }
        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endauth
        </div>
    @endif

    <div class="content">
        <div class="title m-b-md">

        </div>
        <ul>
            <li>商品名称：{{$data['goods_name']}}</li>
            <li>商品价格：{{$data['goods_price']}}</li>
            <li>商品库存：{{$data['goods_store']}}</li>
            <li>浏览次数：{{$view}}</li>
        </ul>
        <hr>
    </div>
    <div style="margin-top: 300px">
        <h1>浏览历史</h1>
        <ul>
            @foreach($data1 as $k=>$v)
            <li>商品名称：{{$v->goods_name}} ------ 商品价格：￥{{$v->goods_price}}</li>
            @endforeach
        </ul>
    </div>
</div>
</body>
</html>
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<script>
    wx.ready(function () {   //需在用户可能点击分享按钮前就先调用
        wx.updateAppMessageShareData({
            title: '旺旺', // 分享标题
            desc: 'emmmm....', // 分享描述
            link: 'http://1809niqingxiu.comcto.com/goodsdetail/'+"{{$data['goods_id']}}", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://1809niqingxiu.comcto.com/img/ok.jpg', // 分享图标
            success: function () {
                // 设置成功
                alert('分享成功');
            }
        })
    });

    wx.ready(function () {      //需在用户可能点击分享按钮前就先调用
        wx.updateTimelineShareData({
            title: '旺旺', // 分享标题
            link: 'http://1809niqingxiu.comcto.com/goodsdetail/'+"{{$data['goods_id']}}", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://1809niqingxiu.comcto.com/img/ok.jpg', // 分享图标
            success: function () {
                // 设置成功
                alert('分享成功');
            }
        })
    });
</script>