<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>签到</title>
</head>
<body>
<p>签到次数：{{$num}}</p>
@for($i=0;$i<=count($date_time)-1;$i++)
    <p>签到历史：{{$date_time[$i]}}签到一次</p>
@endfor
</body>
</html>