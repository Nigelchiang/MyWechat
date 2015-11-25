<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nigel
 * Date: 2015/11/25
 * Time: 0:52
 */
//use 的作用是引入命名空间，文件还是得自己引入吗？
use Overtrue\Wechat\Message;
use Overtrue\Wechat\Server;

require __DIR__ . "/autoload.php";

$server = new Server("wxea2364b2dfd8449b", "test", "uyCAHekwGlBLLD78A0iFTsQ6n4O2czDTD1BSITUmyxF");
$server->on(
    'message', function ($message) {
    return Message::make('text')->content("正在功能升级，敬请期待哦~");
});

$result=$server->serve();
echo $result;