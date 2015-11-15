<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>AQI查询</title>
</head>
<body>
<?php

header('Content-type:text/html;charset=utf-8');

require("getToken.php");

$param = array("city" => "shenyang", "key" => "baa7b2f4ce8cd00af3b96daa6bdbf2d3");
$paramString = http_build_query($param);
$url = "http://web.juhe.cn:8080/environment/air/cityair?" . $paramString;
$pm25 = json_decode(getToken($url), true);

if ($pm25) {
    if ($pm25['error_code'] == '0') {
        $date = $pm25['result'][0]['citynow']['date'];
        $city = $pm25['result'][0]['citynow']['city'];
        $AQI = $pm25['result'][0]['citynow']['AQI'];
        $quality = $pm25['result'][0]['citynow']['quality'];
    } else {
        echo $pm25['error_code'] . ":" . $pm25['reason'];
    }
} else {
    echo "请求失败";
}

echo "<table>
<tcaption>$date</tcaption>
<tr>
<td>城市</td><td>$city</td></tr>
<tr><td>AQI</td><td>$AQI</td></tr>
<tr><td>质量</td><td>$quality</td></tr>
</table>";
?>
</body>
</html>
