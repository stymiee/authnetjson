<?php

defined('ROOT_DIR') || define('ROOT_DIR', __DIR__);

spl_autoload_register(function ($class) {
    $class = explode('\\', $class);
    $class = end($class);
    $dirs = array('authnet', 'exceptions', 'interfaces');
    foreach($dirs as $dir) {
        $file = ROOT_DIR . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $class . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});