<?php

/**
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
     * @var     resource  cURL resource
     */
    protected $ch;

    /**
     * Makes the API request and handles any communication errors
     *
     * @param   string  $url    The URL to connect to process a transaction
     * @param   string  $json   A JSON response to be sent as payload
     * @return  string          A JSON response string
     * @throws  \JohnConde\Authnet\AuthnetCurlException
     */
    public function process($url, $json)
    {
        $response = $this->makeRequest($url, $json);
        if($response !== false) {
            return $response;
        }
        $error = null;
        $errno = null;
        if ($this->ch) {
            $error = curl_error($this->ch);
            $errno = curl_errno($this->ch);
        }
        throw new AuthnetCurlException('Connection error: ' . $error . ' (' . $errno . ')');
    }

    /**
     * Uses cURL to send the request to the Authorize.Net endpoint and receive their response
     *
     * @param   string  $url    The URL to connect to process a transaction
     * @param   string  $json   A JSON response to be sent as payload
     * @return  string          A JSON response string
     * @codeCoverageIgnore
     */
    protected function makeRequest($url, $json)
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array("Content-Type: text/json"));
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($this->ch);
        curl_close($this->ch);

        return $response;
    }
}