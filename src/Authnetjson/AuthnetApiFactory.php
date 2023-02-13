<?php

declare(strict_types=1);

/**
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Authnetjson;

use Authnetjson\Exception\AuthnetInvalidCredentialsException;
use Authnetjson\Exception\AuthnetInvalidServerException;
use Curl\Curl;
use Exception;

/**
 * Factory to instantiate an instance of an AuthnetJson object with the proper endpoint
 * URL and Processor Class.
 *
 * @author    John Conde <stymiee@gmail.com>
 * @copyright 2015 - 2023 John Conde <stymiee@gmail.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 *
 * @link https://github.com/stymiee/authnetjson
 */
class AuthnetApiFactory
{
    /**
     * @const Indicates use of Authorize.Net's production server
     */
    public const USE_PRODUCTION_SERVER = 0;

    /**
     * @const Indicates use of the development server
     */
    public const USE_DEVELOPMENT_SERVER = 1;

    /**
     * @const Indicates use of the CDN endpoint
     */
    public const USE_CDN_SERVER = 2;

    /**
     * Validates the Authorize.Net credentials and returns a Request object to be used to make an API call.
     *
     * @param string $login Authorize.Net API Login ID
     * @param string $transaction_key Authorize.Net API Transaction Key
     * @param int|null $endpoint ID of which endpoint to use (optional)
     * @return AuthnetJsonRequest
     * @throws AuthnetInvalidCredentialsException
     * @throws AuthnetInvalidServerException
     */
    public static function getJsonApiHandler(string $login, string $transaction_key, ?int $endpoint = null): object
    {
        $login = trim($login);
        $transaction_key = trim($transaction_key);
        $endpoint = $endpoint ?? self::USE_CDN_SERVER;
        $api_url = static::getWebServiceURL($endpoint);

        if (empty($login) || empty($transaction_key)) {
            throw new AuthnetInvalidCredentialsException('You have not configured your login credentials properly.');
        }

        $curl = new Curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_HEADER, false);
        $curl->setOpt(CURLOPT_TIMEOUT, 10);
        $curl->setOpt(CURLOPT_CONNECTTIMEOUT, 10);
        $curl->setHeader('Content-Type', 'text/json');

        $object = new AuthnetJsonRequest($login, $transaction_key, $api_url);
        $object->setProcessHandler($curl);

        return $object;
    }

    /**
     * Gets the API endpoint to be used for a JSON API call.
     *
     * @param int $server ID of which server to use
     * @return string                  The URL endpoint the request is to be sent to
     * @throws AuthnetInvalidServerException
     */
    protected static function getWebServiceURL(int $server): string
    {
        $urls = [
            static::USE_PRODUCTION_SERVER  => 'https://api.authorize.net/xml/v1/request.api',
            static::USE_DEVELOPMENT_SERVER => 'https://apitest.authorize.net/xml/v1/request.api',
            static::USE_CDN_SERVER         => 'https://api2.authorize.net/xml/v1/request.api',
        ];
        if (array_key_exists($server, $urls)) {
            return $urls[$server];
        }
        throw new AuthnetInvalidServerException('You did not provide a valid server.');
    }

    /**
     * Validates the Authorize.Net credentials and returns a SIM object to be used to make a SIM API call.
     *
     * @param string $login Authorize.Net API Login ID
     * @param string $transaction_key Authorize.Net API Transaction Key
     * @param int|null $server ID of which server to use (optional)
     * @return AuthnetSim
     * @throws AuthnetInvalidCredentialsException
     * @throws AuthnetInvalidServerException
     * @throws Exception
     */
    public static function getSimHandler(string $login, string $transaction_key, ?int $server = null): object
    {
        $login = trim($login);
        $transaction_key = trim($transaction_key);
        $server = $server ?? self::USE_PRODUCTION_SERVER;
        $api_url = static::getSimURL($server);

        if (empty($login) || empty($transaction_key)) {
            throw new AuthnetInvalidCredentialsException('You have not configured your login credentials properly.');
        }

        return new AuthnetSim($login, $transaction_key, $api_url);
    }

    /**
     * Gets the API endpoint to be used for a SIM API call.
     *
     * @param int $server ID of which server to use
     * @return string                  The URL endpoint the request is to be sent to
     * @throws AuthnetInvalidServerException
     */
    protected static function getSimURL(int $server): string
    {
        $urls = [
            static::USE_PRODUCTION_SERVER  => 'https://secure2.authorize.net/gateway/transact.dll',
            static::USE_DEVELOPMENT_SERVER => 'https://test.authorize.net/gateway/transact.dll',
        ];
        if (array_key_exists($server, $urls)) {
            return $urls[$server];
        }
        throw new AuthnetInvalidServerException('You did not provide a valid server.');
    }

    /**
     * Validates the Authorize.Net credentials and returns a Webhooks Request object to be used to make a Webhook call.
     *
     * @param string $login Authorize.Net API Login ID
     * @param string $transaction_key Authorize.Net API Transaction Key
     * @param int|null $server ID of which server to use (optional)
     * @return AuthnetWebhooksRequest
     * @throws AuthnetInvalidCredentialsException
     * @throws AuthnetInvalidServerException
     */
    public static function getWebhooksHandler(string $login, string $transaction_key, ?int $server = null): object
    {
        $login = trim($login);
        $transaction_key = trim($transaction_key);
        $server = $server ?? self::USE_PRODUCTION_SERVER;
        $api_url = static::getWebhooksURL($server);

        if (empty($login) || empty($transaction_key)) {
            throw new AuthnetInvalidCredentialsException('You have not configured your login credentials properly.');
        }

        $base64credentials = base64_encode(sprintf('%s:%s', $login, $transaction_key));

        $curl = new Curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_HEADER, false);
        $curl->setOpt(CURLOPT_TIMEOUT, 10);
        $curl->setOpt(CURLOPT_CONNECTTIMEOUT, 10);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Authorization', sprintf('Basic %s', $base64credentials));

        $object = new AuthnetWebhooksRequest($api_url);
        $object->setProcessHandler($curl);

        return $object;
    }

    /**
     * Gets the API endpoint to be used for a SIM API call.
     *
     * @param int $server ID of which server to use
     * @return string                  The URL endpoint the request is to be sent to
     * @throws AuthnetInvalidServerException
     */
    protected static function getWebhooksURL(int $server): string
    {
        $urls = [
            static::USE_PRODUCTION_SERVER  => 'https://api.authorize.net/rest/v1/',
            static::USE_DEVELOPMENT_SERVER => 'https://apitest.authorize.net/rest/v1/',
        ];
        if (array_key_exists($server, $urls)) {
            return $urls[$server];
        }
        throw new AuthnetInvalidServerException('You did not provide a valid server.');
    }
}
