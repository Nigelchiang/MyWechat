<?php
/**
 * wechat php test
 */

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
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)) {
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
             the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $time = time();
            $keyword = trim($postObj->Content);
            $event = $postObj->Event;
            $eventKey = $postObj->EventKey;
            $msgType = $postObj->MsgType;
            //只有将返回消息放在这样的xml格式里微信才能解析，才能正确地给用户返回消息
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";

            if ($msgType == "image") {
                $msgType = "text";
                $content = "你的皂片我已经收到啦，分析完成之后就恢复你哦~";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $content);
                echo $resultStr;
            }

            $eventKeyStr = substr($eventKey, 0, 8);
            if ($event == "subscribe" && $eventKeyStr == "qrscene_") {
                /*
                 * 数据库插入
                 * 插入value=$eventKey=qrsecne_生成参数？？
                 */
                $msgType = "text";
                $content = "你通过扫描带参数的二维码关注我~谢谢";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $content);
                echo $resultStr;
            }

            if ($event == "subscribe") {
                $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                    <item>
                    <Title><![CDATA[谢谢关注~]]></Title>
                    <Description><![CDATA[这是我的个人微信公众号，最近正在开发后台，会慢慢添加一些有趣的功能哦！\n可以发送你好、天气或者任意内容，试试看，会有惊喜哦！]]></Description>
                    <PicUrl><![CDATA[http://www.sinaimg.cn/dy/slidenews/4_img/2015_11/704_1575962_849639.jpg]]></PicUrl>
                    <Url><![CDATA[http://www.jikexueyuan.com]]></Url>
                    </item>
                    </Articles>
                    </xml>";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time);
                echo $resultStr;
            }

            if ($event == "SCAN") {
                $MsgType = "text";
                $Content = "您已关注过我啦~";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $MsgType, $Content);
                echo $resultStr;
            }


            if (!empty($keyword)) {
                if ($keyword == "你好" | $keyword == "您好") {
                    $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                    <item>
                    <Title><![CDATA[你好呀，李银河]]></Title>
                    <Description><![CDATA[你好你好你好！\n可以发送你好、天气或者任意内容，试试看，会有惊喜哦！]]></Description>
                    <PicUrl><![CDATA[http://1.n1gel.sinaapp.com/img/hello.jpeg]]></PicUrl>
                    <Url><![CDATA[http://www.nigel.top]]></Url>
                    </item>
                    </Articles>
                    </xml>";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time);
                    echo $resultStr;
                }

                $subKeyword = mb_substr($keyword, 0, 2, 'utf8');
                if ($subKeyword == "空气" || $subKeyword == "天气" || $subKeyword == "kq") {
                    include("pm25.php");
                    $city = mb_substr($keyword, 2, 5, 'utf8');
                    $content = getPM25($city);
                    $msgType = 'text';
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $content);
                    echo $resultStr;
                }

                $textArray = array("这是我的个人微信公众号，最近正在开发后台，会慢慢添加一些有趣的功能哦！\n可以发送你好、天气或者任意内容，试试看，会有惊喜哦！", "你好，我是Nigel！我现在还只会说这一句话，但是我会不停地学习的~", "你好，这是我的第二句话~", "你猜我能说多少话呢？", "谢谢你关注我，真的非常感谢，真的！", "你知道你喜欢谁吗？我反正只喜欢敲代码", "好玩吗？还想玩吗？", "这个微信后台是用PHP写的，放在新浪的SAE上的一个简单页面，是我第一次做微信后台哦~", "我在这里写日记好了，反正也没人看得到~", "微信后台还是很好玩的，接触了很多有趣的概念，这对以后开发网络应用肯定有好处的呀~", "垃圾新浪，用Git传代码只能用https协议，怎么才能不用一直输密码呢？好烦呀", "不知不觉已经在代码里敲了好多话了…会不会有人看到呢？", "现在已经凌晨四点半了，早上第一节还有c++,哈哈，上完课就睡觉咯~", "敲代码真好玩~我愿意这样敲一辈子！", "饶意，怎么办，又想起你了。这一句应该不会抽到吧~", "霍比特人是真的好看，要不是看霍比特人我早就睡啦", "敲键盘不敢太大声…这机械键盘声音太大啦…", "再加一句，试试记住密码的效果怎么样");
                //从数组中随机返回一个元素的键名
                $content = $textArray[array_rand($textArray)];
                $msgType = 'text';
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $content);
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