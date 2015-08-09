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
    const USE_PRODUCTION_SERVER  = 0;

    /**
     * @const Indicates use of the development server
     */
    const USE_DEVELOPMENT_SERVER = 1;

    /**
     * @const Indicates use of the Akamai endpoint
     */
    const USE_AKAMAI_SERVER = 2;

    /**
     * @param   string      $login                          Authorize.Net API Login ID
     * @param   string      $transaction_key                Authorize.Net API Transaction Key
     * @param   integer     $server                         ID of which server to use (optional)
     * @return  object      \JohnConde\Authnet\AuthnetJson
     * @throws  \JohnConde\Authnet\AuthnetInvalidCredentialsException
     */
    public static function getJsonApiHandler($login, $transaction_key, $server = self::USE_PRODUCTION_SERVER)
    {
        $login           = trim($login);
        $transaction_key = trim($transaction_key);
        $api_url         = static::getWebServiceURL($server);

        if (empty($login) || empty($transaction_key)) {
            throw new AuthnetInvalidCredentialsException('You have not configured your login credentials properly.');
        }

        $object = new AuthnetJsonRequest($login, $transaction_key, $api_url);
        $object->setProcessHandler(new CurlWrapper());

        return $object;
    }

    /**
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
}