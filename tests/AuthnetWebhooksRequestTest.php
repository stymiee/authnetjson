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

require(__DIR__ . '/../config.inc.php');

class AuthnetWebhooksRequestTest extends \PHPUnit_Framework_TestCase
{
    private $login;
    private $transactionKey;
    private $server;
    private $http;

    protected function setUp()
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
        $this->server         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;

        $this->http = $this->getMockBuilder('\Curl\Curl')
            ->setMethods(['post','get','put','delete'])
            ->getMock();
        $this->http->error = false;
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::__construct()
     */
    public function testConstructor()
    {
        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);

        $reflectionOfRequest = new \ReflectionObject($request);
        $property = $reflectionOfRequest->getProperty('url');
        $property->setAccessible(true);

        $this->assertEquals($property->getValue($request), 'https://apitest.authorize.net/rest/v1/');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::__toString()
     */
    public function testToString()
    {
        $this->http->error = false;
        $this->http->response = $responseJson = '[{
            "name": "test"
        }]';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);
        $request->updateWebhook('871a6a11-b654-45af-b97d-da72a490d0fd', 'http://www.example.com/webhook', ['net.authorize.customer.subscription.expiring'], 'inactive');

        ob_start();
        echo $request;
        $string = ob_get_clean();

        $this->assertContains('https://apitest.authorize.net/rest/v1/webhooks/871a6a11-b654-45af-b97d-da72a490d0fd', $string);
        $this->assertContains('{"url":"http:\/\/www.example.com\/webhook","eventTypes":["net.authorize.customer.subscription.expiring"],"status":"inactive"}', $string);
    }











    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::setProcessHandler()
     */
    public function testProcessorIsInstanceOfCurlWrapper()
    {
        $request = new AuthnetWebhooksRequest(null, null, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler(new \Curl\Curl());

        $reflectionOfRequest = new \ReflectionObject($request);
        $processor = $reflectionOfRequest->getProperty('processor');
        $processor->setAccessible(true);

        $this->assertInstanceOf('\Curl\Curl', $processor->getValue($request));
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::getRawRequest()
     */
    public function testGetRawRequest()
    {
        $this->http->response = $responseJson = '[{
            "name": "test"
        }]';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);
        $request->updateWebhook('871a6a11-b654-45af-b97d-da72a490d0fd', 'http://www.example.com/webhook', ['net.authorize.customer.subscription.expiring'], 'inactive');

        $this->assertEquals('{"url":"http:\/\/www.example.com\/webhook","eventTypes":["net.authorize.customer.subscription.expiring"],"status":"inactive"}', $request->getRawRequest());
    }
}