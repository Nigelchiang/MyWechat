<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2015/12/11
 * Time: 18:06
 */
session_start();
$openid = $_SESSION['openid'];
$examid = $_SESSION['examid'];
$name   = $_SESSION['name'];
echo "这里是成绩展示界面，会生成一张成绩的图片<br/>";
?>
<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1"/>
    <title>查询结果</title>
</head>
<body>
    <h1>恭喜你，备份成功！</h1>
    <p>2015年12月的四六级考试，成绩大约会在2016年2月底公布</p>
    <p>到时候，方方会第一时间通知你的~放心吧！</p>
</body>
</html>

<?php
function sae_log($msg) {
    sae_set_display_errors(false);//关闭信息输出
    sae_debug($msg);//记录日志
    sae_set_display_errors(true);//记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
}