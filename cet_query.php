<?php
session_start();
$openid = $_SESSION['openid'];
$examid = $_POST['examid'];
$name   = $_POST['name'];

//判断是否已经备份过
if (isset($_GET['isJump'])) {
    $examid = $_GET['examid'];
    $name   = $_GET['name'];
}

$examidErr = $nameErr = '';
$is_examid = $is_name = false;
//region 输入验证
//防止第一次进入就报错
//从cet.php跳转过来，设置了session，这里就不执行，也就不会跳到result页面，用户点击提交之后就会更新session，并跳转
//但是用session无法实现下面的表单自动填写
//另外的方法就是，用curl发送post，在header里面加上location，再加上一个校验域
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        $mysql     = new SaeMysql();
        $update    = "insert into cet (examid,name,openid) VALUES ('$examid','$name','$openid')";
        $updata_wx = "update wechat_user set name='$name' WHERE openid='$openid'";
        $bool      = $mysql->runSql($update);
        $bool      = $mysql->runSql($updata_wx);
        if (!$bool) {
            sae_log("插入考号姓名出错" . $mysql->errmsg());
        }
        $mysql->closeDb();
        //跳转
        header("Location:http://5.n1gel.sinaapp.com/cet_result.php");
    }
}
//endregion
?>
    <!doctype>
    <html lang="zh">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1"/>
        <link rel="stylesheet" href="http://cdn.rmbz.net/bootstrap-3.3.5-dist/css/bootstrap.min.css">
        <!--        <script src="jquery-2.1.4.min.js"></script>-->
        <title>四六级查分</title>
    </head>
    <body>

    <!--可以用一个ajax，输入完成之后就发一个请求到服务器验证一下-->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="submit">
        <div class="alert alert-info">
            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
            考试成绩预计在2016年2月20-3月1左右发布<br/>
            &nbsp;&nbsp;每个微信号可以备份多个考号~
        </div>
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon">考号</div>
                <input type="text" required maxlength="15" name="examid" class="form-control" placeholder="请输入15位准考证号"
                       value="<?php echo $examid; ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon">姓名</div>
                <input type="text" required maxlength="3" name="name" class="form-control" placeholder="姓名只输入前3个字"
                       value="<?php echo $name; ?>">
            </div>
        </div>
        <button type="button" class="btn btn-primary btn-block" onclick="onSubmit();">备份</button>
    </form>
    <!--自定义了一个漂亮的button，给他绑定一个点击事件，点击之后，找到这个表单，然后调用表单的提交事件，OK啦，前后端完美结合。-->
    <script>
        function onSubmit() {
            document.getElementById('submit').submit();
        }
    </script>
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