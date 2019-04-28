<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
hello
</body>
</html>
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<script>
    wx.config({
        // debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: "{{$jsconfig['appId']}}", // 必填，公众号的唯一标识
        timestamp:"{{$jsconfig['timestamp']}}" , // 必填，生成签名的时间戳
        nonceStr:"{{$jsconfig['nonceStr']}}", // 必填，生成签名的随机串
        signature: "{{$jsconfig['signature']}}",// 必填，签名
        jsApiList: ['updateAppMessageShareData','updateTimelineShareData','onMenuShareTimeline','onMenuShareAppMessage','chooseImage','uploadImage'] // 必填，需要使用的JS接口列表
    })
    wx.ready(function () {   //需在用户可能点击分享按钮前就先调用
        wx.updateAppMessageShareData({
            title: '😋', // 分享标题
            desc: '🤣', // 分享描述
            link: 'http://1809niqingxiu.comcto.com/activity/index', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://1809niqingxiu.comcto.com/img/ok.jpg', // 分享图标

            success: function () {
                // 设置成功
                // alert('分享成功');
            }
        })
        wx.updateTimelineShareData({
            title: 'en ', // 分享标题
            link: 'http://1809niqingxiu.comcto.com/activity/index', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://1809niqingxiu.comcto.com/img/ok.jpg', // 分享图标
            success: function () {
                // 设置成功
                // alert('分享成功');
            }
        })
        wx.onMenuShareTimeline({
            title: '123', // 分享标题
            link: 'http://1809niqingxiu.comcto.com/activity/index', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://1809niqingxiu.comcto.com/img/okk.jpg', // 分享图标
            success: function () {
                // 用户点击了分享后执行的回调函数
            },
        })
        wx.onMenuShareAppMessage({
            title: '1234', // 分享标题
            desc: 'emmmm....', // 分享描述
            link: 'http://1809niqingxiu.comcto.com/activity/index', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://1809niqingxiu.comcto.com/img/okk.jpg', // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户点击了分享后执行的回调函数
            }
        });
    })
</script>

