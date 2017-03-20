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

use \Curl\Curl;

/**
 * Factory to instantiate an instance of an AuthnetJson object with the proper endpoint
 * URL and Processor Class
 *
 * @package    AuthnetJSON
 * @author     John Conde <stymiee@gmail.com>
 * @copyright  John Conde <stymiee@gmail.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link       https://github.com/stymiee/authnetjson
 */

class AuthnetApiFactory
{
    /**
     * @const Indicates use of Authorize.Net's production server
     */
    const USE_PRODUCTION_SERVER = 0;

    /**
     * @const Indicates use of the development server
     */
    const USE_DEVELOPMENT_SERVER = 1;

    /**
     * @const Indicates use of the Akamai endpoint
     */
    const USE_AKAMAI_SERVER = 2;

    /**
     * Validates the Authorize.Net credentials and returns a Request object to be used to make an API call
     *
     * @param   string      $login                          Authorize.Net API Login ID
     * @param   string      $transaction_key                Authorize.Net API Transaction Key
     * @param   integer     $server                         ID of which server to use (optional)
     * @return  object      \JohnConde\Authnet\AuthnetJson
     * @throws  \JohnConde\Authnet\AuthnetInvalidCredentialsException
     */
    public static function getJsonApiHandler($login, $transaction_key, $server = self::USE_AKAMAI_SERVER)
    {
        $login           = trim($login);
        $transaction_key = trim($transaction_key);
        $api_url         = static::getWebServiceURL($server);

        if (empty($login) || empty($transaction_key)) {
            throw new AuthnetInvalidCredentialsException('You have not configured your login credentials properly.');
        }

        $curl = new Curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_HEADER, false);
        $curl->setHeader('Content-Type', 'text/json');

        $object = new AuthnetJsonRequest($login, $transaction_key, $api_url);
        $object->setProcessHandler($curl);

        return $object;
    }

    /**
     * Gets the API endpoint to be used for a JSON API call
     *
     * @param   integer     $server     ID of which server to use
     * @return  string                  The URL endpoint the request is to be sent to
     * @throws  \JohnConde\Authnet\AuthnetInvalidServerException
     */
    protected static function getWebServiceURL($server)
    {
        if ($server === static::USE_PRODUCTION_SERVER) {
            $url = 'https://api.authorize.net/xml/v1/request.api';
        }
        else if ($server === static::USE_DEVELOPMENT_SERVER) {
            $url = 'https://apitest.authorize.net/xml/v1/request.api';
        }
        else if ($server === static::USE_AKAMAI_SERVER) {
            $url = 'https://api2.authorize.net/xml/v1/request.api';
        }
        else {
            throw new AuthnetInvalidServerException('You did not provide a valid server.');
        }
        return $url;
    }

    /**
     * Validates the Authorize.Net credentials and returns a SIM object to be used to make a SIM API call
     *
     * @param   string      $login                          Authorize.Net API Login ID
     * @param   string      $transaction_key                Authorize.Net API Transaction Key
     * @param   integer     $server                         ID of which server to use (optional)
     * @return  object      \JohnConde\Authnet\AuthnetSim
     * @throws  \JohnConde\Authnet\AuthnetInvalidCredentialsException
     */
    public static function getSimHandler($login, $transaction_key, $server = self::USE_PRODUCTION_SERVER)
    {
        $login           = trim($login);
        $transaction_key = trim($transaction_key);
        $api_url         = static::getSimURL($server);

        if (empty($login) || empty($transaction_key)) {
            throw new AuthnetInvalidCredentialsException('You have not configured your login credentials properly.');
        }

        return new AuthnetSim($login, $transaction_key, $api_url);
    }

    /**
     * Gets the API endpoint to be used for a SIM API call
     *
     * @param   integer     $server     ID of which server to use
     * @return  string                  The URL endpoint the request is to be sent to
     * @throws  \JohnConde\Authnet\AuthnetInvalidServerException
     */
    protected static function getSimURL($server)
    {
        if ($server === static::USE_PRODUCTION_SERVER) {
            $url = 'https://secure2.authorize.net/gateway/transact.dll';
        }
        else if ($server === static::USE_DEVELOPMENT_SERVER) {
            $url = 'https://test.authorize.net/gateway/transact.dll';
        }
        else {
            throw new AuthnetInvalidServerException('You did not provide a valid server.');
        }
        return $url;
    }

    /**
     * Validates the Authorize.Net credentials and returns a Webhooks Request object to be used to make an Webhook call
     *
     * @param   string      $login                          Authorize.Net API Login ID
     * @param   string      $transaction_key                Authorize.Net API Transaction Key
     * @param   integer     $server                         ID of which server to use (optional)
     * @return  object      \JohnConde\Authnet\AuthnetJson
     * @throws  \JohnConde\Authnet\AuthnetInvalidCredentialsException
     */
    public static function getWebhooksHandler($login, $transaction_key, $server = self::USE_PRODUCTION_SERVER)
    {
        $login           = trim($login);
        $transaction_key = trim($transaction_key);
        $api_url         = static::getWebhooksURL($server);

        if (empty($login) || empty($transaction_key)) {
            throw new AuthnetInvalidCredentialsException('You have not configured your login credentials properly.');
        }

        $base64credentials = base64_encode(sprintf('%s:%s', $login, $transaction_key));

        $curl = new Curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_HEADER, false);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Authorization', sprintf('Basic %s', $base64credentials));

        $object = new AuthnetWebhooksRequest($api_url);
        $object->setProcessHandler($curl);

        return $object;
    }

    /**
     * Gets the API endpoint to be used for a SIM API call
     *
     * @param   integer     $server     ID of which server to use
     * @return  string                  The URL endpoint the request is to be sent to
     * @throws  \JohnConde\Authnet\AuthnetInvalidServerException
     */
    protected static function getWebhooksURL($server)
    {
        if ($server === static::USE_PRODUCTION_SERVER) {
            $url = 'https://api.authorize.net/rest/v1/';
        }
        else if ($server === static::USE_DEVELOPMENT_SERVER) {
            $url = 'https://apitest.authorize.net/rest/v1/';
        }
        else {
            throw new AuthnetInvalidServerException('You did not provide a valid server.');
        }
        return $url;
    }
}