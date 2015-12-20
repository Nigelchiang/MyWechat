<?php

spl_autoload_register(function ($class) {

    if (false !== stripos($class, 'Overtrue\Wechat')) {
        //以绝对路径来包含
        require_once __DIR__ . '/src/' . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 8)) . '.php';
    } elseif ($class === 'Face') {
        require_once 'Face.php';
    }
});