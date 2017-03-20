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

class AuthnetWebhooksResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::__construct()
     * @expectedException \JohnConde\Authnet\AuthnetInvalidJsonException
     */
    public function testExceptionIsRaisedForInvalidJsonException()
    {
        new AuthnetWebhooksResponse('');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::__construct()
     */
    public function testConstruct()
    {
        $responseJson = '{
            "url": "http://example.com",
            "eventTypes": [
                "net.authorize.payment.authorization.created"
            ],
            "status": "active"
        }';
        new AuthnetWebhooksResponse($responseJson);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::__toString()
     */
    public function testToString()
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

        $this->assertContains('example.com', $string);
        $this->assertContains('net.authorize.payment.authorization.created', $string);
        $this->assertContains('active', $string);
    }

//    /**
//     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::__get()
//     */
//    public function testGet()
//    {
//        $responseJson = '{
//            "url": "http://example.com",
//            "eventTypes": [
//                "net.authorize.payment.authorization.created"
//            ],
//            "status": "active"
//        }';
//
//        $response = new AuthnetJsonResponse($responseJson);
//
//        $this->assertEquals('http://example.com', $response->url);
//    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::getEventTypes()
     */
    public function testGetEventTypes()
    {
        $responseJson = '[{
            "name": "net.authorize.payment.authcapture.created"
        }, {
            "name": "net.authorize.customer.subscription.cancelled"
        }, {
            "name": "net.authorize.payment.authorization.created"
        }]';

        $eventTypes = (new AuthnetWebhooksResponse($responseJson))->getEventTypes();

        $this->assertCount(3, $eventTypes);
        $this->assertEquals('net.authorize.payment.authcapture.created', $eventTypes[0]);
        $this->assertEquals('net.authorize.customer.subscription.cancelled', $eventTypes[1]);
        $this->assertEquals('net.authorize.payment.authorization.created', $eventTypes[2]);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::getEventTypes()
     */
    public function testGetEventTypesFromWebhooks()
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

        $this->assertCount(4, $eventTypes);
        $this->assertEquals('net.authorize.payment.authcapture.created', $eventTypes[0]);
        $this->assertEquals('net.authorize.customer.created', $eventTypes[1]);
        $this->assertEquals('net.authorize.customer.paymentProfile.created', $eventTypes[2]);
        $this->assertEquals('net.authorize.customer.subscription.expiring', $eventTypes[3]);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::getWebhooksId()
     */
    public function testGetWebhooksId()
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

        $this->assertEquals('72a55c78-66e6-4b1e-a4d6-3f925c00561f', $id);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::getStatus()
     */
    public function testGetStatus()
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

        $this->assertEquals('active', $status);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::getUrl()
     */
    public function testGetUrl()
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

        $this->assertEquals('http://example.com', $url);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::getWebhooks()
     */
    public function testGetWebhooks()
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

        $this->assertCount(3, $webhooks);
        $this->assertcount(4, $webhooks[0]->getEventTypes());
        $this->assertEquals('net.authorize.payment.authcapture.created', $webhooks[0]->getEventTypes()[0]);
        $this->assertEquals('72a55c78-66e6-4b1e-a4d6-3f925c00561f', $webhooks[0]->getWebhooksId());
        $this->assertEquals('active', $webhooks[0]->getStatus());
        $this->assertEquals('http://example.com', $webhooks[0]->getUrl());
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::getNotificationHistory()
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::getNotificationId()
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::getDeliveryStatus()
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::getEventType()
     * @covers            \JohnConde\Authnet\AuthnetWebhooksResponse::getEventDate()
     */
    public function testGetNotificationHistory()
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

        $this->assertCount(1, $history);
        $this->assertEquals('e35d5ede-27c5-46cc-aabb-131f10154ed3', $history[0]->getNotificationId());
        $this->assertEquals('Delivered', $history[0]->getDeliveryStatus());
        $this->assertEquals('net.authorize.payment.authcapture.created', $history[0]->getEventType());
        $this->assertEquals('2017-02-09T19:18:42.167', $history[0]->getEventDate());
    }
}