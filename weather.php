<?php
header('Content-type:text/html;charset=utf-8');

require("getToken.php");


function getWeather($cityname)
{
    $urlcityname = urlencode($cityname);
    $url = "http://v.juhe.cn/weather/index?&cityname=" . $urlcityname . "&key=81e88deace60aeae578ccffb2176d484";
    $weather = json_decode(gettoken($url), ture);

//    print_r($weather);
    if ($weather['resultcode'] == '200') {
        $wen = "当前温度：" . $weather['result']['sk']['temp'];//当前温度
        $fen = "当前风向风级：" . $weather['result']['sk']['wind_direction'] . "-" . $weather['result']['sk']['wind_strength'];//当前风向风级
        $city = "城市：" . $weather['result']['today']['city'];//城市
        $riqi = "日期：" . $weather['result']['today']['date_y'];//日期
        $wendu = "今日温度：" . $weather['result']['today']['temperature'];//温度
        $chuan = "穿衣指数: " . $weather['result']['today']['dressing_advice'];//穿衣指数

        return $city . "\n" . $riqi . "\n" . $wendu . "\n" . $wen . "\n" . $fen . "\n" . $chuan;
    } else {
        return $weather['resultcode'] . ": " . $weather['reason'];
    }
}