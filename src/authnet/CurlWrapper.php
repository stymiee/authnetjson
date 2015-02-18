<?php

namespace JohnConde\Authnet;

class CurlWrapper Implements ProcessorInterface
{
    public function process($url, $json)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/json"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, SSL_CERT_DIR);

        if(($response = curl_exec($ch)) !== false) {
            curl_close($ch);
            unset($ch);
            return $response;
        }
        throw new AuthnetCurlException('Connection error: ' . curl_error($ch) . ' (' . curl_errno($ch) . ')');
    }

    public function setResponse($json)
    {

    }

    public function getName()
    {
        return __CLASS__;
    }
} 