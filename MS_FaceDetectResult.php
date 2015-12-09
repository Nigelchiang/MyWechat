<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nigel
 * Date: 2015/11/25
 * Time: 19:23
 */
/**
 * 调用微软的人脸识别API，通过PHP在人脸位置绘制矩形标识
 */
//sae_xhprof_start();
//取出URL中的数据放到数组中
parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $rectangle);
var_dump($rectangle);
//通过cUrl下载的图片，放到imagecreatefromstring里，就可以了
$url = $_GET['$url'];
$img = imagecreatefromstring(getImg($url));

foreach ($rectangle as $rec) {
    drawRec($rec, $img);
}

function drawRec($rectangle, $img) {

    $x1     = $rectangle['left'];
    $y1     = $rectangle['top'];
    $width  = $rectangle['width'];
    $height = $rectangle['height'];
    $gender = $rectangle['gender'];
    $x2     = $x1 + $width;
    $y2     = $y1 + $height;


    $color_male   = imagecolorallocate($img, 13, 163, 238);
    $color_female = imagecolorallocate($img, 186, 11, 147);
    $color        = ($gender == "male") ? $color_male : $color_female;
    //设置笔画的粗细
    imagesetthickness($img, 3);
    //画一个矩形
    imagerectangle($img, $x1, $y1, $x2, $y2, $color);
}

//欺骗浏览器，输出图片
header('Content-Type:image/jpeg');
//imageX 第二个参数指定filename，将文件保存到一个地址而不是输出到浏览器
//使用sae storage的wrapper来保存图片
//$stor = new SaeStorage("mkm32j3l42", "3jxwz5kix5limjww22z0l10yk1300y35yy5j03xy");
//var_dump($stor->getList('n'));
//$stor->write('n', "nothing1.txt", "haha");

imagejpeg($img);
//file_put_contents("saestor://n/test.txt", "haha");
imagedestroy($img);


function getImg($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // good edit, thanks!
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); // also, this seems wise considering output is image.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

//sae_xhprof_end();
?>
