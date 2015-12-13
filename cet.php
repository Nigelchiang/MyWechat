<?php
session_start();
//查询数据库，openid是否已经存在
//用SAEMySQL的getdata方法，如果结果为空，会返回false，但没有错误
$openid             = $_GET['openid'];
$_SESSION['openid'] = $openid;
$mysql              = new SaeMysql();
$query              = "SELECT openid,name FROM wechat_user WHERE openid='$openid'";
//根据openid取出考号，以降序排列，默认查询考号最大的一次
$examid = "select examid from cet WHERE openid='$openid' ORDER BY examid DESC";
//从数组取出两个变量
$query  = $mysql->getLine($query);
$examid = $mysql->getVar($examid);

//数据库保存的openid
$openid_db          = $query['openid'];
$name               = $query['name'];
$_SESSION['examid'] = $examid;
$_SESSION['name']   = $name;
//var_dump($examid);
//var_dump($mysql->getLine($query));
//var_dump($mysql->getVar($query));
//sae_log(json_encode($openid . "-" . $examid));

//用户尚未注册
if (empty($openid_db)) {
    $signup = "INSERT INTO wechat_user (openid) VALUES ('$openid') ";
    $bool   = $mysql->runSql($signup);
    if (!$bool) {
        echo $debug = sprintf("注册失败 %d : %s", $mysql->errno(), $mysql->errmsg());
        sae_log($debug);
    } else {
        //        echo "你已成功注册" . "<br/>";
        //        echo "即将跳转到备份的页面";
        //        echo "if 111111";
        //跳到填写考号和姓名的页面
        header("Location:http://5.n1gel.sinaapp.com/cet_query.php");
        //填写之后，更新session，更新数据库
    }
    //用户已注册，为备份考号和姓名
} elseif (empty($examid)) {
    //    echo "即将跳转到备份的页面";
    //    echo "if 222222222";
    header("Location:http://5.n1gel.sinaapp.com/cet_query.php");
    //页面获取exanid和姓名直接查询，将查询结果存到数据库并显示到页面，生成一个模板图片，让用户保存
    //填写之后，更新session，更新数据库
} else {
    //直接获取session，执行查询就好
    //获取微信分享的接口
    //既然不能通过curl跳转，那就用curl把那个网页整个抓取过来好了，反正效果也是一样的
    $url   = "http://5.n1gel.sinaapp.com/cet_query.php?";
    $param = array(
        "isJump" => true,
        "examid" => $examid,
        "name"   => $name,
    );
    $url .= http_build_query($param);
    //    $post = array(
    //        "examid" => $examid,
    //        "name"   => $name,
    //    );
    //    $ch = curl_init();
    //    if(substr($url,0,5)=='https'){
    //        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    //        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
    //    }
    //    //这里并不是抓取数据，而是抓网页然后直接在这里显示，所以这一项就不设置了
    //    //但是抓取过来应该也是一个被服务器解释过的静态的网页呀，用PHP实现的动态功能都不会实现了呀
    //    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //    curl_setopt($ch, CURLOPT_URL, $url);
    ////    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    //    curl_setopt($ch, CURLOPT_POST, true);
    //    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    //    $response = curl_exec($ch);
    //    if($error=curl_error($ch)){
    //        die($error);
    //    }
    //    curl_close($ch);
    header("Location:" . $url);
}

$mysql->closeDb();
function sae_log($msg) {
    sae_set_display_errors(false);//关闭信息输出
    sae_debug($msg);//记录日志
    sae_set_display_errors(true);//记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
}

