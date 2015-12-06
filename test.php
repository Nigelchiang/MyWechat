<?php

//use 的作用是引入命名空间，文件还是得自己引入吗？
use Overtrue\Wechat\Message;
use Overtrue\Wechat\Server;

require __DIR__ . "/autoload.php";

$appId          = "wxea2364b2dfd8449b";
$token          = "test";
$encodingAESKey = "uyCAHekwGlBLLD78A0iFTsQ6n4O2czDTD1BSITUmyxF";
$server         = new Server($appId, $token, $encodingAESKey);

//文字消息处理，调用图灵机器人
$server->on('message', 'text', function ($message) {
    return Message::make('text')->content("正在功能升级，敬请期待哦~");
});

//图片处理，调用Face++
$server->on('message', 'image', function ($msg) {
    require "face/face.php";
    $face             = new Facepp();
    $face->api_key    = "5ab70241a2a2d6e7a4f10b5f79385526";
    $face->api_secret = 'pwhInerTEiE2FPQKRgoRZlw5vkzdJ-WF';
    $params         = array(
        'attribute' => 'gender,age,race,smiling,glass,pose',
        'url'       => $msg->PicUrl);
    $response         = $face->execute('/detection/detect', $params);
    if ($response['http_code'] == 200) {
        $data = json_decode($response['body'], true);

        return Message::make('text')->content($data);
    }
    return Message::make('text')->content($msg->PicUrl);

});

$result = $server->serve();
echo $result;