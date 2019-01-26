<?php

/**
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnConde\Authnet;

/**
 * Adapter for the Authorize.Net Webhooks API
 *
 * @package     AuthnetJSON
 * @author      John Conde <stymiee@gmail.com>
 * @copyright   John Conde <stymiee@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link        https://github.com/stymiee/authnetjson
 * @see         https://developer.authorize.net/api/reference/
 */
class AuthnetWebhooksResponse
{
    /**
     * @var     object  SimpleXML object representing the API response
     */
    private $response;

    /**
     * @var     string  JSON string that is the response sent by Authorize.Net
     */
    private $responseJson;

    /**
     * Creates the response object with the response json returned from the API call
     *
     * @param   string      $responseJson   Response from Authorize.Net
     * @throws  \JohnConde\Authnet\AuthnetInvalidJsonException
     */
    public function __construct($responseJson)
    {
        $this->responseJson = $responseJson;
        if (($this->response = json_decode($this->responseJson)) === null) {
            throw new AuthnetInvalidJsonException('Invalid JSON returned by the API');
        }
    }

    /**
     * Outputs the response JSON in a human readable format
     *
     * @return  string  HTML table containing debugging information
     */
    public function __toString()
    {
        $output  = '<table summary="Authorize.Net Webhooks Response" id="authnet-response">'."\n";
        $output .= '<tr>'."\n\t\t".'<th colspan="2"><b>Response JSON</b></th>'."\n".'</tr>'."\n";
        $output .= '<tr><td colspan="2"><pre>'."\n";
        $output .= $this->responseJson."\n";
        $output .= '</pre></td></tr>'."\n";
        $output .= '</table>';

        return $output;
    }

    /**
     * Gets a response variable from the API response
     *
     * net.authorize.customer.created
     * net.authorize.customer.deleted
     * net.authorize.customer.updated
     * net.authorize.customer.paymentProfile.created
     * net.authorize.customer.paymentProfile.deleted
     * net.authorize.customer.paymentProfile.updated
     * net.authorize.customer.subscription.cancelled
     * net.authorize.customer.subscription.created
     * net.authorize.customer.subscription.expiring
     * net.authorize.customer.subscription.suspended
     * net.authorize.customer.subscription.terminated
     * net.authorize.customer.subscription.updated
     * net.authorize.payment.authcapture.created
     * net.authorize.payment.authorization.created
     * net.authorize.payment.capture.created
     * net.authorize.payment.fraud.approved
     * net.authorize.payment.fraud.declined
     * net.authorize.payment.fraud.held
     * net.authorize.payment.priorAuthCapture.created
     * net.authorize.payment.refund.created
     * net.authorize.payment.void.created
     *
     * @return  array   Array of event types supported by Webhooks API
     */
    public function getEventTypes()
    {
        $events = [];
        if (isset($this->response->eventTypes)) {
            foreach ($this->response->eventTypes as $event) {
                $events[] = $event;
            }
        } else {
            $events = array_column($this->response, 'name');
        }
        return $events;
    }

    /**
     * Gets the webhooks ID
     *
     * @return  string  Webhooks ID
     */
    public function getWebhooksId()
    {
        return $this->response->webhookId;
    }

    /**
     * Gets the status of the Webhooks
     *
     * @return  string  Staus of the webhooks [active|inactive]
     */
    public function getStatus()
    {
        return $this->response->status;
    }

    /**
     * Gets the URL the Webhooks API will use for these Webhooks
     *
     * @return  string
     */
    public function getUrl()
    {
        return $this->response->url;
    }

    /**
     * Gets a list of webhooks
     *
     * @return  array
     * @throws  \JohnConde\Authnet\AuthnetInvalidJsonException
     */
    public function getWebhooks()
    {
        $webhooks = [];
        foreach ($this->response as $webhook) {
            $webhooks[] = new AuthnetWebhooksResponse(json_encode($webhook));
        }
        return $webhooks;
    }

    /**
     * Gets a list of webhooks
     *
     * @return  array
     * @throws  \JohnConde\Authnet\AuthnetInvalidJsonException
     */
    public function getNotificationHistory()
    {
        $notifications = [];
        if (count($this->response->notifications)) {
            foreach ($this->response->notifications as $notification) {
                $notifications[] = new AuthnetWebhooksResponse(json_encode($notification));
            }
        }
        return $notifications;
    }

    /**
     * Gets the notification ID of a notification
     *
     * @return  string
     */
    public function getNotificationId()
    {
        return $this->response->notificationId;
    }

    /**
     * Gets the delivery status of a notification
     *
     * @return  string
     */
    public function getDeliveryStatus()
    {
        return $this->response->deliveryStatus;
    }

    /**
     * Gets the event type of a notification
     *
     * @return  string
     */
    public function getEventType()
    {
        return $this->response->eventType;
    }

    /**
     * Gets the event date of a notification
     *
     * @return  string
     */
    public function getEventDate()
    {
        return $this->response->eventDate;
    }
}