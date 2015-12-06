<?php

//use 的作用是引入命名空间，文件还是得自己引入吗？
use Overtrue\Wechat\Message;
use Overtrue\Wechat\Server;

require __DIR__ . "/autoload.php";

$appId          = "wxea2364b2dfd8449b";
$token          = "test";
$encodingAESKey = "uyCAHekwGlBLLD78A0iFTsQ6n4O2czDTD1BSITUmyxF";
$server         = new Server($appId, $token, $encodingAESKey);

//关注事件
$server->on('event', 'subscribe', function () {

});
//文字消息处理，调用图灵机器人
$server->on('message', 'text', function ($message) {
    $url    = "http://www.tuling123.com/openapi/api?";
    $params = array("key" => "08ad04b298923b29a203d0aca21a9779", "info" => $message->Content);
    $url .= http_build_query($params);
    $response = file_get_contents($url);
    $data     = json_decode($response, true);
    //处理链接类请求
    if ($data['code'] == 200000) {
        //        return Message::make('news')->item(
        //            Message::make('news_item')->title($data['text'])->url($data['url']));
        $link = "<a href=\"" . $data['url'] . "\"> 『点击查看』</a>";

        return Message::make('text')->content($data['text'] . $link);
    }

    return Message::make('text')->content($data['text']);
});
//图片处理，调用Face++
$server->on('message', 'image', function ($msg) {
    //    require "face/face.php";
    //    $face             = new Facepp();
    //    $face->api_key    = "5ab70241a2a2d6e7a4f10b5f79385526";
    //    $face->api_secret = 'pwhInerTEiE2FPQKRgoRZlw5vkzdJ-WF';
    $params = array(
        'api_key'    => "5ab70241a2a2d6e7a4f10b5f79385526",
        'api_secret' => 'pwhInerTEiE2FPQKRgoRZlw5vkzdJ-WF',
        'attribute'  => 'gender,age,race,smiling,glass,pose',
        'url'        => $msg->PicUrl);
    $url    = "http://apicn.faceplusplus.com/v2/detection/detect?" . http_build_query($params);
    //    $response         = $face->execute('/detection/detect', $params);
    $response  = file_get_contents($url);
    $data      = json_decode($response, true);
    $resultStr = '';
    $faceArray = $data['face'];
    //如果没有检测到人脸
    if (empty($data['face'])) {
        $resultStr = "照片中木有人脸=.=";
    } else {
        $resultStr .= "图中共检测到" . count($faceArray) . "张脸!";
        for ($i = 0; $i < count($faceArray); $i++) {
            $resultStr .= "\n第" . ($i + 1) . "张脸";
            $tempFace = $faceArray[$i];
            // 获取所有属性
            $tempAttr = $tempFace['attribute'];
            // 年龄：包含年龄分析结果
            // value的值为一个非负整数表示估计的年龄, range表示估计年龄的正负区间
            $tempAge = $tempAttr['age'];
            // 性别：包含性别分析结果
            // value的值为Male/Female, confidence表示置信度
            $tempGenger = $tempAttr['gender'];
            // 种族：包含人种分析结果
            // value的值为Asian/White/Black, confidence表示置信度
            $tempRace = $tempAttr['race'];
            // 微笑：包含微笑程度分析结果
            //value的值为0-100的实数，越大表示微笑程度越高
            $tempSmiling = $tempAttr['smiling'];
            // 眼镜：包含眼镜佩戴分析结果
            // value的值为None/Dark/Normal, confidence表示置信度
            $tempGlass = $tempAttr['glass'];
            // 造型：包含脸部姿势分析结果
            // 包括pitch_angle, roll_angle, yaw_angle
            // 分别对应抬头，旋转（平面旋转），摇头
            //            // 单位为角度。
            //            $tempPose = $tempAttr['pose'];
            //返回年龄
            $minAge = $tempAge['value'] - $tempAge['range'];
            $minAge = $minAge < 0 ? 0 : $minAge;
            $maxAge = $tempAge['value'] + $tempAge['range'];
            $resultStr .= "\n年龄：" . $minAge . "-" . $maxAge . "岁";
            // 返回性别
            if ($tempGenger['value'] === "Male") {
                $resultStr .= "\n性别：男";
            } else if ($tempGenger['value'] === "Female") {
                $resultStr .= "\n性别：女";
            }
            // 返回种族
            if ($tempRace['value'] === "Asian") {
                $resultStr .= "\n种族：黄种人";
            } else if ($tempRace['value'] === "Male") {
                $resultStr .= "\n种族：白种人";
            } else if ($tempRace['value'] === "Black") {
                $resultStr .= "\n种族：黑种人";
            }
            // 返回眼镜
            if ($tempGlass['value'] === "None") {
                $resultStr .= "\n眼镜：木有眼镜";
            } else if ($tempGlass['value'] === "Dark") {
                $resultStr .= "\n眼镜：目测墨镜";
            } else if ($tempGlass['value'] === "Normal") {
                $resultStr .= "\n眼镜：普通眼镜";
            }
            //返回微笑
            $resultStr .= "\n微笑：" . round($tempSmiling['value']) . "%";
        }

        if (count($faceArray) === 2) {
            // 获取face_id
            $tempId1 = $faceArray[0]['face_id'];
            $tempId2 = $faceArray[1]['face_id'];

            // face++ 链接
            $params   = array(
                'api_key'    => "5ab70241a2a2d6e7a4f10b5f79385526",
                'api_secret' => 'pwhInerTEiE2FPQKRgoRZlw5vkzdJ-WF',
                'face_id1'   => $tempId1,
                'face_id2'   => $tempId2);
            $jsonStr  = file_get_contents("http://apicn.faceplusplus.com/v2/detection/detect?" . http_build_query($params));
            $replyDic = json_decode($jsonStr, true);
            //取出相似程度
            $tempResult = $replyDic['similarity'];
            $resultStr .= "\n相似程度：" . round($tempResult) . "%";
            //具体分析相似处
            $tempSimilarity = $replyDic['component_similarity'];
            $tempEye        = $tempSimilarity['eye'];
            $tempEyebrow    = $tempSimilarity['eyebrow'];
            $tempMouth      = $tempSimilarity['mouth'];
            $tempNose       = $tempSimilarity['nose'];
            $resultStr .= "\n相似分析:";
            $resultStr .= "\n眼睛：" . round($tempEye) . "%";
            $resultStr .= "\n眉毛：" . round($tempEyebrow) . "%";
            $resultStr .= "\n嘴巴：" . round($tempMouth) . "%";
            $resultStr .= "\n鼻子：" . round($tempNose) . "%";
        }

    }

    return Message::make('text')->content($resultStr);

});

$result = $server->serve();
echo $result;