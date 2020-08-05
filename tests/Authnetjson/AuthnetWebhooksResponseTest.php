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

class AuthnetWebhooksResponseTest extends TestCase
{
    /**
     * @covers            \Authnetjson\AuthnetWebhooksResponse::__construct()
     * @covers            \Authnetjson\AuthnetInvalidJsonException::__construct()
     */
    public function testExceptionIsRaisedForInvalidJsonException() : void
    {
        $this->expectException(AuthnetInvalidJsonException::class);
        new AuthnetWebhooksResponse('');
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhooksResponse::__construct()
     */
    public function testConstruct() : void
    {
        $responseJson = '{
            "url": "http://example.com",
            "eventTypes": [
                "net.authorize.payment.authorization.created"
            ],
            "status": "active"
        }';
        try {
            new AuthnetWebhooksResponse($responseJson);
            self::assertTrue(true);
        } catch (\Exception $e) {
            self::assertTrue(false);
        }
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhooksResponse::__toString()
     */
    public function testToString() : void
    {
        $responseJson = '{
            "url": "http://example.com",
            "eventTypes": [
                "net.authorize.payment.authorization.created"
            ],
            "status": "active"
        }';

        $response = new AuthnetWebhooksResponse($responseJson);

        ob_start();
        echo $response;
        $string = ob_get_clean();

        self::assertStringContainsString('example.com', $string);
        self::assertStringContainsString('net.authorize.payment.authorization.created', $string);
        self::assertStringContainsString('active', $string);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhooksResponse::getEventTypes()
     */
    public function testGetEventTypes() : void
    {
        $responseJson = '[{
            "name": "net.authorize.payment.authcapture.created"
        }, {
            "name": "net.authorize.customer.subscription.cancelled"
        }, {
            "name": "net.authorize.payment.authorization.created"
        }]';

        $eventTypes = (new AuthnetWebhooksResponse($responseJson))->getEventTypes();

        self::assertCount(3, $eventTypes);
        self::assertEquals('net.authorize.payment.authcapture.created', $eventTypes[0]);
        self::assertEquals('net.authorize.customer.subscription.cancelled', $eventTypes[1]);
        self::assertEquals('net.authorize.payment.authorization.created', $eventTypes[2]);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhooksResponse::getEventTypes()
     */
    public function testGetEventTypesFromWebhooks() : void
    {
        $responseJson = '{
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

        $eventTypes = (new AuthnetWebhooksResponse($responseJson))->getEventTypes();

        self::assertCount(4, $eventTypes);
        self::assertEquals('net.authorize.payment.authcapture.created', $eventTypes[0]);
        self::assertEquals('net.authorize.customer.created', $eventTypes[1]);
        self::assertEquals('net.authorize.customer.paymentProfile.created', $eventTypes[2]);
        self::assertEquals('net.authorize.customer.subscription.expiring', $eventTypes[3]);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhooksResponse::getWebhooksId()
     */
    public function testGetWebhooksId() : void
    {
        $responseJson = '{
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

        $id = (new AuthnetWebhooksResponse($responseJson))->getWebhooksId();

        self::assertEquals('72a55c78-66e6-4b1e-a4d6-3f925c00561f', $id);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhooksResponse::getStatus()
     */
    public function testGetStatus() : void
    {
        $responseJson = '{
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

        $status = (new AuthnetWebhooksResponse($responseJson))->getStatus();

        self::assertEquals('active', $status);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhooksResponse::getUrl()
     */
    public function testGetUrl() : void
    {
        $responseJson = '{
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
            "url": "http://example.com"
        }';

        $url = (new AuthnetWebhooksResponse($responseJson))->getUrl();

        self::assertEquals('http://example.com', $url);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhooksResponse::getWebhooks()
     */
    public function testGetWebhooks() : void
    {
        $responseJson = '[{
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
            "url": "http://example.com"
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
            "url": "http://example.com"
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
            "url": "http://example.com"
        }]';

        $webhooks = (new AuthnetWebhooksResponse($responseJson))->getWebhooks();

        self::assertCount(3, $webhooks);
        self::assertcount(4, $webhooks[0]->getEventTypes());
        self::assertEquals('net.authorize.payment.authcapture.created', $webhooks[0]->getEventTypes()[0]);
        self::assertEquals('72a55c78-66e6-4b1e-a4d6-3f925c00561f', $webhooks[0]->getWebhooksId());
        self::assertEquals('active', $webhooks[0]->getStatus());
        self::assertEquals('http://example.com', $webhooks[0]->getUrl());
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhooksResponse::getNotificationHistory()
     * @covers            \Authnetjson\AuthnetWebhooksResponse::getNotificationId()
     * @covers            \Authnetjson\AuthnetWebhooksResponse::getDeliveryStatus()
     * @covers            \Authnetjson\AuthnetWebhooksResponse::getEventType()
     * @covers            \Authnetjson\AuthnetWebhooksResponse::getEventDate()
     */
    public function testGetNotificationHistory() : void
    {
        $responseJson = '{
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

        $history = (new AuthnetWebhooksResponse($responseJson))->getNotificationHistory();

        self::assertCount(1, $history);
        self::assertEquals('e35d5ede-27c5-46cc-aabb-131f10154ed3', $history[0]->getNotificationId());
        self::assertEquals('Delivered', $history[0]->getDeliveryStatus());
        self::assertEquals('net.authorize.payment.authcapture.created', $history[0]->getEventType());
        self::assertEquals('2017-02-09T19:18:42.167', $history[0]->getEventDate());
    }
}
