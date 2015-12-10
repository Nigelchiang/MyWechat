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
$mysql = new SaeMysql();
$query = "";
$mysql->getData($query);

?>
</body>
</html>