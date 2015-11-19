<?php

header('Content-type:text/html;charset=utf-8');

require("getToken.php");
//测试
function getPM25($cityname)
{
    /*
     * 地址："http://web.juhe.cn:8080/environment/air/cityair?"
     * key:"baa7b2f4ce8cd00af3b96daa6bdbf2d3"
     *
     * pm25.in
     *  "http://www.pm25.in/api/querys/pm2_5.json?"
     *  "token" => "5j1znBVAsnSf5xQyNQyq"
     */

    $param = array("city" => $cityname, "key" => "baa7b2f4ce8cd00af3b96daa6bdbf2d3");
    $paramString = http_build_query($param);
    echo $url = "http://web.juhe.cn:8080/environment/air/cityair?" . $paramString;
    $data = json_decode(getToken($url), true);

    if ($data) {
        if ($data['error_code']=="0") {
            $date = "更新时间: " . $data['result'][0]['citynow']['date'];
            $city = "查询城市: " . $data['result'][0]['citynow']['city'];
            $AQI = "当前AQI: " . $data['result'][0]['citynow']['aqi'];
//            $pm2_5 = "PM2.5(1h平均): " . $data[0]['pm2_5'];
//            $pm2_5_24h = "PM2.5(24h平均)" . $data[0]['pm2_5_24h'];
//            $primary_pollutant = "首要污染物" . $data[0]['primary_pollutant'];
            $quality = "空气质量: " . $data['result'][0]['citynow']['quality'];
            $content = $city . "\n" . $AQI . "\n". $quality . "\n" . $date;
        } else {
            $content = $data['error_code'].": ".$data['reason'];
        }
    } else {
        $content = "请求失败";
    }

    return $content;
}

?>
