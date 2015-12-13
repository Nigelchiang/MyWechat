<?php

/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2015/12/9
 * Time: 9:32
 */
class Face {

    /**
     * 图片URL
     * @var string
     */
    private $url;
    /**
     * 图片资源
     * @var resource
     */
    private $img;
    /**
     * 图片识别的数据
     * @var array
     */
    private $info;
    /**
     * 在storage中保存的URL
     * @var string
     */
    private $newUrl;

    function __construct($url) {
        $this->url  = $url;
        $this->info = $this->detect();
        $this->img  = &$this->getImg();
        $this->draw();
    }

    /**
     * 通过cUrl下载图片
     * @return resource 创建的图片资源
     */
    private function getImg() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // good edit, thanks!
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); // also, this seems wise considering output is image.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        if ($data === false) {
            $this->log("curl获取图片失败" . curl_errno($ch) . " : " . curl_error($ch));
        }
        curl_close($ch);

        $img = imagecreatefromstring($data);
        if ($img === false) {
            $this->log("图片创建失败");
        }

        return $img;
    }

    /**
     * 调用微软人脸识别API
     * @return array 失败返回false
     */
    public function detect() {
        $faceKey    = "dd3e5074da61435dab8dc8b001ff1b2f";
        $requestUrl = "https://api.projectoxford.ai/face/v0/detections?analyzesFaceLandmarks=true&analyzesAge=true&analyzesGender=true";
        $data       = array("url" => $this->url);
        $data       = json_encode($data);
        $header     = array(
            'Content-Type:application/json',
            'Ocp-Apim-Subscription-Key:' . $faceKey,
            'Content-Length:' . strlen($data));
        try {
            $ch = curl_init();
            if (false === $ch) {
                throw new Exception('failed to initialize');
            }

            curl_setopt($ch, CURLOPT_URL, $requestUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            //信任任何ssl证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $output = curl_exec($ch);
            if (false === $output) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            $output = json_decode($output, true);

            if (!isset($output['code'])) {
                return $output;
            }
            // ...process $content now
        } catch (Exception $e) {

            trigger_error(sprintf(
                              'Curl failed with error #%d: %s',
                              $e->getCode(), $e->getMessage()),
                          E_USER_ERROR);

        }

        return false;
    }


    public function draw() {

        foreach ($this->info as $rec) {
            $x1     = $rec['faceRectangle']['left'];
            $y1     = $rec['faceRectangle']['top'];
            $width  = $rec['faceRectangle']['width'];
            $height = $rec['faceRectangle']['height'];
            $gender = $rec['attributes']['gender'];
            $x2     = $x1 + $width;
            $y2     = $y1 + $height;
            //拆分数组,取出需要的五个点坐标
            $points = array_slice($rec['faceLandmarks'], 0, 5);


            $color_male   = imagecolorallocate($this->img, 13, 163, 238);
            $color_female = imagecolorallocate($this->img, 186, 11, 147);
            $color        = ($gender == "male") ? $color_male : $color_female;
            //设置笔画的粗细
            imagesetthickness($this->img, 3);
            //画一个矩形
            $bool = imagerectangle($this->img, $x1, $y1, $x2, $y2, $color);
            //画点
            foreach ($points as $point) {
                $bool = imagefilledrectangle($this->img, $point['x'] - 7, $point['y'] - 7, $point['x'] + 7,
                                             $point['y'] + 7,
                                             $color);
            }

            if (!$bool) {
                $this->log("画图失败");
            }
        }
    }

    /**
     * 将图片资源保存到storage
     * @returns string storage中图片的URL
     */
    public function save() {
        //给文件名添加随机的后缀，防止重复
        $random   = mt_rand();
        $filename = substr(str_replace("/", "", parse_url($this->url, PHP_URL_PATH)), -10) . $random . ".jpg";
        //just for test
        //$stor = new SaeStorage("n353jmy031","zwwkm3wjxmmkxkhwzlyjhxz3lh2xkyj3zhx014lh");

        /*  imagepng这样的函数不支持wrapper,用临时文件解决
            imageX 第二个参数指定filename，将文件保存到一个地址而不是输出到浏览器
            使用sae storage的wrapper来保存图片
            file_put_contents("saestor://n/test.txt", "haha");

            保存为临时文件
            $bool = imagejpeg($img, SAE_TMP_PATH . $filename);
            imagedestroy($img);

            $this-log("保存的文件名：" . $filename);
            从临时文件里取出，保存到storage里
            file_put_contents("saestor://wechatimg/$filename",
                              file_get_contents(SAE_TMP_PATH . $filename));*/

        /*
         * 新的文件保存方法 用缓存来实现，这个方法应该会快很多，因为减少了两个特别慢的函数
         */
        $domain = "wechatimg";
        $stor   = new SaeStorage();
        ob_start();
        imagejpeg($this->img);
        $imgstr = ob_get_contents();
        $bool   = $stor->write($domain, $filename, $imgstr);
        ob_end_clean();
        imagedestroy($this->img);
        if (!$bool) {
            $this->log("保存文件失败");
        }
        $this->newUrl = $stor->getUrl($domain, $filename);

        return $this->newUrl;

    }

    function __get($name) {
        return $this->$name;
    }

    /**
     * SAE调试 在日志中心选择错误日志查看
     * @param $msg string
     */
    private function log($msg) {
        sae_set_display_errors(false);//关闭信息输出
        sae_debug($msg);//记录日志
        sae_set_display_errors(true);//记录日志后再打开信息输出，否则会阻止正常的错误信息的显示
    }
}