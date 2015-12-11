<?php
//session_start();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta>
    <title>四六级查分</title>
</head>
<body>
<?php

$openid = $_GET['openid'];

//查询数据库，openid是否已经存在
$mysql  = new SaeMysql();
$query  = "SELECT openid FROM cet WHERE openid='$openid'";
$openid = $mysql->getVar($query);
//var_dump($examid);
//var_dump($mysql->getLine($query));
//var_dump($mysql->getVar($query));
sae_log(json_encode($openid));
//用户尚未备份考号
if (empty($openid)) {
    $signup = "INSERT INTO cet (openid) VALUES('$openid') ";
    $bool   = $mysql->runSql($signup);
    if (!$bool) {
        echo $debug=sprintf("注册失败 %d : %s",$mysql->errno(),$mysql->errmsg());
        sae_log($debug);
    } else {
        echo "你已成功注册";
        sae_log("成功注册");
        //跳到填写考号和姓名的页面
        //带上openid或者设置session
    }
} else {
    echo "难道跳到这里来了？";
    //跳到备份的页面 //带上openid或者session和
    //页面获取exanid和姓名直接查询，将查询结果存到数据库并显示到页面，生成一个模板图片，让用户保存
}

$mysql->closeDb();
function sae_log($msg) {
    sae_set_display_errors(false);//关闭信息输出
    sae_debug($msg);//记录日志
    sae_set_display_errors(true);//记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
}
?>
</body>
</html>