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
/**
 * 生成提示功能的news
 * @return array
 */
$welcome = function () {
    return array(
        Message::make('news_item')->title("你好~欢迎关注！")->PicUrl('http://n1gel-n1gel.stor.sinaapp.com/img%2Fwelcome.jpg'),
        Message::make('news_item')->title("『1』发送图片可以查询照片中人脸的年龄和性别信息")->PicUrl('http://233.weego.sinaapp.com/images/face.jpg'),
        Message::make('news_item')->title("『2』发送一张两人合影的照片可以计算两人的相似程度")->PicUrl('http://233.weego.sinaapp.com/images/mask.png'),
        Message::make('news_item')->title("『3』机智的图灵机器人陪你聊天解闷,还可以查天气查火车查航班…")->PicUrl('http://n1gel-n1gel.stor.sinaapp.com/img%2Fbaymax.png'),
        Message::make('news_item')->title("『4』四六级查分功能正在开发中，敬请期待~")->PicUrl('http://n1gel-n1gel.stor.sinaapp.com/img%2F%E5%9B%9B%E5%85%AD%E7%BA%A7%E6%9F%A5%E5%88%86.jpg'));
};
$server->on('event', 'subscribe', function ($event) use ($welcome) {
    return Message::make('news')->items($welcome);
});

//文字消息处理，调用图灵机器人
$server->on('message', 'text', function ($message) use ($welcome) {
    $guide = array("你好", "你能干什么", "哈哈", "您好", "喂");
    if (in_array(trim($message->Content), $guide)) {
        return Message::make('news')->items($welcome);
    }

    $url    = "http://www.tuling123.com/openapi/api?";
    $params = array("key" => "08ad04b298923b29a203d0aca21a9779", "info" => $message->Content);
    $url .= http_build_query($params);
    $response = file_get_contents($url);
    $data     = json_decode($response, true);

//    if ($data['code'] == 200000) {
//        //处理链接类请求
//        $link = "<a href=\"" . $data['url'] . "\"> 『点击查看』</a>";
//
//        return Message::make('text')->content($data['text'] . $link);
//
//    } elseif ($data['code'] == 100000) {
//        //返回天气news
//        $weatherArray = explode(';', $data['text']);
//        //用;分，最后会有一个空字符串
//        if (count($weatherArray) == 5) {
//            $city = strtok($weatherArray[0], ':');
//
//            //今日天气特殊处理
//            $items[0]['title'] = str_replace(',', "\n", strtok(':'));
//            //取出天气状况，决定天气图标
//            $tmp                 = explode(' ', $items[0]['title']);
//            $items[0]['weather'] = $tmp[3];
//            for ($i = 1; $i < 4; ++$i) {
//                $items[$i]['title']   = str_replace(',', "\n", $weatherArray[$i]);
//                $tmp                  = explode(' ', $items[$i]['title']);
//                $items[$i]['weather'] = $tmp[2];
//            }
//            //这是返回数据的BUG
//            //            $items[1]['title'] = str_replace('大雪', "~~ ", $items[1]['title']);
//
//            foreach ($items as &$item) {
//                if (strstr($item['weather'], "多云转晴")) {
//                    $item['url'] = "http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E6%99%B4%E8%BD%AC%E5%A4%9A%E4%BA%91.png";
//                } elseif (strstr($item['weather'], "阵雨转多云")) {
//                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E9%9B%A8%E8%BD%AC%E5%A4%9A%E4%BA%91.png';
//                } elseif (strstr($item['weather'], "晴")) {
//                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E6%99%B4.png';
//                } elseif (strstr($item['weather'], "多云")) {
//                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E5%A4%9A%E4%BA%91.png';
//                } elseif (strstr($item['weather'], "小雪")) {
//                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E9%9B%AA.png';
//                } elseif (strstr($item['weather'], "雨")) {
//                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E5%A4%A7%E9%9B%A8.png';
//                } elseif (strstr($item['weather'], "阴")) {
//                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E9%98%B4.png';
//                } elseif (strstr($item['weather'], "雪")) {
//                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E5%A4%A7%E9%9B%AA.png';
//                } elseif (strstr($item['weather'], "小雨")) {
//                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E5%B0%8F%E9%9B%A8.png';
//                } else {
//                    $item['url'] = "http://n1gel-n1gel.stor.sinaapp.com/weather%2F528a43662164c_12.png";
//                }
//            }
//
//            return Message::make('news')->items(function () use ($city, $items) {
//                return array(
//                    Message::make('news_item')->title("亲，已为你找到{$city}的天气信息")->PicUrl("http://n1gel-n1gel.stor.sinaapp.com/weather%2Fweather_cover.jpg"),
//                    Message::make('news_item')->title($items[0]['title'])->PicUrl($items[0]['url']),
//                    Message::make('news_item')->title($items[1]['title'])->PicUrl($items[1]['url']),
//                    Message::make('news_item')->title($items[2]['title'])->PicUrl($items[2]['url']),
//                    Message::make('news_item')->title($items[3]['title'])->PicUrl($items[3]['url'])
//                );
//            });
//        }
//
//        return Message::make('text')->content($data['text']);
//    }

    return Message::make('text')->content("test");

});
//图片处理，调用Face++
$server->on('message', 'image', function ($title) {
    //    require "face/face.php";
    //    $face             = new Facepp();
    //    $face->api_key    = "5ab70241a2a2d6e7a4f10b5f79385526";
    //    $face->api_secret = 'pwhInerTEiE2FPQKRgoRZlw5vkzdJ-WF';
    $params = array(
        'api_key'    => "5ab70241a2a2d6e7a4f10b5f79385526",
        'api_secret' => 'pwhInerTEiE2FPQKRgoRZlw5vkzdJ-WF',
        'attribute'  => 'gender,age,race,smiling,glass,pose',
        'url'        => $title->PicUrl);
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
            if (count($faceArray) > 1) {
                $resultStr .= "\n第" . ($i + 1) . "张脸";
            }
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