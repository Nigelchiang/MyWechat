<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>RegExp</title>
</head>
<body>
<p>
    <?php
    /**
     * email地址匹配
     * 特殊符号分为在方括号内和方括号外
     */
    /**
     * mb_eregi()
     * Regular expression match ignoring case with multibyte support
     * string  $pattern         The regular expression pattern.
     * string  $string          The string being searched.
     * array   $regs [optional] Contains a substring of the matched string.
     */
    $pattern = '^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-.]+$';
    echo mb_eregi($pattern, "n@1.com") ? "true" : "false";
    /**
     * ip地址匹配
     */
    /**
     * 手机号码匹配
     */
    /**
     *
     */
    $name = "test";
    function test() {

        $name = "inside";
        echo "inside the funciton \$name = " . $name . "<br />";
        include("wx_tpl.php");
        echo $test;
    }

    $name();
    ?>
</p>
</body>
</html>