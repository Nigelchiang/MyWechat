<?php
//php的知识太少了，现在做点东西真的很吃力，我还是好好学学再来做这个规划工作吧
/**
 * Created by IntelliJ IDEA.
 * User: Nigel
 * Date: 2015/11/18
 * Time: 21:29
 */
public

class Robot {

    private $label;
    private $x;
    private $y;

    public function __construct($label = null, $x, $y) {

        $this->label = $label;
        $this->x     = $x;
        $this->y     = $y;
    }

    public function request($url, array) {

        function getToken($url) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $output = curl_exec($ch);
            curl_close($ch);

            return $output;
        }
    }

    /**
     * @param $info
     * @return mixed
     */
    public function robot($info) {

        include_once("wx_tpl.php");

        $param = ['key' => '08ad04b298923b29a203d0aca21a9779', 'info' => $info];
        $url   = "http://www.tuling123.com/openapi/api?" . http_build_query($param);


        $reply = json_decode(getToken($url), true);


        return $reply;

    }
}


