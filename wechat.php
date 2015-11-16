<?php
/**
 * wechat php test
 */

//define your token
define("TOKEN", "nigel");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();

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
            $this->responseMsg();
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
            $keyword = trim($postObj->Content);
            $time = time();

            //只有将返回消息放在这样的xml格式里微信才能解析，才能正确地给用户返回消息
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            $substr = mb_substr($keyword, 0, 2, 'utf8');
            if ($substr == "天气") {
                include("pm25.php");
                getPM25(urlencode($substr));
            }
            if (!empty($keyword)) {
                $msgType = "text";
                $textArray = array("你好，我是Nigel！我现在还只会说这一句话，但是我会不停地学习的~", "你好，这是我的第二句话~", "你猜我能说多少话呢？", "谢谢你关注我，真的非常感谢，真的！", "你知道你喜欢谁吗？我反正只喜欢敲代码", "好玩吗？还想玩吗？", "这个微信后台是用PHP写的，放在新浪的SAE上的一个简单页面，是我第一次做微信后台哦~", "我在这里写日记好了，反正也没人看得到~", "微信后台还是很好玩的，接触了很多有趣的概念，这对以后开发网络应用肯定有好处的呀~", "垃圾新浪，用Git传代码只能用https协议，怎么才能不用一直输密码呢？好烦呀", "不知不觉已经在代码里敲了好多话了…会不会有人看到呢？", "现在已经凌晨四点半了，早上第一节还有c++,哈哈，上完课就睡觉咯~", "敲代码真好玩~我愿意这样敲一辈子！", "饶意，怎么办，又想起你了。这一句应该不会抽到吧~", "霍比特人是真的好看，要不是看霍比特人我早就睡啦", "敲键盘不敢太大声…这机械键盘声音太大啦…", "再加一句，试试记住密码的效果怎么样");
                //从数组中随机返回一个元素的键名
                $contentStr = $textArray[array_rand($textArray)];
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
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