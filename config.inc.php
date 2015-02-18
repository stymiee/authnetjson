<?php
    defined('AUTHNET_LOGIN')    || define('AUTHNET_LOGIN', 'cnpdev4289');
    defined('AUTHNET_TRANSKEY') || define('AUTHNET_TRANSKEY', 'SR2P8g4jdEn7vFLQ');
    defined('SSL_CERT_DIR')     || define('SSL_CERT_DIR', __DIR__ . '/ssl/cert.pem');

    if (!function_exists('curl_init')) {
        throw new \Exception('CURL PHP extension not installed');
    }
