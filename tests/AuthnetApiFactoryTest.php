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

class AuthnetApiFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $login;
    private $transactionKey;

    protected function setUp()
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     * @uses              \JohnConde\Authnet\AuthnetJson
     * @expectedException \JohnConde\Authnet\AuthnetInvalidCredentialsException
     */
    public function testExceptionIsRaisedForInvalidCredentialsLogin()
    {
        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $authnet = AuthnetApiFactory::getJsonApiHandler(null, $this->transactionKey, $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     * @uses              \JohnConde\Authnet\AuthnetJson
     * @expectedException \JohnConde\Authnet\AuthnetInvalidCredentialsException
     */
    public function testExceptionIsRaisedForInvalidCredentialsTransactionKey()
    {
        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, null, $server);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     * @expectedException \JohnConde\Authnet\AuthnetInvalidServerException
     */
    public function testExceptionIsRaisedForAuthnetInvalidServer()
    {
        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, null);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::CurlWrapper
     * @uses              \JohnConde\Authnet\AuthnetJson
     */
    public function testCurlWrapperProductionResponse()
    {
        $server  = AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $server);

        $this->assertInstanceOf('JohnConde\Authnet\CurlWrapper', new CurlWrapper());
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     * @covers            \JohnConde\Authnet\AuthnetApiFactory::CurlWrapper
     * @uses              \JohnConde\Authnet\AuthnetJson
     */
    public function testCurlWrapperDevelopmentResponse()
    {
        $server  = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $server);

        $this->assertInstanceOf('JohnConde\Authnet\CurlWrapper', new CurlWrapper());
    }
}