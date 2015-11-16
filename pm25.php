<?php

header('Content-type:text/html;charset=utf-8');

require("getToken.php");

function getPM25($cityname)
{
    $param = array("city" => $cityname, "key" => "baa7b2f4ce8cd00af3b96daa6bdbf2d3");
    $paramString = http_build_query($param);
    $url = "http://web.juhe.cn:8080/environment/air/cityair?" . $paramString;
    $pm25 = json_decode(getToken($url), true);
    $content='';
    if ($pm25) {
        if ($pm25['error_code'] == '0') {
            $date = $pm25['result'][0]['citynow']['date'];
            $city = $pm25['result'][0]['citynow']['city'];
            $AQI = $pm25['result'][0]['citynow']['AQI'];
            $quality = $pm25['result'][0]['citynow']['quality'];
            $content=$date . "\n" . "城市: ".$city . "\n" ."AQI: ". $AQI . "\n" ."空气质量: ". $quality;
        } else {
            echo $pm25['error_code'] . ":" . $pm25['reason'];
        }
    } else {
        echo "请求失败";
    }
    return $content;
}
?>
