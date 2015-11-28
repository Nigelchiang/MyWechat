<?php
require_once('php-saestorage-master/saestorage.class.php');
#your app accesskey
$ak = 'n353jmy031';
#your app secretkey
$sk = 'zwwkm3wjxmmkxkhwzlyjhxz3lh2xkyj3zhx014lh';
#your domain name
$domain = 'n1gel';
#your file name
$filename = 'emoji.txt';
$storage = new SaeStorage($ak, $sk);
$fileContent = $storage->read($domain,$filename);
var_dump($fileContent);
?>