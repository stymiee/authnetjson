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

    protected function setUp()
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetWebServiceUrlProductionServer()
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
    public function testGetWebServiceUrlDevelopmentServer()
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
    public function testGetWebServiceUrlAkamaiServer()
    {
        $server           = AuthnetApiFactory::USE_AKAMAI_SERVER;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $server);

        $this->assertEquals($url, 'https://api2.authorize.net/xml/v1/request.api');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     * @expectedException \JohnConde\Authnet\AuthnetInvalidServerException
     */
    public function testGetWebServiceUrlBadServer()
    {
        $server           = 99;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke(null, $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetJsonRequest
     * @expectedException \JohnConde\Authnet\AuthnetInvalidCredentialsException
     */
    public function testExceptionIsRaisedForInvalidCredentialsLogin()
    {
        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getJsonApiHandler(null, $this->transactionKey, $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetJsonRequest
     * @expectedException \JohnConde\Authnet\AuthnetInvalidCredentialsException
     */
    public function testExceptionIsRaisedForInvalidCredentialsTransactionKey()
    {
        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getJsonApiHandler($this->login, null, $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @expectedException \JohnConde\Authnet\AuthnetInvalidServerException
     */
    public function testExceptionIsRaisedForAuthnetInvalidServer()
    {
        AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, null);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetJsonRequest
     */
    public function testCurlWrapperProductionResponse()
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
    public function testCurlWrapperDevelopmentResponse()
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
    public function testGetSimHandler()
    {
        $server = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $sim    = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $server);

        $this->assertInstanceOf('JohnConde\Authnet\AuthnetSim', $sim);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getSimHandler
     * @expectedException \JohnConde\Authnet\AuthnetInvalidCredentialsException
     */
    public function testExceptionIsRaisedForInvalidCredentialsSim()
    {
        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getSimHandler(null, $this->transactionKey, $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getSimURL
     */
    public function testGetSimServerTest()
    {
        $server           = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getSimURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $server);

        $this->assertEquals($url, 'https://test.authorize.net/gateway/transact.dll');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getSimURL
     */
    public function testGetSimServerProduction()
    {
        $server           = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getSimURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $server);

        $this->assertEquals($url, 'https://secure2.authorize.net/gateway/transact.dll');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getSimURL
     * @expectedException \JohnConde\Authnet\AuthnetInvalidServerException
     */
    public function testExceptionIsRaisedForAuthnetInvalidSimServer()
    {
        AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, null);
    }










    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @uses              \JohnConde\Authnet\AuthnetWebhooksRequest
     * @expectedException \JohnConde\Authnet\AuthnetInvalidCredentialsException
     */
    public function testExceptionIsRaisedForInvalidCredentialsLoginWebhooks()
    {
        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getWebhooksHandler(null, $this->transactionKey, $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @uses              \JohnConde\Authnet\AuthnetWebhooksRequest
     * @expectedException \JohnConde\Authnet\AuthnetInvalidCredentialsException
     */
    public function testExceptionIsRaisedForInvalidCredentialsTransactionKeyWebhooks()
    {
        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getWebhooksHandler($this->login, null, $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @expectedException \JohnConde\Authnet\AuthnetInvalidServerException
     */
    public function testExceptionIsRaisedForAuthnetInvalidServerWebhooks()
    {
        AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, null);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @uses              \JohnConde\Authnet\AuthnetWebhooksRequest
     */
    public function testCurlWrapperProductionResponseWebhooks()
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
    public function testCurlWrapperDevelopmentResponseWebhooks()
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
    public function testGetWebhooksUrlProductionServer()
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
    public function testGetWebhooksUrlDevelopmentServer()
    {
        $server           = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getWebhooksURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $server);

        $this->assertEquals($url, 'https://apitest.authorize.net/rest/v1/');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebhooksURL
     * @expectedException \JohnConde\Authnet\AuthnetInvalidServerException
     */
    public function testGetWebhooksUrlBadServer()
    {
        $server           = 99;
        $reflectionMethod = new \ReflectionMethod('\JohnConde\Authnet\AuthnetApiFactory', 'getWebhooksURL');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke(null, $server);
    }
}