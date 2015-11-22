<?php namespace nigel; ?>
<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Test</title>
    <style>
        body {
            background-color: #fff;
            color: #222;
            font-family: sans-serif;
        }

        pre {
            margin: 0;
            font-family: monospace;
        }

        a:link {
            color: #009;
            text-decoration: none;
            background-color: #fff;
        }

        a:hover {
            text-decoration: underline;
        }

        table {
            border-collapse: collapse;
            border: 0;
            width: 934px;
            box-shadow: 1px 2px 3px #ccc;
        }

        .center {
            text-align: center;
        }

        .center table {
            margin: 1em auto;
            text-align: left;
        }

        .center th {
            text-align: center !important;
        }

        td, th {
            border: 1px solid #666;
            font-size: 75%;
            vertical-align: baseline;
            padding: 4px 5px;
        }

        h1 {
            font-size: 150%;
        }

        h2 {
            font-size: 125%;
        }

        .p {
            text-align: left;
        }

        .e {
            background-color: #ccf;
            width: 300px;
            font-weight: bold;
        }

        .h {
            background-color: #99c;
            font-weight: bold;
        }

        .v {
            background-color: #ddd;
            max-width: 300px;
            overflow-x: auto;
        }

        .v i {
            color: #999;
        }

        img {
            float: right;
            border: 0;
        }

        hr {
            width: 934px;
            background-color: #ccc;
            border: 0;
            height: 1px;
        }
    </style>
</head>
<body>
<?php
header('Content-type:text/html;charset=utf-8');

define('BR', "<br />");
define('HR', "<hr />");

/**
 * Class Test
 */
class Test {

    private $name;
    private $age;
    private $content;

    /**
     * 构造函数
     * @param $a string
     */
    public function __construct($a) {

        $this->name    = $a;
        $this->age     = 18;
        $this->content = "这是我的$this->name" . BR . "我今年$this->age 啦！" . BR;
    }

    /**
     * called when instance was destroied
     */
    public function __destruct() {

        echo "I am being destroied!" . BR;
    }

    /**
     * 魔术方法 __get() __set()
     * 一个方法就解决了封装中的数据域读取问题，取代了Java里多余的很多get和set方法
     * 但是怎么控制哪些可以被get和set呢？在函数里面判断吗？
     * @param $property
     * @return mixed
     */
    public function __get($property) {

        if ($property !== "age")
            return $this->$property;
    }

    public function __set($property, $value) {

        if ($property != "name") {
            $this->$property = $value;
        }
    }

    /**
     * method of class
     */
    public function _do() {

        new Test("第二个类");
        //直接调用构造函数不会生成对象
        $this->__construct("new test.");
        echo "A method of Test class" . BR;
    }
}

/**
 * Class E_Test
 */
class E_Test extends Test {

    /**
     * 查询魔术变量及超级全局变量的值
     */
    public static function info() {

        echo "<div class='center'>";
        //echo get_class() . BR; //=>E_Test
        echo "<table>";
        echo "<h1>Magic Constants</h1>";
        echo "<tr><td class='e'>__CLASS</td><td class='v'>" . __CLASS__ . "</td></tr>";
        echo "<tr><td class='e'>__DIR__</td><td class='v'>" . __DIR__ . "</td></tr>";
        echo "<tr><td class='e'>__FILE__</td><td class='v'>" . __FILE__ . "</td></tr>";
        echo "<tr><td class='e'>__FUNCTION__</td><td class='v'>" . __FUNCTION__ . "</td></tr>";
        echo "<tr><td class='e'>__LINE__</td><td class='v'>" . __LINE__ . "</td></tr>";
        echo "<tr><td class='e'>__METHOD__</td><td class='v'>" . __METHOD__ . "</td></tr>";
        echo "<tr><td class='e'>__NAMESPACE__</td><td class='v'>" . __NAMESPACE__ . "</td></tr>";
        echo "<tr><td class='e'>__TRAIT__</td><td class='v'>" . __TRAIT__ . "</td></tr>";
        echo "</table>";
        echo "<h1>\$_GET</h1>";
        static::show($_GET);
        echo "<h1>\$_POST</h1>";
        self::show($_POST);
        echo "<h1>\$_REQUEST</h1>";
        self::show($_REQUEST);
        echo "<h1>\$_SERVER</h1>";
        self::show($_SERVER);
        if ($_ENV) {
            echo "<h1>\$_ENV</h1>";
            self::show($_ENV);
        }
        if ($_SESSION) {
            echo "<h1>\$_SESSION</h1>";
            self::show($_SESSION);
        }
        echo "<h1>\$_FILES</h1>";
        self::show($_FILES);
        echo "<h1>\$_COOKIE</h1>";
        self::show($_COOKIE);
        echo "</div>";
    }

    public static function show($name) {

        echo "<table>";
        foreach ($name as $key => $value) {
            echo "<tr><td class='e'>$key</td><td class='v'>" . $value . "</td></tr>";
        }
        //        print_r($name);
        echo "</table>";
    }

    /**
     * 尝试使用curl抓取页面
     */

    public static function cUrl() {

        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, 'http://www.nigel.top');
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        print_r($data);


    }
}

E_Test::info();
//E_Test::cUrl();

?>

</body>
</html>