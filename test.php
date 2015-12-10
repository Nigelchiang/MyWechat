<?php

//use 的作用是引入命名空间，文件还是得自己引入吗？
use Overtrue\Wechat\Message;
use Overtrue\Wechat\Messages\NewsItem;
use Overtrue\Wechat\Server;

//sae_xhprof_start();
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
    $i = 1;

    return array(
        Message::make('news_item')->title("你好~欢迎关注！")->PicUrl('http://n1gel-n1gel.stor.sinaapp.com/img%2Fwelcome.jpg'),
        Message::make('news_item')->title("『" . $i++ . "』发送图片可以查询照片中人脸的年龄和性别信息,还会在脸上标出来哦…")->PicUrl('http://n1gel-wechatimg.stor.sinaapp.com/mmbizaC7DypReicewYESlc5gXjH3IKQbYribnF72lBOIpmK0BWKZ6XTVdcSmaPzwp4NibAqdZTzSYuxNaRoqbrtqaacNWA0814814157.jpg'),
        Message::make('news_item')->title("『" . $i++ . "』机智的图灵机器人陪你聊天解闷,可以查天气查火车查航班…")->PicUrl('http://n1gel-n1gel.stor.sinaapp.com/img%2Fbaymax.png'),
        Message::make('news_item')->title("『" . $i++ . "』新功能：语音聊天~直接给我发送语音就可以聊天了哦~")->PicUrl('http://www.36dsj.com/wp-content/uploads/2015/03/228.jpg'),
        Message::make('news_item')->title("『" . $i++ . "』四六级查分已经完成，即将上线！")->PicUrl('http://n1gel-n1gel.stor.sinaapp.com/img%2F%E5%9B%9B%E5%85%AD%E7%BA%A7%E6%9F%A5%E5%88%86.jpg'));
};
$server->on('event', 'subscribe', function ($event) use ($welcome) {
    return Message::make('news')->items($welcome);
});

//文字消息处理，调用图灵机器人
$server->on('message', 'text', function ($message) use ($welcome) {
    //    sae_log("消息内容 " . $message->Content);

    return handleText($message->Content, $welcome);
});
//图片处理，调用微软API
$server->on('message', 'image', function ($image) {
    require "MS_Face_Detect.php";
    $picUrl   = $image->PicUrl;
    $response = detect($picUrl);
    if ($response !== false) {
        $amount      = count($response);
        $description = "";
        $title       = "";
        if ($response === array()) {
            $title .= "照片中木有人脸/:fade";
        } else {
            $drawedUrl = processImg($picUrl, $response);
            $title .= "照片中共检测到{$amount}张脸 点击查看大图";
            for ($i = 0; $i < $amount; $i++) {
                if ($amount > 1) {
                    $description .= sprintf("\n第%s张脸\n", $i + 1);
                }

                $attr = $response[$i]['attributes'];
                $description .= "年龄: " . $attr['age'];
                $description .= "\n性别: " . $attr['gender'];
            }

            return Message::make('news')->item(
                Message::make("news_item")->title($title)->description($description)->url($drawedUrl)->PicUrl
                ($drawedUrl)
            );
        }

        return Message::make('text')->content($title);
    }

    return Message::make('text')->content("不好意思出错啦/:bye");


});
//语音消息处理，使用微信的识别结果
$server->on('message', 'voice', function ($message) use ($welcome) {
    if (!isset($message->Recognition)) {
        sae_log("无法使用语音消息的Recognition字段");
    } else {
        //        sae_log("语音消息内容 " . $message->Recognition);

        return handleText($message->Recognition, $welcome);
    }
});

$result = $server->serve();
echo $result;

/**
 * SAE调试 在日志中心选择错误日志查看
 * @param $msg string
 */
function sae_log($msg) {
    sae_set_display_errors(false);//关闭信息输出
    sae_debug($msg);//记录日志
    sae_set_display_errors(true);//记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
}

