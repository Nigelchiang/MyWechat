<?php

header('Content-type:text/html;charset=utf-8');

require("getToken.php");

function getPM25($cityname)
{
    /*
     * 地址："http://web.juhe.cn:8080/environment/air/cityair?"
     * key:"baa7b2f4ce8cd00af3b96daa6bdbf2d3"
     */
    $param = array("city" => $cityname, "token" => "5j1znBVAsnSf5xQyNQyq",'station'=>'no');
    $paramString = http_build_query($param);
    $url = "http://www.pm25.in/api/querys/pm2_5.json" . $paramString;
    $data = json_decode(getToken($url), true);
    if ($data) {
        if ($data['error_code'] == '0') {
            $date = "更新时间: ".$data[0]['time_point'];
            $city = "查询城市: ".$data[0]['area'];
            $AQI = "当前AQI: ".$data[0]['aqi'];
            $pm2_5 = "PM2.5(1h平均): " . $data[0]['pm2_5'];
            $pm2_5_24h = "PM2.5(24h平均)" . $data[0]['pm2_5_24h'];
            $primary_pollutant="首要污染物".$data[0]['primary_pollutant'];
            $quality = "空气质量: ".$data[0]['quality'];
            $content= $city . "\n"  . $AQI . "\n".$pm2_5."\n".$pm2_5_24h."\n".$primary_pollutant."\n". $quality.
            "\n".$date;
        } else {
            $content= $data['error'];
        }
    } else {
        $content= "请求失败";
    }
    return $content;
}
?>
