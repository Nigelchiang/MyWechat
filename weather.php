<?php
header('Content-type:text/html;charset=utf-8');

echo getWeather("沈阳");

function getWeather($cityname) {

    $urlcityname = urlencode($cityname);
    echo $url = "http://v.juhe.cn/weather/index?&cityname=" . $urlcityname .
                "&key=81e88deace60aeae578ccffb2176d484";
    $weather = json_decode(getToken($url), true);

    if ($weather['resultcode'] == '200') {
        $wen   = "当前温度：" . $weather['result']['sk']['temp'];
        $fen
               = "当前风向风级：" . $weather['result']['sk']['wind_direction'] . "-" . $weather['result']['sk']['wind_strength'];
        $city  = "城市：" . $weather['result']['today']['city'];
        $riqi  = "日期：" . $weather['result']['today']['date_y'];
        $wendu = "今日温度：" . $weather['result']['today']['temperature'];
        $chuan = "穿衣指数: " . $weather['result']['today']['dressing_advice'];

        return $city . "\n" . $riqi . "\n" . $wendu . "\n" . $wen . "\n" . $fen . "\n" . $chuan;
    } else {
        return $weather['resultcode'] . ": " . $weather['reason'];
    }
}

/**
 * @param $url
 * @return mixed
 */
function getToken($url) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}
