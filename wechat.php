<?php
//装载模板文件
include_once("wx_tpl.php");

//获取微信发送数据
$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

//define your token
define("TOKEN", "nigel");

$wechatObj = new wechatCallbackapiTest();
if ($_GET["echostr"]) {
    $wechatObj->valid();
} else {
    $wechatObj->responseMsg();
}


class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if ($this->checkSignature()) {
            //验证签名之后，确认get来自微信服务器，原样返回随机字符串echoStr，则验证成功
            //这里只是为了第一次验证服务器，以后可以不需要这样，可以自己增加代码
            echo $echoStr;//这个以后还需要吗？
            exit;
        }
    }

    public function responseMsg()
    {
        //extract post data
        global $postStr;
        if (!empty($postStr)) {
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
             the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            //解析数据
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            //发送消息方ID
            $fromUsername = $postObj->FromUserName;
            //接受消息方ID
            $toUsername = $postObj->ToUserName;
            //消息类型
            $form_MsgType = $postObj->MsgType;
            $time = time();
            $form_Content = trim($postObj->Content);

            //只有将返回消息放在这样的xml格式里微信才能解析，才能正确地给用户返回消息
            if ($form_MsgType == "image") {
                //todo 将收到的图片放在链接里返回
                $articleCount = "1";
                $title = "你的美照已经收到啦~";
                $description = "你的皂片我已经收到啦，分析完成之后就回复你哦~";
                $picUrl = $postObj->PicUrl;
                $url = $picUrl;
                global $newsTpl;
                $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, $articleCount, $title, $description,
                    $picUrl, $url);
                echo $resultStr;
                exit;
            }

            //todo 增加语音消息的处理
            if ($form_MsgType == "voice") {
                $content = "你的声音真美~~我会好好听哒，稍后回复你哦";


                global $textTpl;
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $content);
                echo $resultStr;
                exit;
            }

            //小视频消息处理
            if ($form_MsgType == "shortvideo"){


                $content="哈哈哈，这个小视频可真好玩…我还要多看几遍，待会儿给你回复处理信息哈~";
                global $textTpl;
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $content);
                echo $resultStr;
            }

            //地理位置信息回复
            if($form_MsgType=="location"){
                $lable=$postObj->Label;
                $content = "你在这里：" . $lable . "\n对不对？我是不是很聪明呀~";

                global $textTpl;
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $content);
                echo $resultStr;
            }



                //todo 增加事件处理

            if ($form_MsgType == "event") {
                $event = $postObj->Event;
                $eventKey = $postObj->EventKey;

                $eventKeyStr = substr($eventKey, 0, 8);
                if ($event == "subscribe" && $eventKeyStr == "qrscene_") {
                    /*
                     * 数据库插入
                     * 插入value=$eventKey=qrsecne_生成参数？？
                     */
                    $content = "你通过扫描带参数的二维码关注我~谢谢";
                    global $textTpl;
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $content);
                    echo $resultStr;
                    exit;
                }

                if ($event == "subscribe") {

                    $articleCount = "1";
                    $title = "谢谢关注~";
                    $description = "这是我的个人微信公众号，最近正在开发后台，会慢慢添加一些有趣的功能哦！\n" .
                        "可以发送\"你好\"、天气+城市(比如:\"天气沈阳\")、空气+城市(比如:\"空气沈阳\")  " .
                        "或者任意内容 (比如:\"江航好帅！\" 、\"你是傻逼\") …\n".
                        "现在还可以发送声音、图片、视频和地理位置了哦，我都可以处理啦~赶紧试试吧！".
                        "试试看，会有惊喜哦！";
                    $picUrl = "http://1.n1gel.sinaapp.com/img/hello.jpeg";
                    $url = "http://www.nigel.top";
                    global $newsTpl;
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, $articleCount, $title, $description,
                        $picUrl, $url);
                    echo $resultStr;
                    exit;
                }

                if ($event == "SCAN") {
                    $Content = "您已关注过我啦~";
                    global $textTpl;
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $Content);
                    echo $resultStr;
                    exit;
                }
            }

            if (!empty($form_Content)) {
                if ($form_Content == "你好" | $form_Content == "您好") {

                    $articleCount = "1";
                    $title = "你好呀，李银河";
                    $description = "你好你好你好！\n" .
                        "这是我的个人微信公众号，最近正在开发后台，会慢慢添加一些有趣的功能哦！\n" .
                        "可以发送\"你好\"、天气+城市(比如:\"天气沈阳\")、空气+城市(比如:\"空气沈阳\")  " .
                        "或者任意内容 (比如:\"江航好帅！\" 、\"你是傻逼\") …\n".
                        "现在还可以发送声音、图片、视频和地理位置了哦，我都可以处理啦~赶紧试试吧！".
                        "试试看，会有惊喜哦！";
                    $picUrl = "http://1.n1gel.sinaapp.com/img/hello.jpeg";
                    $url = "http://www.nigel.top";
                    global $newsTpl;
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, $articleCount, $title, $description,
                        $picUrl, $url);
                    echo $resultStr;
                    exit;
                }

                if ($form_Content == "自拍") {

                    $articleCount = "1";
                    $title = "臭不要脸放自拍";
                    $description = "啥也没有\n啥也没有\n啥也没有";
                    $picUrl = "http://1.n1gel.sinaapp.com/img/cover.jpg";
                    $url = "http://1.n1gel.sinaapp.com/img/selfie.jpg";
                    global $newsTpl;
                    $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, $articleCount, $title, $description,
                        $picUrl, $url);
                    echo $resultStr;
                    exit;
                }

                $subKeyword = mb_substr($form_Content, 0, 2, 'utf8');
                if ($subKeyword == "空气" || $subKeyword == "kq") {
                    include("pm25.php");
                    $city = mb_substr($form_Content, 2, 5, 'utf8');
                    $content = getPM25($city);
                    global $textTpl;
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $content);
                    echo $resultStr;
                }

                if ($subKeyword == "天气" || $subKeyword == "tq") {
                    include('weather.php');
                    $city = mb_substr($form_Content, 2, 5, 'utf8');
                    $content = getWeather($city);
                    global $textTpl;
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $content);
                    echo $resultStr;
                }

                $textArray = array("这是我的个人微信公众号，最近正在开发后台，会慢慢添加一些有趣的功能哦！\n可以发送你好、天气或者任意内容，试试看，会有惊喜哦！",
                    "你好，我是Nigel！我现在还只会说这一句话，但是我会不停地学习的~",
                    "你好，这是我的第二句话~",
                    "你猜我能说多少话呢？",
                    "谢谢你关注我，真的非常感谢，真的！",
                    "你知道你喜欢谁吗？我反正只喜欢敲代码",
                    "好玩吗？还想玩吗？",
                    "这个微信后台是用PHP写的，放在新浪的SAE上的一个简单页面，是我第一次做微信后台哦~",
                    "我在这里写日记好了，反正也没人看得到~",
                    "微信后台还是很好玩的，接触了很多有趣的概念，这对以后开发网络应用肯定有好处的呀~",
                    "垃圾新浪，用git传代码只能用https，怎么才能不用一直输密码呢？好烦呀",
                    "不知不觉已经在代码里敲了好多话了…会不会有人看到呢？",
                    "现在已经凌晨四点半了，早上第一节还有c++,"
                    . "哈哈，上完课就睡觉咯~",
                    "敲代码真好玩~我愿意这样敲一辈子！",
                    "XX，怎么办，又想起你了。这一句应该不会抽到吧~那打个码好了",
                    "霍比特人是真的好看，要不是看霍比特人我早就睡啦",
                    "…这键盘声音太大啦…把室友吵醒了就死了…",
                    "再加一句，试试记住密码的效果怎么样",
                    '你可真好玩儿~真逗！',
                    '你才是逗比呢，人家那么萌~',
                    '真的吗？真的吗？真的是真的吗？',
                    '玩够了吗，我可要生气了，哼',
                    '你也真是够无聊的，居然还在聊…',
                    '\(^o^)/\(^o^)/\(^o^)/',
                    '看看你能坚持多久，我特意回来加了好多句话~',
                    'O(∩_∩)O嗯!厉害厉害，我服我服！',
                    'b(￣▽￣)d哈哈',
                    'd=====(￣▽￣*)b 给你点个赞，继续加油，继续生活~',
                    '～(￣▽￣～)(～￣▽￣)～好开心~',
                    'O(∩_∩)O哈哈~',
                    '~(๑•́ ₃ •̀๑)~(๑•́ ₃ •̀๑)好困呀…已经好久没睡觉了…');
                //从数组中随机返回一个元素的键名
                $content = $textArray[array_rand($textArray)];
                global $textTpl;
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $content);
                echo $resultStr;

            } else {
                echo "Input something...";
            }

        } else {
            echo "";
            exit;
        }
    }


    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

}

?>