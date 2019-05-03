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

use PHPUnit\Framework\TestCase;

require(__DIR__ . '/../config.inc.php');

class AuthnetApiFactoryTest extends TestCase
{
    private $login;
    private $transactionKey;

    protected function setUp() : void
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetWebServiceUrlProductionServer() : void
    {
        $server           = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $server);

        $this->assertEquals($url, 'https://api.authorize.net/xml/v1/request.api');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetWebServiceUrlDevelopmentServer() : void
    {
        $server           = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $server);

        $this->assertEquals($url, 'https://apitest.authorize.net/xml/v1/request.api');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetWebServiceUrlAkamaiServer() : void
    {
        $server           = AuthnetApiFactory::USE_AKAMAI_SERVER;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $server);

        $this->assertEquals($url, 'https://api2.authorize.net/xml/v1/request.api');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     * @covers            \JohnConde\Authnet\AuthnetException::__construct()
     * @covers            \JohnConde\Authnet\AuthnetInvalidServerException::__construct()
     */
    public function testGetWebServiceUrlBadServer() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetInvalidServerException');

        $server           = 99;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke(null, $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @covers            \JohnConde\Authnet\AuthnetInvalidCredentialsException::__construct()
     * @uses              \JohnConde\Authnet\AuthnetJsonRequest
     */
    public function testExceptionIsRaisedForInvalidCredentialsLogin() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetInvalidCredentialsException');

        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getJsonApiHandler('', $this->transactionKey, $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @covers            \JohnConde\Authnet\AuthnetInvalidCredentialsException::__construct()
     * @uses              \JohnConde\Authnet\AuthnetJsonRequest
     */
    public function testExceptionIsRaisedForInvalidCredentialsTransactionKey() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetInvalidCredentialsException');

        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getJsonApiHandler($this->login, '', $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @covers            \JohnConde\Authnet\AuthnetInvalidServerException::__construct()
     */
    public function testExceptionIsRaisedForAuthnetInvalidServer() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetInvalidServerException');

        AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, 5);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetJsonRequest
     */
    public function testCurlWrapperProductionResponse() : void
    {
        $server  = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $server);

        $reflectionClass = new \ReflectionClass('\JohnConde\Authnet\AuthnetJsonRequest');
        $reflectionOfProcessor = $reflectionClass->getProperty('processor');
        $reflectionOfProcessor->setAccessible(true);
        $processor = $reflectionOfProcessor->getValue($authnet);

        $this->assertInstanceOf('\Curl\Curl', $processor);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetJsonRequest
     */
    public function testCurlWrapperDevelopmentResponse() : void
    {
        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $server);

        $reflectionClass = new \ReflectionClass('\JohnConde\Authnet\AuthnetJsonRequest');
        $reflectionOfProcessor = $reflectionClass->getProperty('processor');
        $reflectionOfProcessor->setAccessible(true);
        $processor = $reflectionOfProcessor->getValue($authnet);

        $this->assertInstanceOf('\Curl\Curl', $processor);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getSimHandler
     */
    public function testGetSimHandler() : void
    {
        $server = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $sim    = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $server);

        $this->assertInstanceOf('JohnConde\Authnet\AuthnetSim', $sim);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getSimHandler
     * @covers            \JohnConde\Authnet\AuthnetInvalidCredentialsException::__construct()
     */
    public function testExceptionIsRaisedForInvalidCredentialsSim() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetInvalidCredentialsException');

        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getSimHandler('', $this->transactionKey, $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getSimURL
     */
    public function testGetSimServerTest() : void
    {
        $server           = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getSimURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke($reflectionMethod, $server);

        $this->assertEquals($url, 'https://test.authorize.net/gateway/transact.dll');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getSimURL
     */
    public function testGetSimServerProduction() : void
    {
        $server           = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getSimURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke($reflectionMethod, $server);

        $this->assertEquals($url, 'https://secure2.authorize.net/gateway/transact.dll');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getSimURL
     * @covers            \JohnConde\Authnet\AuthnetInvalidServerException::__construct()
     */
    public function testExceptionIsRaisedForAuthnetInvalidSimServer() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetInvalidServerException');

        AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, 5);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @covers            \JohnConde\Authnet\AuthnetInvalidCredentialsException::__construct()
     * @uses              \JohnConde\Authnet\AuthnetWebhooksRequest
     */
    public function testExceptionIsRaisedForInvalidCredentialsLoginWebhooks() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetInvalidCredentialsException');

        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getWebhooksHandler('', $this->transactionKey, $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @covers            \JohnConde\Authnet\AuthnetInvalidCredentialsException::__construct()
     * @uses              \JohnConde\Authnet\AuthnetWebhooksRequest
     */
    public function testExceptionIsRaisedForInvalidCredentialsTransactionKeyWebhooks() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetInvalidCredentialsException');

        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getWebhooksHandler($this->login, '', $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @covers            \JohnConde\Authnet\AuthnetInvalidServerException::__construct()
     */
    public function testExceptionIsRaisedForAuthnetInvalidServerWebhooks() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetInvalidServerException');

        AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, 5);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @uses              \JohnConde\Authnet\AuthnetWebhooksRequest
     */
    public function testCurlWrapperProductionResponseWebhooks() : void
    {
        $server  = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $authnet = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $server);

        $reflectionClass = new \ReflectionClass('\JohnConde\Authnet\AuthnetWebhooksRequest');
        $reflectionOfProcessor = $reflectionClass->getProperty('processor');
        $reflectionOfProcessor->setAccessible(true);
        $processor = $reflectionOfProcessor->getValue($authnet);

        $this->assertInstanceOf('\Curl\Curl', $processor);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @uses              \JohnConde\Authnet\AuthnetWebhooksRequest
     */
    public function testCurlWrapperDevelopmentResponseWebhooks() : void
    {
        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $authnet = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $server);

        $reflectionClass = new \ReflectionClass('\JohnConde\Authnet\AuthnetWebhooksRequest');
        $reflectionOfProcessor = $reflectionClass->getProperty('processor');
        $reflectionOfProcessor->setAccessible(true);
        $processor = $reflectionOfProcessor->getValue($authnet);

        $this->assertInstanceOf('\Curl\Curl', $processor);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksURL
     */
    public function testGetWebhooksUrlProductionServer() : void
    {
        $server           = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getWebhooksURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $server);

        $this->assertEquals($url, 'https://api.authorize.net/rest/v1/');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksURL
     */
    public function testGetWebhooksUrlDevelopmentServer() : void
    {
        $server           = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getWebhooksURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $server);

        $this->assertEquals($url, 'https://apitest.authorize.net/rest/v1/');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksURL
     * @covers            \JohnConde\Authnet\AuthnetInvalidServerException::__construct()
     */
    public function testGetWebhooksUrlBadServer() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetInvalidServerException');

        $server           = 99;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getWebhooksURL');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke(null, $server);
    }
}