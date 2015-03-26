<?php
    defined('AUTHNET_LOGIN')    || define('AUTHNET_LOGIN', '');
    defined('AUTHNET_TRANSKEY') || define('AUTHNET_TRANSKEY', '');

    if (!function_exists('curl_init')) {
        throw new \Exception('cURL PHP extension not installed');
    }
