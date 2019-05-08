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


<div>
    <div>
        <h2>商品详情</h2>
        <ul>
            <li>商品名称：{{$data['goods_name']}}</li>
            <li>商品价格：{{$data['goods_price']}}</li>
            <li>商品库存：{{$data['goods_store']}}</li>
            <li>浏览次数：{{$view}}</li>
            <li>商品图片：<br>
                <img src="http://weixin.cccute.top/img/{{$data['goods_img']}}" alt="暂无图片" width="170" height="200">
            </li>

        </ul>==============================================
    </div>
    <div>
        <h2>浏览历史</h2>
        <ul>
            @foreach($data1 as $k=>$v)
                <li>商品名称：{{$v->goods_name}} ------ 商品价格：￥{{$v->goods_price}}</li>
            @endforeach
        </ul>
    </div>
</div>

<div id="qrcode"></div>
</body>
</html>
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<script src="/js/qrcode.js"></script>
<script type="text/javascript">
    new QRCode(document.getElementById('qrcode'), "{{$url_code}}");
</script>
<script>
    wx.config({
        // debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: "{{$jsconfig['appId']}}", // 必填，公众号的唯一标识
        timestamp:"{{$jsconfig['timestamp']}}" , // 必填，生成签名的时间戳
        nonceStr:"{{$jsconfig['nonceStr']}}", // 必填，生成签名的随机串
        signature: "{{$jsconfig['signature']}}",// 必填，签名
        jsApiList: ['updateAppMessageShareData','updateTimelineShareData','onMenuShareTimeline','onMenuShareAppMessage','chooseImage','uploadImage'] // 必填，需要使用的JS接口列表
    });

    wx.ready(function () {   //需在用户可能点击分享按钮前就先调用
        wx.updateAppMessageShareData({
            title: '旺仔', // 分享标题
            desc: 'emmmm....', // 分享描述
            link: 'http://weixin.cccute.top/goodsdetail/'+"{{$data['goods_id']}}", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://weixin.cccute.top/img/okk.jpg', // 分享图标

            success: function () {
                // 设置成功
                // alert('分享成功');
            }
        })
        wx.updateTimelineShareData({
            title: '旺旺', // 分享标题
            link: 'http://weixin.cccute.top/goodsdetail/' + "{{$data['goods_id']}}", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://weixin.cccute.top/img/okk.jpg', // 分享图标
            success: function () {
                // 设置成功
                // alert('分享成功');
            }
        })
        wx.onMenuShareTimeline({
            title: '旺旺fei', // 分享标题
            link: 'http://weixin.cccute.top/goodsdetail/' + "{{$data['goods_id']}}", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://weixin.cccute.top/img/okk.jpg', // 分享图标
            success: function () {
                // 用户点击了分享后执行的回调函数
            },
        })
        wx.onMenuShareAppMessage({
            title: '旺仔fei', // 分享标题
            desc: 'emmmm....', // 分享描述
            link: 'http://weixin.cccute.top/goodsdetail/'+"{{$data['goods_id']}}", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://weixin.cccute.top/img/okk.jpg', // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户点击了分享后执行的回调函数
            }
        });
    });
</script>
