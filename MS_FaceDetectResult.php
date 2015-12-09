<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nigel
 * Date: 2015/11/25
 * Time: 19:23
 */
/**
 * 调用微软的人脸识别API，通过PHP在人脸位置绘制矩形标识
 */
//sae_xhprof_start();
//取出URL中的数据放到数组中
parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $rectangle);
//通过cUrl下载的图片，放到imagecreatefromstring里，就可以了
$url = $_GET['$url'];


//sae_xhprof_end();
?>
