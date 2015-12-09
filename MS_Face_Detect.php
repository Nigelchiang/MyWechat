<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2015/12/9
 * Time: 9:32
 */
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
            return array($output);
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

$url="https://oxfordportal.blob.core.windows.net/face/demo/Verification%201-2.jpg";
$response = detect($url);
if ($response !== false) {
    $amount      = count($response);
    $description = "";

    if ($amount == 0) {
        $description = "照片中木有人脸/:fade";
    } else {
        $description .= "照片中共检测到{$amount}张脸";
        $params = array();
        for ($i = 0; $i < $amount; $i++) {
            if ($amount > 1) {
                $description .= "\n第{$i}张脸";
            }
            $rec  = $response[$i][0]['faceRectangle'];
            $attr = $response[$i][0]['attributes'];

            $rec["gender"] =$attr['gender'];
            array_push($params, $rec);
            $description .= "\n年龄: " . $attr['age'];
            $description .= "\n性别: " . $attr['gender'];
        }

        $ch = curl_init("MS_FaceDetectResult.php?" ."url={$url}". http_build_query($params));
        curl_exec($ch);
    }
}
