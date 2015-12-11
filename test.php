<?php

//use 的作用是引入命名空间，文件还是得自己引入吗？
use Overtrue\Wechat\Message;
use Overtrue\Wechat\Messages\NewsItem;
use Overtrue\Wechat\Server;

//SAE调试
//$debug=true //开关
if (isset($debug) && $debug) {
    sae_xhprof_start();
}
require __DIR__ . "/autoload.php";
require "MS_Face_Detect.php";

$appId          = "wxea2364b2dfd8449b";
$token          = "test";
$encodingAESKey = "uyCAHekwGlBLLD78A0iFTsQ6n4O2czDTD1BSITUmyxF";
$server         = new Server($appId, $token, $encodingAESKey);

//关注事件
/**
 * 生成提示功能的news
 * @param null $user_name
 * @return array
 */
$welcome = function ($user_name = null) {
    $i = 1;

    return array(
        Message::make('news_item')->title("{$user_name} 你好~欢迎关注！")->PicUrl('http://n1gel-n1gel.stor.sinaapp.com/img%2Fwelcome.jpg'),
        Message::make('news_item')->title("『" . $i++ . "』发送图片可以查询照片中人脸的年龄和性别信息,还会在脸上标出来哦…")->PicUrl('http://n1gel-wechatimg.stor.sinaapp.com/mmbizaC7DypReicewYESlc5gXjH3IKQbYribnF72lBOIpmK0BWKZ6XTVdcSmaPzwp4NibAqdZTzSYuxNaRoqbrtqaacNWA0814814157.jpg'),
        Message::make('news_item')->title("『" . $i++ . "』机智的图灵机器人陪你聊天解闷,可以查天气查火车查航班…")->PicUrl('http://n1gel-n1gel.stor.sinaapp.com/2786001_213751420000_2.jpg'),
        Message::make('news_item')->title("『" . $i++ . "』新功能：语音聊天~直接给我发送语音就可以聊天了哦~")->PicUrl('http://www.36dsj.com/wp-content/uploads/2015/03/228.jpg'),
        Message::make('news_item')->title("『" . $i++ . "』四六级查分已经上线,回复\"46\"来备份考号吧！")->PicUrl('http://n1gel-n1gel.stor.sinaapp.com/img%2F%E5%9B%9B%E5%85%AD%E7%BA%A7%E6%9F%A5%E5%88%86.jpg'));
};
$server->on('event', 'subscribe', function ($event) use ($welcome) {
    sae_log("用户关注: " . $event->openid);
    $mysql = new SaeMysql();
    //用户以前是否关注过
    $everFollowed = "select openid,name from wechat_user WHERE openid='$event->FromUserName'";
    $user         = $mysql->getLine($everFollowed);
    //用户第一次关注
    if ($user === false) {
        $signup = "insert into wechat_user(openid,followTime) VALUES ('$event->FromUserName',$event->CreateTime)";
        $mysql->runSql($signup);
        sae_log($mysql->errno() . "-" . $mysql->errmsg());
        $mysql->closeDb();

        return Message::make('news')->items($welcome);
    } else {
        //MySQL如何修改现有的一行数据？
        //更新关注时间、关注状态，获取用户姓名
        $update = "update wechat_user set followTime='$event->CreateTime',isFollow=1 WHERE openid='$event->FromUserName'";
        $mysql->runSql($update);
        sae_log($mysql->errno() . "-" . $mysql->errmsg());
        $name = $mysql->getVar("select name from wechat_user WHERE openid='$event->FromUserName'");
        sae_log($mysql->errno() . "-" . $mysql->errmsg());

        return Message::make('news')->items(function () use ($name, $welcome) {
            $welcome($name);
        });
    }

});
//取消关注
$server->on('event', 'unsubscribe', function ($event) {
    sae_log("用户取消关注: " . $event->openid);
    $mysql  = new SaeMysql();
    $signup = "insert into wechat_user(openid,isFollow,unfollowTime) VALUES ('$event->fromusername',0,$event->CreateTime)";
    $mysql->runSql($signup);
    sae_log($mysql->errno() . "-" . $mysql->errmsg());
    $mysql->closeDb();
});

//文字消息处理，调用图灵机器人
$server->on('message', 'text', function ($message) use ($welcome) {
    //四六级查分-备份考号
    return handleText($message->Content, $message->FromUserName, $welcome);
});
//图片处理，调用微软API
$server->on('message', 'image', function ($image) {
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
    sae_log($message->Recogniton);

    return handleText($message->Recognition, $message->FromUserName, $welcome);

});

$result = $server->serve();
echo $result;


/**
 * 处理文字消息
 * @param $text    string
 * @param $openid  string
 * @param $welcome closure
 * @return mixed
 */
function handleText($text, $openid, $welcome) {
    if (in_array(trim($text), array("你好", "你能干什么", "哈哈", "您好", "喂", "你有什么功能"))) {
        return Message::make('news')->items($welcome);
    }
    if (in_array(trim($text), array("四六级", "46", "查分"))) {
        $url = "5.n1gel.sinaapp.com/cet.php?openid={$openid}";

        return Message::make('news')->item(
            Message::make('news_item')->title("四六级查分-先来备份一下考号吧~")->PicUrl("http://n1gel-n1gel.stor.sinaapp.com/cet_cover.jpg")->description("大家期待已久的四六级查分功能终于做好啦！！\n不过，考试成绩得两个月后才会公布，那么久考号早就丢了吧…\n快来备份一下考号吧，成绩公布后，我会第一时间通知你们哦，到时候还是回复『46』就可以直接查到分数啦！\n")->url($url)
        );

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

if (isset($debug) && $debug) {
    sae_xhprof_end();
}


/**
 * SAE调试 在日志中心选择错误日志查看
 * @param $msg string
 */
function sae_log($msg) {
    sae_set_display_errors(false);//关闭信息输出
    sae_debug($msg);//记录日志
    sae_set_display_errors(true);//记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
}