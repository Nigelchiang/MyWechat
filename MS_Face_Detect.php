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

$response=detect("https://oxfordportal.blob.core.windows.net/face/demo/detection%204.jpg");
var_dump($a="MS_FaceDetectResult.php?".http_build_query($response));
var_dump(urldecode($a));