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
echo "这里是成绩展示界面，会生成一张成绩的图片\n";
echo "openid".$openid;