/**
 * 处理文字消息
 * @param $text    string
 * @param $welcome closure
 * @return mixed
 */
function handleText($text, $welcome) {
    $guide = array("你好", "你能干什么", "哈哈", "您好", "喂", "你有什么功能");
    if (in_array(trim($text), $guide)) {
        return Message::make('news')->items($welcome);
    }

    $url    = "http://www.tuling123.com/openapi/api?";
    $params = array("key" => "08ad04b298923b29a203d0aca21a9779", "info" => $text);
    $url .= http_build_query($params);
    //换成使用curl，哈哈，时间变成了1/3，太厉害啦！
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

    $response = curl_exec($ch);
    $data     = json_decode($response, true);

    if ($data['code'] == 200000) {
        //处理链接类请求
        $link = "<a href=\"" . $data['url'] . "\"> 『点击查看』</a>";

        return Message::make('text')->content($data['text'] . $link);

    } elseif ($data['code'] == 100000) {
        //返回天气news
        $weatherArray = explode(';', $data['text']);
        //用;分，最后会有一个空字符串
        if (count($weatherArray) == 5) {
            $city = strtok($weatherArray[0], ':');

            //今日天气特殊处理
            $items[0]['title'] = str_replace(',', "\n", strtok(':'));
            //取出天气状况，决定天气图标
            $tmp                 = explode(' ', $items[0]['title']);
            $items[0]['weather'] = $tmp[3];
            for ($i = 1; $i < 4; ++$i) {
                $items[$i]['title']   = str_replace(',', "\n", $weatherArray[$i]);
                $tmp                  = explode(' ', $items[$i]['title']);
                $items[$i]['weather'] = $tmp[2];
            }
            //这是返回数据的BUG
            //            $items[1]['title'] = str_replace('大雪', "~~ ", $items[1]['title']);

            foreach ($items as &$item) {
                if (strstr($item['weather'], "多云转晴")) {
                    $item['url'] = "http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E6%99%B4%E8%BD%AC%E5%A4%9A%E4%BA%91.png";
                } elseif (strstr($item['weather'], "阵雨转多云")) {
                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E9%9B%A8%E8%BD%AC%E5%A4%9A%E4%BA%91.png';
                } elseif (strstr($item['weather'], "晴")) {
                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E6%99%B4.png';
                } elseif (strstr($item['weather'], "多云")) {
                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E5%A4%9A%E4%BA%91.png';
                } elseif (strstr($item['weather'], "小雪")) {
                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E9%9B%AA.png';
                } elseif (strstr($item['weather'], "雨")) {
                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E5%A4%A7%E9%9B%A8.png';
                } elseif (strstr($item['weather'], "阴")) {
                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E9%98%B4.png';
                } elseif (strstr($item['weather'], "雪")) {
                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E5%A4%A7%E9%9B%AA.png';
                } elseif (strstr($item['weather'], "小雨")) {
                    $item['url'] = 'http://n1gel-n1gel.stor.sinaapp.com/weather%2F%E5%B0%8F%E9%9B%A8.png';
                } else {
                    $item['url'] = "http://n1gel-n1gel.stor.sinaapp.com/weather%2F528a43662164c_12.png";
                }
            }

            return Message::make('news')->items(function () use ($city, $items) {
                return array(
                    Message::make('news_item')->title("亲，已为你找到{$city}的天气信息")->PicUrl("http://n1gel-n1gel.stor.sinaapp.com/weather%2Fweather_cover.jpg"),
                    Message::make('news_item')->title($items[0]['title'])->PicUrl($items[0]['url']),
                    Message::make('news_item')->title($items[1]['title'])->PicUrl($items[1]['url']),
                    Message::make('news_item')->title($items[2]['title'])->PicUrl($items[2]['url']),
                    Message::make('news_item')->title($items[3]['title'])->PicUrl($items[3]['url'])
                );
            });
        }

        return Message::make('text')->content($data['text']);
    }

    return Message::make('text')->content($data['text']);


}
//sae_xhprof_end();