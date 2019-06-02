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

class AuthnetWebhooksRequestTest extends TestCase
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
        $this->http->response = '[{
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
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::__toString()
     */
    public function testToStringNA()
    {
        $this->http->error = false;
        $this->http->response = '[{
            "name": "test"
        }]';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);
        $request->getEventTypes();

        ob_start();
        echo $request;
        $string = ob_get_clean();

        $this->assertContains('N/A', $string);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::getEventTypes()
     */
    public function testGetEventTypes()
    {
        $this->http->error = false;
        $this->http->response = $responseJson = '[
            {
                "name": "net.authorize.customer.created"
            },
            {
                "name": "net.authorize.customer.deleted"
            },
            {
                "name": "net.authorize.customer.updated"
            },
            {
                "name": "net.authorize.customer.paymentProfile.created"
            },
            {
                "name": "net.authorize.customer.paymentProfile.deleted"
            },
            {
                "name": "net.authorize.customer.paymentProfile.updated"
            },
            {
                "name": "net.authorize.customer.subscription.cancelled"
            },
            {
                "name": "net.authorize.customer.subscription.created"
            },
            {
                "name": "net.authorize.customer.subscription.expiring"
            },
            {
                "name": "net.authorize.customer.subscription.suspended"
            },
            {
                "name": "net.authorize.customer.subscription.terminated"
            },
            {
                "name": "net.authorize.customer.subscription.updated"
            },
            {
                "name": "net.authorize.payment.authcapture.created"
            },
            {
                "name": "net.authorize.payment.authorization.created"
            },
            {
                "name": "net.authorize.payment.capture.created"
            },
            {
                "name": "net.authorize.payment.fraud.approved"
            },
            {
                "name": "net.authorize.payment.fraud.declined"
            },
            {
                "name": "net.authorize.payment.fraud.held"
            },
            {
                "name": "net.authorize.payment.priorAuthCapture.created"
            },
            {
                "name": "net.authorize.payment.refund.created"
            },
            {
                "name": "net.authorize.payment.void.created"
            }
        ]';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);
        $response = $request->getEventTypes();

        $this->assertInstanceOf(AuthnetWebhooksResponse::class, $response);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::createWebhooks()
     */
    public function testCreateWebhooks()
    {
        $this->http->error = false;
        $this->http->response = '{
            "_links": {
                "self": {
                    "href": "/rest/v1/webhooks/72a55c78-66e6-4b1e-a4d6-3f925c00561f"
                }
            },
            "webhookId": "72a55c78-66e6-4b1e-a4d6-3f925c00561f",
            "eventTypes": [
                "net.authorize.payment.authcapture.created",
                "net.authorize.customer.created",
                "net.authorize.customer.paymentProfile.created",
                "net.authorize.customer.subscription.expiring"
            ],
            "status": "active",
            "url": "http://localhost:55950/api/webhooks"
        }';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);
        $response = $request->createWebhooks([
            'net.authorize.customer.created',
            'net.authorize.customer.paymentProfile.created',
            'net.authorize.customer.subscription.expiring'
        ], 'http://requestb.in/', 'active');

        $this->assertInstanceOf(AuthnetWebhooksResponse::class, $response);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::testWebhook()
     */
    public function testTestWebhook()
    {
        $this->http->error = false;
        $this->http->response = '';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);
        $response = $request->testWebhook('ba4c73f3-0808-48bf-ae2f-f49064770e60');

        $this->assertNull($response);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::deleteWebhook()
     */
    public function testDeleteWebhook()
    {
        $this->http->error = false;
        $this->http->response = '';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);
        $response = $request->deleteWebhook('ba4c73f3-0808-48bf-ae2f-f49064770e60');

        $this->assertNull($response);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::getWebhooks()
     */
    public function testGetWebhooks()
    {
        $this->http->error = false;
        $this->http->response = '[{
                "_links": {
                    "self": {
                        "href": "/rest/v1/webhooks/72a55c78-66e6-4b1e-a4d6-3f925c00561f"
                    }
                },
                "webhookId": "72a55c78-66e6-4b1e-a4d6-3f925c00561f",
                "eventTypes": [
                    "net.authorize.payment.authcapture.created",
                    "net.authorize.customer.created",
                    "net.authorize.customer.paymentProfile.created",
                    "net.authorize.customer.subscription.expiring"
                ],
                "status": "active",
                "url": "http://localhost:55950/api/webhooks"
            }, {
                "_links": {
                    "self": {
                        "href": "/rest/v1/webhooks/7be120d3-2247-4706-b9b1-98931fdfdcce"
                    }
                },
                "webhookId": "7be120d3-2247-4706-b9b1-98931fdfdcce",
                "eventTypes": [
                    "net.authorize.customer.subscription.expiring",
                    "net.authorize.customer.paymentProfile.created",
                    "net.authorize.payment.authcapture.created",
                    "net.authorize.customer.created"
                ],
                "status": "inactive",
                "url": "http://localhost:55950/api/webhooks"
            }, {
                "_links": {
                    "self": {
                        "href": "/rest/v1/webhooks/62c68677-0d71-43a7-977a-f4dea3827fac"
                    }
                },
                "webhookId": "62c68677-0d71-43a7-977a-f4dea3827fac",
                "eventTypes": [
                    "net.authorize.customer.subscription.expiring",
                    "net.authorize.customer.created",
                    "net.authorize.customer.paymentProfile.created",
                    "net.authorize.payment.authcapture.created"
                ],
                "status": "active",
                "url": "http://localhost:55950/api/webhooks"
        }]';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);
        $response = $request->getWebhooks();

        $this->assertInstanceOf(AuthnetWebhooksResponse::class, $response);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::getWebhook()
     */
    public function testGetWebhook()
    {
        $this->http->error = false;
        $this->http->response = '{
            "_links": {
                "self": {
                    "href": "/rest/v1/webhooks/72a55c78-66e6-4b1e-a4d6-3f925c00561f"
                }
            },
            "webhookId": "72a55c78-66e6-4b1e-a4d6-3f925c00561f",
            "eventTypes": [
                "net.authorize.payment.authcapture.created",
                "net.authorize.customer.created",
                "net.authorize.customer.paymentProfile.created",
                "net.authorize.customer.subscription.expiring"
            ],
            "status": "active",
            "url": "http://localhost:55950/api/webhooks"
        }';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);
        $response = $request->getWebhook('cd2c262f-2723-4848-ae92-5d317902441c');

        $this->assertInstanceOf(AuthnetWebhooksResponse::class, $response);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::updateWebhook()
     */
    public function testUpdateWebhook()
    {
        $this->http->error = false;
        $this->http->response = '{
            "_links": {
                "self": {
                    "href": "/rest/v1/webhooks/72a55c78-66e6-4b1e-a4d6-3f925c00561f"
                }
            },
            "webhookId": "72a55c78-66e6-4b1e-a4d6-3f925c00561f",
            "eventTypes": [
                "net.authorize.payment.authcapture.created"
            ],
            "status": "active",
            "url": "http://requestb.in/19okx6x1"
        }';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);
        $response = $request->updateWebhook('ba4c73f3-0808-48bf-ae2f-f49064770e60', 'http://requestb.in/', [
            'net.authorize.customer.created'
        ], 'active');

        $this->assertInstanceOf(AuthnetWebhooksResponse::class, $response);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::getNotificationHistory()
     */
    public function testGetNotificationHistory()
    {
        $this->http->error = false;
        $this->http->response = '{
            "_links": {
                "self": {
                    "href": "/rest/v1/notifications?offset=0&limit=100"
                }
            },
            "notifications": [
            {
                "_links": {
                    "self": {
                        "href": "/rest/v1/notifications/e35d5ede-27c5-46cc-aabb-131f10154ed3"
                    }
                },
                "notificationId": "e35d5ede-27c5-46cc-aabb-131f10154ed3",
                "deliveryStatus": "Delivered",
                "eventType": "net.authorize.payment.authcapture.created",
                "eventDate": "2017-02-09T19:18:42.167"
                }
            ]
        }';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);
        $response = $request->getNotificationHistory();

        $this->assertInstanceOf(AuthnetWebhooksResponse::class, $response);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::handleResponse()
     */
    public function testHandleResponse()
    {
        $this->http->error = false;
        $this->http->response = '{"error"}';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);

        $method = new \ReflectionMethod('\JohnConde\Authnet\AuthnetWebhooksRequest', 'handleResponse');
        $method->setAccessible(true);

        $response = $method->invoke($request);

        $this->assertEquals($this->http->response, $response);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::handleResponse()
     * @expectedException \JohnConde\Authnet\AuthnetCurlException
     */
    public function testHandleResponseWithErrorMessage()
    {
        $this->http->error          = true;
        $this->http->error_message  = 'Error Message';
        $this->http->error_code     = 100;
        $this->http->response       = '{
  "status": 400,
  "reason": "MISSING_DATA",
  "message": "error",
  "correlationId": "xxxxxxx"
}';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);

        $method = new \ReflectionMethod('\JohnConde\Authnet\AuthnetWebhooksRequest', 'handleResponse');
        $method->setAccessible(true);

        $method->invoke($request);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::handleResponse()
     * @expectedException \JohnConde\Authnet\AuthnetCurlException
     */
    public function testHandleResponseWithErrorMessageTestMessage()
    {
        $this->http->error          = true;
        $this->http->error_message  = 'Error Message';
        $this->http->error_code     = 100;
        $this->http->response       = '{
  "status": 400,
  "reason": "MISSING_DATA",
  "message": "error",
  "correlationId": "xxxxxxx"
}';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);

        $method = new \ReflectionMethod('\JohnConde\Authnet\AuthnetWebhooksRequest', 'handleResponse');
        $method->setAccessible(true);

        try {
            $method->invoke($request);
        }
        catch (\AuthnetCurlException $e) {
            $this->assertEquals(sprintf('Connection error: %s (%s)', $this->http->error_message, $this->http->error_code), $e->getMessage());
        }
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksRequest::handleResponse()
     * @expectedException \JohnConde\Authnet\AuthnetCurlException
     */
    public function testHandleResponseWithErrorMessageNoMessage()
    {
        $this->http->error          = true;
        $this->http->error_code     = 100;
        $this->http->response       = json_encode([
            'status' => '100',
            'reason' => 'error',
            'message' => 'not good',
        ]);

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);

        $method = new \ReflectionMethod('\JohnConde\Authnet\AuthnetWebhooksRequest', 'handleResponse');
        $method->setAccessible(true);

        try {
            $method->invoke($request);
        }
        catch (\AuthnetCurlException $e) {
            $error_message = sprintf('(%u) %s: %s', $this->http->response->status, $this->http->response->reason, $this->http->response->message);
            $this->assertEquals(sprintf('Connection error: %s (%s)', $error_message, $this->http->error_code), $e->getMessage());
        }
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