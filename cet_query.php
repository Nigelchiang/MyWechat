<?php
session_start();
$openid = $_SESSION['openid'];

$examid = $_POST['examid'];
$name   = $_POST['name'];

$examidErr = $nameErr = '';
$is_examid = $is_name = false;
//region 输入验证
if (empty($examid)) {
    $examidErr = "请输入15位准考证号";
    ?>
    <script>
        alert("<?php echo $examidErr?>");
        document.getElementById("examid").focus();
    </script>
    <?php
} elseif (!test_examid($examid)) {
    $examidErr = "准考证号为15位数字";
    ?>
    <script>
        alert("<?php echo $examidErr?>");
        document.getElementById("examid").focus();
    </script>
    <?php
} else {
    $is_examid = true;
}
if (empty($name)) {
    $nameErr = "名字未填写";
    ?>
    <script>
        alert("<?php echo $nameErr?>");
    </script>
    <?php
} elseif (!test_name($name)) {
    $nameErr = "姓名为2~3个汉字";
    ?>
    <script>
        alert("<?php echo $nameErr?>");
    </script>
    <?php
} else {
    $is_name = true;
}
if ($is_examid && $is_name) {
    $_SESSION['examid'] = $examid;
    $_SESSION['name']   = $name;

    //更新数据库
    $mysql  = new SaeMysql();
    $update = "insert into cet (examid,name,openid) VALUES ('$examid','$name','$openid')";
    $mysql->runSql($update);
    sae_log("插入成绩出错" . $mysql->errmsg());
    $mysql->closeDb();
    //跳转
    header("Location:http://5.n1gel.sinaapp.com/cet_result.php");
}
//endregion
?>
    <!doctype html>
    <html lang="zh">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1"/>
        <title>四六级查分</title>
    </head>
    <body>
    2015年12月四六级考试成绩预计在2016年2月20-3月1左右发布，方方会第一时间提醒大家的~
    <!--可以用一个ajax，输入完成之后就发一个请求到服务器验证一下-->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for="examid">准考证号</label>
        <input type="text" name="examid" id="examid" placeholder="请输入15位准考证号" value="<?php echo $examid; ?>">
        <label for="name">姓名</label>
        <input type="text" name="name" id="name" placeholder="姓名超过3个字，可只输入前3个" value="<?php echo $name; ?>">
        <input type="submit" value="备份">
    </form>
    </body>
    </html>
<?php

function sae_log($msg) {
    sae_set_display_errors(false);//关闭信息输出
    sae_debug($msg);//记录日志
    sae_set_display_errors(true);//记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
}

function test_examid(&$input) {
    $input = htmlspecialchars(stripslashes(trim($input)));
    if (!preg_match("/^\d{15}$/", $input)) {
        return false;
    } else {
        return $input;
    }
}

function test_name(&$name) {
    $name = htmlspecialchars(stripslashes(trim($name)));
    //匹配2到3个中文
    $pattern = "/^[\x{4e00}-\x{9fa5}]{2,3}$/u";
    if (!preg_match($pattern, $name)) {
        return false;
    } else {
        return $name;
    }
}

?>