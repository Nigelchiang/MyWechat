<?php
session_start(); ?>
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
$query  = "SELECT examid FROM cet WHERE openid=$openid";
$examid = $mysql->getData($query);
//用户尚未备份考号
if (empty($examid)) {
    $signup = "INSERT INTO cet (openid) VALUES($openid) ";
    $bool   = $mysql->runSql($signup);
    if (!$bool) {
        echo "注册失败";
    } else {
        echo "你已成功注册" . "<br/>";
    }
    //跳到填写考号和姓名的页面
    //带上openid或者设置session
} else {
    //跳到结果的页面 //带上openid或者session和
    //页面获取exanid和姓名直接查询，将查询结果存到数据库并显示到页面，生成一个模板图片，让用户保存
}

$mysql->closeDb();
?>
</body>
</html>