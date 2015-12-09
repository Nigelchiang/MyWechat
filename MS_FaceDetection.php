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
include "sdk/SAE_Storage/saestorage.class.php";


$url       = "https://oxfordportal.blob.core.windows.net/face/demo/Verification%201-2.jpg";
$rectangle = detect($url)['0'];
$x1        = $rectangle['left'];
$y1        = $rectangle['top'];
$width     = $rectangle['width'];
$height    = $rectangle['height'];
$x2        = $x1 + $width;
$y2        = $y1 + $height;

//通过cUrl下载的图片，放到imagecreatefromstring里，就可以了
$img          = imagecreatefromstring(getImg($url));
$color_male   = imagecolorallocate($img, 13, 163, 238);
$color_female = imagecolorallocate($img, 186, 11, 147);
//设置笔画的粗细
imagesetthickness($img, 3);
//画一个矩形
imagerectangle($img, $x1, $y1, $x2, $y2, $color_female);
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


/**
 * 调用微软人脸识别API
 * @param $url
 * @return array
 */
function detect($url) {
    $faceKey    = "dd3e5074da61435dab8dc8b001ff1b2f";
    $requestUrl = "https://api.projectoxford.ai/face/v0/detections?analyzesFaceLandmarks=true&analyzesAge=true&analyzesGender=true";
    $data       = array("url" => $url);
    $data       = json_encode($data);
    $header     = array(
        'Content-Type:application/json',
        'Ocp-Apim-Subscription-Key:' . $faceKey,
        'Content-Length:' . strlen($data));
    try {
        $ch = curl_init();
        if (false === $ch) {
            throw new Exception('failed to initialize');
        }

        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //信任任何ssl证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        if (false === $output) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }

        $output = json_decode($output, true);

        //        if (!isset($output['code'])) {
        return array($output['0']['faceRectangle']);
        //        }
        // ...process $content now
    } catch (Exception $e) {

        trigger_error(sprintf(
                          'Curl failed with error #%d: %s',
                          $e->getCode(), $e->getMessage()),
                      E_USER_ERROR);

    }
    //    return false;
}

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
