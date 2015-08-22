<?php
    /*
     * This file is NOT required to use the AuthnetJson package.
     * It exists solely for the examples to work "out of the box".
     */

    defined('AUTHNET_LOGIN')    || define('AUTHNET_LOGIN', 'cnpdev4289');
    defined('AUTHNET_TRANSKEY') || define('AUTHNET_TRANSKEY', 'SR2P8g4jdEn7vFLQ');

    if (version_compare(PHP_VERSION, '5.3.0') < 0) {
        throw new \Exception('AuthnetJson requires PHP 5.3 or greater');
    }

    if (!function_exists('curl_init')) {
        throw new \Exception('cURL PHP extension not installed');
    }
