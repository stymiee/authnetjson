<?php

class AuthnetApiFactoryTest extends PHPUnit_Framework_TestCase
{
    private $login;
    private $transactionKey;
    private $server;

    protected function setUp()
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
    }

    public function testUnitTestWrapperResponse()
    {
        $server  = \JohnConde\Authnet\AuthnetApiFactory::USE_UNIT_TEST_SERVER;
        $authnet = \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $server);

        $this->assertEquals('JohnConde\Authnet\UnitTestWrapper', $authnet->identifyProcessorWrapper());
    }

    public function testCurlWrapperProductionResponse()
    {
        $server  = \JohnConde\Authnet\AuthnetApiFactory::USE_PRODUCTION_SERVER;
        $authnet = \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $server);

        $this->assertEquals('JohnConde\Authnet\CurlWrapper', $authnet->identifyProcessorWrapper());
    }

    public function testCurlWrapperDevelopmentResponse()
    {
        $server  = \JohnConde\Authnet\AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
        $authnet = \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $server);

        $this->assertEquals('JohnConde\Authnet\CurlWrapper', $authnet->identifyProcessorWrapper());
    }
}