<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace JohnConde\Authnet;

/**
 * Wrapper for cURL
 *
 * @package    AuthnetJSON
 * @author     John Conde <stymiee@gmail.com>
 * @copyright  John Conde <stymiee@gmail.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link       https://github.com/stymiee/authnetjson
 */
class CurlWrapper
{
    /**
     * @param   string  $url    The URL to connect to process a transaction
     * @param   string  $json   A JSON response to be sent as payload
     * @return  string          A JSON response string
     * @throws  \JohnConde\Authnet\AuthnetCurlException
     */
    public function process($url, $json)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/json"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if(($response = curl_exec($ch)) !== false) {
            curl_close($ch);
            unset($ch);
            return $response;
        }
        throw new AuthnetCurlException('Connection error: ' . curl_error($ch) . ' (' . curl_errno($ch) . ')');
    }
} 