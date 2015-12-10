<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2015/12/9
 * Time: 9:32
 */
/**
 * @param $url  string 用户上传的图片信息
 * @param $data array 识别的人脸信息
 * @return string storage里存的图片URL
 */
function processImg($url, $data) {
    $img = getImg($url);
    draw($img, $data);
    $drawed_url = save($img, $url);

    return $drawed_url;
}

/**
 * 调用微软人脸识别API
 * @param $url
 * @return array 失败返回false
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

        if (!isset($output['code'])) {
            return $output;
        }
        // ...process $content now
    } catch (Exception $e) {

        trigger_error(sprintf(
                          'Curl failed with error #%d: %s',
                          $e->getCode(), $e->getMessage()),
                      E_USER_ERROR);

    }

    return false;
}

/**
 * 将图片资源保存到storage
 * @param $img       resource 图片资源
 * @param $url       string 原始图片的URL，用作文件名
 * @returns string storage中图片的URL
 */
function save(&$img, $url) {
    //给文件名添加随机的后缀，防止重复
    $random   = mt_rand();
    $filename = substr(str_replace("/", "", parse_url($url, PHP_URL_PATH)), -10) . $random . ".jpg";
    //just for test
    //$stor = new SaeStorage("n353jmy031","zwwkm3wjxmmkxkhwzlyjhxz3lh2xkyj3zhx014lh");

    /*    imagepng这样的函数不支持wrapper,用临时文件解决
        imageX 第二个参数指定filename，将文件保存到一个地址而不是输出到浏览器
        使用sae storage的wrapper来保存图片
        file_put_contents("saestor://n/test.txt", "haha");

        保存为临时文件
        $bool = imagejpeg($img, SAE_TMP_PATH . $filename);
        imagedestroy($img);

            sae_log("保存的文件名：" . $filename);
        从临时文件里取出，保存到storage里
        file_put_contents("saestor://wechatimg/$filename",
                          file_get_contents(SAE_TMP_PATH . $filename));*/

    /*
     * 新的文件保存方法 用缓存来实现，这个方法应该会快很多，因为减少了两个特别慢的函数
     */
    $domain = "wechatimg";
    $stor   = new SaeStorage();
    ob_start();
    imagejpeg($img);
    $imgstr = ob_get_contents();
    $bool   = $stor->write($domain, $filename, $imgstr);
    ob_end_clean();
    imagedestroy($img);
    if (!$bool) {
        sae_log("保存文件失败");
    }


    return $stor->getUrl($domain, $filename);

}

/**
 * 获取保存在wechatimg里图片的URL
 * @param $filename string
 * @return string
 */
function getUrl($filename) {
    $stor = new SaeStorage();

    return $stor->getUrl("wechatimg", $filename);
}


/**
 * @param $recs array 画图的数据
 * @param $img  mixed  图片资源
 */
function draw(&$img, &$recs) {

    foreach ($recs as $rec) {
        $x1     = $rec['faceRectangle']['left'];
        $y1     = $rec['faceRectangle']['top'];
        $width  = $rec['faceRectangle']['width'];
        $height = $rec['faceRectangle']['height'];
        $gender = $rec['attributes']['gender'];
        $x2     = $x1 + $width;
        $y2     = $y1 + $height;
        //拆分数组,取出需要的五个点坐标
        $points = array_slice($rec['faceLandmarks'], 0, 5);


        $color_male   = imagecolorallocate($img, 13, 163, 238);
        $color_female = imagecolorallocate($img, 186, 11, 147);
        $color        = ($gender == "male") ? $color_male : $color_female;
        //设置笔画的粗细
        imagesetthickness($img, 3);
        //画一个矩形
        $bool = imagerectangle($img, $x1, $y1, $x2, $y2, $color);
        //画点
        foreach ($points as $point) {
            $bool = imagefilledrectangle($img, $point['x'] - 7, $point['y'] - 7, $point['x'] + 7, $point['y'] + 7,
                                         $color);
        }

        if (!$bool) {
            sae_log("画图失败");
        }
    }

    return $img;
}

//欺骗浏览器，输出图片
//header('Content-Type:image/jpeg');

/**
 * 通过cUrl下载图片
 * @param $url string 图片的URL
 * @return resource 创建的图片资源
 */
function getImg($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // good edit, thanks!
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); // also, this seems wise considering output is image.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    if ($data === false) {
        sae_log("curl获取图片失败");
    }
    curl_close($ch);

    $img = imagecreatefromstring($data);
    if ($img === false) {
        sae_log("图片创建失败");
    }

    return $img;
}


