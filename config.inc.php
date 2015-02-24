<?php
    defined('AUTHNET_LOGIN')    || define('AUTHNET_LOGIN', 'cnpdev4289');
    defined('AUTHNET_TRANSKEY') || define('AUTHNET_TRANSKEY', 'SR2P8g4jdEn7vFLQ');

    if (!function_exists('curl_init')) {
        throw new \Exception('cURL PHP extension not installed');
    }
