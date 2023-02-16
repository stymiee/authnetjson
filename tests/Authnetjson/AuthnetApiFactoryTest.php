<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnConde\Authnet\tests;

use JohnConde\Authnet\AuthnetApiFactory;
use JohnConde\Authnet\AuthnetJsonRequest;
use JohnConde\Authnet\AuthnetSim;
use JohnConde\Authnet\AuthnetWebhooksRequest;
use JohnConde\Authnet\Exception\AuthnetInvalidCredentialsException;
use JohnConde\Authnet\Exception\AuthnetInvalidServerException;
use PHPUnit\Framework\TestCase;
use Curl\Curl;
use ReflectionClass;
use ReflectionMethod;

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
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetWebServiceUrlProductionServer()
    {
        $endpoint         = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $endpoint);

        self::assertEquals('https://api.authorize.net/xml/v1/request.api', $url);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetWebServiceUrlDevelopmentServer()
    {
        $endpoint         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $endpoint);

        self::assertEquals('https://apitest.authorize.net/xml/v1/request.api', $url);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetWebServiceUrlCDNServer()
    {
        $endpoint         = AuthnetApiFactory::USE_CDN_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $endpoint);

        self::assertEquals('https://api2.authorize.net/xml/v1/request.api', $url);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     * @covers \JohnConde\Authnet\Exception\AuthnetException::__construct()
     * @covers \JohnConde\Authnet\Exception\AuthnetInvalidServerException::__construct()
     */
    public function testGetWebServiceUrlBadServer()
    {
        $this->expectException(AuthnetInvalidServerException::class);

        $endpoint         = 99;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke(null, $endpoint);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @covers \JohnConde\Authnet\Exception\AuthnetInvalidCredentialsException::__construct()
     * @uses \JohnConde\Authnet\AuthnetJsonRequest
     */
    public function testExceptionIsRaisedForInvalidCredentialsLogin()
    {
        $this->expectException(AuthnetInvalidCredentialsException::class);

        $endpoint  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getJsonApiHandler('', $this->transactionKey, $endpoint);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @covers \JohnConde\Authnet\Exception\AuthnetInvalidCredentialsException::__construct()
     * @uses \JohnConde\Authnet\AuthnetJsonRequest
     */
    public function testExceptionIsRaisedForInvalidCredentialsTransactionKey()
    {
        $this->expectException(AuthnetInvalidCredentialsException::class);

        $endpoint  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getJsonApiHandler($this->login, '', $endpoint);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @covers \JohnConde\Authnet\Exception\AuthnetInvalidServerException::__construct()
     */
    public function testExceptionIsRaisedForAuthnetInvalidServer()
    {
        $this->expectException(AuthnetInvalidServerException::class);

        AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, 5);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses \JohnConde\Authnet\AuthnetJsonRequest
     */
    public function testCurlWrapperProductionResponse()
    {
        $endpoint = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $authnet  = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $endpoint);

        $reflectionClass = new ReflectionClass(AuthnetJsonRequest::class);
        $reflectionOfProcessor = $reflectionClass->getProperty('processor');
        $reflectionOfProcessor->setAccessible(true);
        $processor = $reflectionOfProcessor->getValue($authnet);

        self::assertInstanceOf(Curl::class, $processor);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses \JohnConde\Authnet\AuthnetJsonRequest
     */
    public function testCurlWrapperDevelopmentResponse()
    {
        $endpoint = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $authnet  = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $endpoint);

        $reflectionClass = new ReflectionClass(AuthnetJsonRequest::class);
        $reflectionOfProcessor = $reflectionClass->getProperty('processor');
        $reflectionOfProcessor->setAccessible(true);
        $processor = $reflectionOfProcessor->getValue($authnet);

        self::assertInstanceOf(Curl::class, $processor);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getSimHandler
     */
    public function testGetSimHandler()
    {
        $endpoint = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $sim    = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $endpoint);

        self::assertInstanceOf(AuthnetSim::class, $sim);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getSimHandler
     * @covers \JohnConde\Authnet\Exception\AuthnetInvalidCredentialsException::__construct()
     */
    public function testExceptionIsRaisedForInvalidCredentialsSim()
    {
        $this->expectException(AuthnetInvalidCredentialsException::class);

        $endpoint  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getSimHandler('', $this->transactionKey, $endpoint);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getSimURL
     */
    public function testGetSimServerTest()
    {
        $endpoint         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getSimURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke($reflectionMethod, $endpoint);

        self::assertEquals('https://test.authorize.net/gateway/transact.dll', $url);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getSimURL
     */
    public function testGetSimServerProduction()
    {
        $endpoint         = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getSimURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke($reflectionMethod, $endpoint);

        self::assertEquals('https://secure2.authorize.net/gateway/transact.dll', $url);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getSimURL
     * @covers \JohnConde\Authnet\Exception\AuthnetInvalidServerException::__construct()
     */
    public function testExceptionIsRaisedForAuthnetInvalidSimServer()
    {
        $this->expectException(AuthnetInvalidServerException::class);

        AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, 5);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @covers \JohnConde\Authnet\Exception\AuthnetInvalidCredentialsException::__construct()
     * @uses \JohnConde\Authnet\AuthnetWebhooksRequest
     */
    public function testExceptionIsRaisedForInvalidCredentialsLoginWebhooks()
    {
        $this->expectException(AuthnetInvalidCredentialsException::class);

        $endpoint  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getWebhooksHandler('', $this->transactionKey, $endpoint);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @covers \JohnConde\Authnet\Exception\AuthnetInvalidCredentialsException::__construct()
     * @uses \JohnConde\Authnet\AuthnetWebhooksRequest
     */
    public function testExceptionIsRaisedForInvalidCredentialsTransactionKeyWebhooks()
    {
        $this->expectException(AuthnetInvalidCredentialsException::class);

        $endpoint = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getWebhooksHandler($this->login, '', $endpoint);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @covers \JohnConde\Authnet\Exception\AuthnetInvalidServerException::__construct()
     */
    public function testExceptionIsRaisedForAuthnetInvalidServerWebhooks()
    {
        $this->expectException(AuthnetInvalidServerException::class);

        AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, 5);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @uses \JohnConde\Authnet\AuthnetWebhooksRequest
     */
    public function testCurlWrapperProductionResponseWebhooks()
    {
        $endpoint = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $authnet  = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $endpoint);

        $reflectionClass = new ReflectionClass(AuthnetWebhooksRequest::class);
        $reflectionOfProcessor = $reflectionClass->getProperty('processor');
        $reflectionOfProcessor->setAccessible(true);
        $processor = $reflectionOfProcessor->getValue($authnet);

        self::assertInstanceOf(Curl::class, $processor);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getWebhooksHandler
     * @uses \JohnConde\Authnet\AuthnetWebhooksRequest
     */
    public function testCurlWrapperDevelopmentResponseWebhooks()
    {
        $endpoint = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $authnet  = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $endpoint);

        $reflectionClass = new ReflectionClass(AuthnetWebhooksRequest::class);
        $reflectionOfProcessor = $reflectionClass->getProperty('processor');
        $reflectionOfProcessor->setAccessible(true);
        $processor = $reflectionOfProcessor->getValue($authnet);

        self::assertInstanceOf(Curl::class, $processor);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getWebhooksURL
     */
    public function testGetWebhooksUrlProductionServer()
    {
        $endpoint         = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebhooksURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $endpoint);

        self::assertEquals('https://api.authorize.net/rest/v1/', $url);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getWebhooksURL
     */
    public function testGetWebhooksUrlDevelopmentServer()
    {
        $endpoint         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebhooksURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $endpoint);

        self::assertEquals('https://apitest.authorize.net/rest/v1/', $url);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetApiFactory::getWebhooksURL
     * @covers \JohnConde\Authnet\Exception\AuthnetInvalidServerException::__construct()
     */
    public function testGetWebhooksUrlBadServer()
    {
        $this->expectException(AuthnetInvalidServerException::class);

        $endpoint         = 99;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebhooksURL');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke(null, $endpoint);
    }
}
