<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Authnetjson\tests;

use Authnetjson\AuthnetApiFactory;
use Authnetjson\AuthnetWebhooksRequest;
use Authnetjson\AuthnetWebhooksResponse;
use Authnetjson\Exception\AuthnetCurlException;
use PHPUnit\Framework\TestCase;
use Curl\Curl;

class AuthnetWebhooksRequestTest extends TestCase
{
    private $login;
    private $transactionKey;
    private $server;
    private $http;

    protected function setUp(): void
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
        $this->server         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;

        $this->http = $this->getMockBuilder(Curl::class)
            ->getMock();
        $this->http->error = false;
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::__construct()
     */
    public function testConstructor(): void
    {
        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);

        $reflectionOfRequest = new \ReflectionObject($request);
        $property = $reflectionOfRequest->getProperty('url');
        $property->setAccessible(true);

        self::assertEquals($property->getValue($request), 'https://apitest.authorize.net/rest/v1/');
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::__toString()
     */
    public function testToString(): void
    {
        $this->http->error = false;
        $this->http->response = '[{
            "name": "test"
        }]';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $request->updateWebhook('871a6a11-b654-45af-b97d-da72a490d0fd', 'http://www.example.com/webhook', ['net.authorize.customer.subscription.expiring'], 'inactive');

        ob_start();
        echo $request;
        $string = ob_get_clean();

        self::assertStringContainsString('https://apitest.authorize.net/rest/v1/webhooks/871a6a11-b654-45af-b97d-da72a490d0fd', $string);
        self::assertStringContainsString('{"url":"http:\/\/www.example.com\/webhook","eventTypes":["net.authorize.customer.subscription.expiring"],"status":"inactive"}', $string);
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::__toString()
     */
    public function testToStringNA(): void
    {
        $this->http->error = false;
        $this->http->response = '[{
            "name": "test"
        }]';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $request->getEventTypes();

        ob_start();
        echo $request;
        $string = ob_get_clean();

        self::assertStringContainsString('N/A', $string);
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::getEventTypes()
     * @covers \Authnetjson\AuthnetWebhooksRequest::getByUrl()
     */
    public function testGetEventTypes(): void
    {
        $this->http->error = false;
        $this->http->response = '[
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

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->getEventTypes();

        self::assertInstanceOf(AuthnetWebhooksResponse::class, $response);
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::createWebhooks()
     */
    public function testCreateWebhooks(): void
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

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createWebhooks([
            'net.authorize.customer.created',
            'net.authorize.customer.paymentProfile.created',
            'net.authorize.customer.subscription.expiring'
        ], 'http://requestb.in/', 'active');

        self::assertInstanceOf(AuthnetWebhooksResponse::class, $response);
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::testWebhook()
     */
    public function testTestWebhook(): void
    {
        $this->http->error = false;
        $this->http->response = '';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->testWebhook('ba4c73f3-0808-48bf-ae2f-f49064770e60');

        self::assertNull($response);
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::deleteWebhook()
     */
    public function testDeleteWebhook(): void
    {
        $this->http->error = false;
        $this->http->response = '';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->deleteWebhook('ba4c73f3-0808-48bf-ae2f-f49064770e60');

        self::assertNull($response);
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::getWebhooks()
     * @covers \Authnetjson\AuthnetWebhooksRequest::getByUrl()
     */
    public function testGetWebhooks(): void
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

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->getWebhooks();

        self::assertInstanceOf(AuthnetWebhooksResponse::class, $response);
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::getWebhook()
     * @covers \Authnetjson\AuthnetWebhooksRequest::getByUrl()
     */
    public function testGetWebhook(): void
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

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->getWebhook('cd2c262f-2723-4848-ae92-5d317902441c');

        self::assertInstanceOf(AuthnetWebhooksResponse::class, $response);
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::updateWebhook()
     */
    public function testUpdateWebhook(): void
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

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->updateWebhook('ba4c73f3-0808-48bf-ae2f-f49064770e60', 'http://requestb.in/', [
            'net.authorize.customer.created'
        ], 'active');

        self::assertInstanceOf(AuthnetWebhooksResponse::class, $response);
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::getNotificationHistory()
     */
    public function testGetNotificationHistory(): void
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

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->getNotificationHistory();

        self::assertInstanceOf(AuthnetWebhooksResponse::class, $response);
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::handleResponse()
     */
    public function testHandleResponse(): void
    {
        $this->http->error = false;
        $this->http->response = '{"error"}';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);

        $method = new \ReflectionMethod(AuthnetWebhooksRequest::class, 'handleResponse');
        $method->setAccessible(true);

        $response = $method->invoke($request);

        self::assertEquals($this->http->response, $response);
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::handleResponse()
     * @covers \Authnetjson\Exception\AuthnetCurlException::__construct()
     */
    public function testHandleResponseWithErrorMessage(): void
    {
        $this->expectException(AuthnetCurlException::class);

        $this->http->error          = true;
        $this->http->error_message  = 'Error Message';
        $this->http->error_code     = 100;
        $this->http->response       = '{
  "status": 400,
  "reason": "MISSING_DATA",
  "message": "error",
  "correlationId": "xxxxxxx"
}';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);

        $method = new \ReflectionMethod(AuthnetWebhooksRequest::class, 'handleResponse');
        $method->setAccessible(true);

        $method->invoke($request);
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::handleResponse()
     * @covers \Authnetjson\Exception\AuthnetCurlException::__construct()
     */
    public function testHandleResponseWithErrorMessageTestMessage(): void
    {
        $this->expectException(AuthnetCurlException::class);

        $this->http->error          = true;
        $this->http->error_message  = 'Error Message';
        $this->http->error_code     = 100;
        $this->http->response       = '{
  "status": 400,
  "reason": "MISSING_DATA",
  "message": "error",
  "correlationId": "xxxxxxx"
}';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);

        $method = new \ReflectionMethod(AuthnetWebhooksRequest::class, 'handleResponse');
        $method->setAccessible(true);

        try {
            $method->invoke($request);
        }
        catch (\AuthnetCurlException $e) {
            self::assertEquals(sprintf('Connection error: %s (%s)', $this->http->error_message, $this->http->error_code), $e->getMessage());
        }
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::handleResponse()
     * @covers \Authnetjson\Exception\AuthnetCurlException::__construct()
     */
    public function testHandleResponseWithErrorMessageNoMessage(): void
    {
        $this->expectException(AuthnetCurlException::class);

        $this->http->error          = true;
        $this->http->error_code     = 100;
        $this->http->response       = json_encode([
            'status' => '100',
            'reason' => 'error',
            'message' => 'not good',
        ]);

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);

        $method = new \ReflectionMethod(AuthnetWebhooksRequest::class, 'handleResponse');
        $method->setAccessible(true);

        try {
            $method->invoke($request);
        }
        catch (\AuthnetCurlException $e) {
            $error_message = sprintf('(%u) %s: %s', $this->http->response->status, $this->http->response->reason, $this->http->response->message);
            self::assertEquals(sprintf('Connection error: %s (%s)', $error_message, $this->http->error_code), $e->getMessage());
        }
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::setProcessHandler()
     */
    public function testProcessorIsInstanceOfCurlWrapper(): void
    {
        $request = new AuthnetWebhooksRequest('');
        $request->setProcessHandler(new Curl());

        $reflectionOfRequest = new \ReflectionObject($request);
        $processor = $reflectionOfRequest->getProperty('processor');
        $processor->setAccessible(true);

        self::assertInstanceOf(Curl::class, $processor->getValue($request));
    }

    /**
     * @covers \Authnetjson\AuthnetWebhooksRequest::getRawRequest()
     */
    public function testGetRawRequest(): void
    {
        $this->http->response = $responseJson = '[{
            "name": "test"
        }]';

        $request = AuthnetApiFactory::getWebhooksHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $request->updateWebhook('871a6a11-b654-45af-b97d-da72a490d0fd', 'http://www.example.com/webhook', ['net.authorize.customer.subscription.expiring'], 'inactive');

        self::assertEquals('{"url":"http:\/\/www.example.com\/webhook","eventTypes":["net.authorize.customer.subscription.expiring"],"status":"inactive"}', $request->getRawRequest());
    }
}
