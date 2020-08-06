<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Authnetjson;

use PHPUnit\Framework\TestCase;
use Curl\Curl;
use ReflectionClass;
use ReflectionMethod;

class AuthnetApiFactoryTest extends TestCase
{
    private $login;
    private $transactionKey;

    protected function setUp(): void
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetWebServiceUrlProductionServer(): void
    {
        $endpoint         = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $endpoint);

        self::assertEquals('https://api.authorize.net/xml/v1/request.api', $url);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetWebServiceUrlDevelopmentServer(): void
    {
        $endpoint         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $endpoint);

        self::assertEquals('https://apitest.authorize.net/xml/v1/request.api', $url);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetWebServiceUrlCDNServer(): void
    {
        $endpoint         = AuthnetApiFactory::USE_CDN_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $endpoint);

        self::assertEquals('https://api2.authorize.net/xml/v1/request.api', $url);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getWebServiceURL
     * @covers            \Authnetjson\AuthnetException::__construct()
     * @covers            \Authnetjson\AuthnetInvalidServerException::__construct()
     */
    public function testGetWebServiceUrlBadServer(): void
    {
        $this->expectException(AuthnetInvalidServerException::class);

        $endpoint         = 99;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebServiceURL');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke(null, $endpoint);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @covers            \Authnetjson\AuthnetInvalidCredentialsException::__construct()
     * @uses              \Authnetjson\AuthnetJsonRequest
     */
    public function testExceptionIsRaisedForInvalidCredentialsLogin(): void
    {
        $this->expectException(AuthnetInvalidCredentialsException::class);

        $endpoint  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getJsonApiHandler('', $this->transactionKey, $endpoint);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @covers            \Authnetjson\AuthnetInvalidCredentialsException::__construct()
     * @uses              \Authnetjson\AuthnetJsonRequest
     */
    public function testExceptionIsRaisedForInvalidCredentialsTransactionKey(): void
    {
        $this->expectException(AuthnetInvalidCredentialsException::class);

        $endpoint  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getJsonApiHandler($this->login, '', $endpoint);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @covers            \Authnetjson\AuthnetInvalidServerException::__construct()
     */
    public function testExceptionIsRaisedForAuthnetInvalidServer(): void
    {
        $this->expectException(AuthnetInvalidServerException::class);

        AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, 5);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses              \Authnetjson\AuthnetJsonRequest
     */
    public function testCurlWrapperProductionResponse(): void
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
     * @covers            \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses              \Authnetjson\AuthnetJsonRequest
     */
    public function testCurlWrapperDevelopmentResponse(): void
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
     * @covers            \Authnetjson\AuthnetApiFactory::getSimHandler
     */
    public function testGetSimHandler(): void
    {
        $endpoint = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $sim    = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $endpoint);

        self::assertInstanceOf(AuthnetSim::class, $sim);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getSimHandler
     * @covers            \Authnetjson\AuthnetInvalidCredentialsException::__construct()
     */
    public function testExceptionIsRaisedForInvalidCredentialsSim(): void
    {
        $this->expectException(AuthnetInvalidCredentialsException::class);

        $endpoint  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getSimHandler('', $this->transactionKey, $endpoint);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getSimURL
     */
    public function testGetSimServerTest(): void
    {
        $endpoint         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getSimURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke($reflectionMethod, $endpoint);

        self::assertEquals('https://test.authorize.net/gateway/transact.dll', $url);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getSimURL
     */
    public function testGetSimServerProduction(): void
    {
        $endpoint         = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getSimURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke($reflectionMethod, $endpoint);

        self::assertEquals('https://secure2.authorize.net/gateway/transact.dll', $url);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getSimURL
     * @covers            \Authnetjson\AuthnetInvalidServerException::__construct()
     */
    public function testExceptionIsRaisedForAuthnetInvalidSimServer(): void
    {
        $this->expectException(AuthnetInvalidServerException::class);

        AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, 5);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getWebhooksHandler
     * @covers            \Authnetjson\AuthnetInvalidCredentialsException::__construct()
     * @uses              \Authnetjson\AuthnetWebhooksRequest
     */
    public function testExceptionIsRaisedForInvalidCredentialsLoginWebhooks(): void
    {
        $this->expectException(AuthnetInvalidCredentialsException::class);

        $endpoint  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getWebhooksHandler('', $this->transactionKey, $endpoint);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getWebhooksHandler
     * @covers            \Authnetjson\AuthnetInvalidCredentialsException::__construct()
     * @uses              \Authnetjson\AuthnetWebhooksRequest
     */
    public function testExceptionIsRaisedForInvalidCredentialsTransactionKeyWebhooks(): void
    {
        $this->expectException(AuthnetInvalidCredentialsException::class);

        $endpoint = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        AuthnetApiFactory::getWebhooksHandler($this->login, '', $endpoint);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getWebhooksHandler
     * @covers            \Authnetjson\AuthnetInvalidServerException::__construct()
     */
    public function testExceptionIsRaisedForAuthnetInvalidServerWebhooks(): void
    {
        $this->expectException(AuthnetInvalidServerException::class);

        AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, 5);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getWebhooksHandler
     * @uses              \Authnetjson\AuthnetWebhooksRequest
     */
    public function testCurlWrapperProductionResponseWebhooks(): void
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
     * @covers            \Authnetjson\AuthnetApiFactory::getWebhooksHandler
     * @uses              \Authnetjson\AuthnetWebhooksRequest
     */
    public function testCurlWrapperDevelopmentResponseWebhooks(): void
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
     * @covers            \Authnetjson\AuthnetApiFactory::getWebhooksURL
     */
    public function testGetWebhooksUrlProductionServer(): void
    {
        $endpoint         = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebhooksURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $endpoint);

        self::assertEquals('https://api.authorize.net/rest/v1/', $url);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getWebhooksURL
     */
    public function testGetWebhooksUrlDevelopmentServer(): void
    {
        $endpoint         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebhooksURL');
        $reflectionMethod->setAccessible(true);
        $url              = $reflectionMethod->invoke(null, $endpoint);

        self::assertEquals('https://apitest.authorize.net/rest/v1/', $url);
    }

    /**
     * @covers            \Authnetjson\AuthnetApiFactory::getWebhooksURL
     * @covers            \Authnetjson\AuthnetInvalidServerException::__construct()
     */
    public function testGetWebhooksUrlBadServer(): void
    {
        $this->expectException(AuthnetInvalidServerException::class);

        $endpoint         = 99;
        $reflectionMethod = new ReflectionMethod(AuthnetApiFactory::class, 'getWebhooksURL');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke(null, $endpoint);
    }
}
