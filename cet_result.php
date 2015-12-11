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
echo "openid".$openid."<br>";
echo "examid: ".$examid."<br>";
echo "name: ".$name."<br/>";


function sae_log($msg) {
    sae_set_display_errors(false);//关闭信息输出
    sae_debug($msg);//记录日志
    sae_set_display_errors(true);//记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
}