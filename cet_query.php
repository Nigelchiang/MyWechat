<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2015/12/11
 * Time: 18:06
 */
session_start();
$openid = $_SESSION['openid'];

sae_log("这里是查询界面，会有两个输入框和一个查询按钮\n" . "openid:" . $openid);

//填完了之后会跳到结果界面
sleep(2);
header("Location:http://5.n1gel.sinaapp.com/cet_result.php");

function sae_log($msg) {
    sae_set_display_errors(false);//关闭信息输出
    sae_debug($msg);//记录日志
    sae_set_display_errors(true);//记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
}