<?php

declare(strict_types=1);

/**
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Authnetjson;

use Authnetjson\Exception\AuthnetInvalidCredentialsException;
use Authnetjson\Exception\AuthnetInvalidJsonException;

/**
 * Handles a Webhook notification from the Authorize.Net Webhooks API
 *
 * @package   AuthnetJSON
 * @author    John Conde <stymiee@gmail.com>
 * @copyright 2015 - 2023 John Conde <stymiee@gmail.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link      https://github.com/stymiee/authnetjson
 * @see       https://developer.authorize.net/api/reference/
 */
class AuthnetWebhook
{
    /**
     * @var object  SimpleXML object representing the Webhook notification
     */
    private $webhook;

    /**
     * @var string  JSON string that is the Webhook notification sent by Authorize.Net
     */
    private $webhookJson;

    /**
     * @var array  HTTP headers sent with the notification
     */
    private $headers;

    /**
     * @var string  Authorize.Net Signature Key
     */
    private $signature;

    /**
     * Creates the response object with the response json returned from the API call
     *
     * @param string $signature Authorize.Net Signature Key
     * @param string $payload Webhook Notification sent by Authorize.Net
     * @param array $headers HTTP headers sent with Webhook. Optional if PHP is run as an Apache module
     * @throws AuthnetInvalidCredentialsException
     * @throws AuthnetInvalidJsonException
     */
    public function __construct(string $signature, string $payload, array $headers = [])
    {
        $this->signature = $signature;
        $this->webhookJson = $payload;
        $this->headers = $headers;
        if (empty($this->headers)) {
            $this->headers = $this->getAllHeaders();
        }
        if (empty($this->signature)) {
            throw new AuthnetInvalidCredentialsException('You have not configured your signature properly.');
        }
        if (($this->webhook = json_decode($this->webhookJson, false)) === null) {
            throw new AuthnetInvalidJsonException('Invalid JSON sent in the Webhook notification');
        }
        $this->headers = array_change_key_case($this->headers, CASE_UPPER);
    }

    /**
     * Outputs the response JSON in a human-readable format
     *
     * @return string  HTML table containing debugging information
     */
    public function __toString()
    {
        $output = '<table id="authnet-webhook">' . "\n";
        $output .= '<caption>Authorize.Net Webhook</caption>' . "\n";
        $output .= '<tr><th colspan="2"><b>Response HTTP Headers</b></th></tr>' . "\n";
        $output .= '<tr><td colspan="2"><pre>' . "\n";
        $output .= var_export($this->headers, true) . "\n";
        $output .= '</pre></td></tr>' . "\n";
        $output .= '<tr><th colspan="2"><b>Response JSON</b></th></tr>' . "\n";
        $output .= '<tr><td colspan="2"><pre>' . "\n";
        $output .= $this->webhookJson . "\n";
        $output .= '</pre></td></tr>' . "\n";
        $output .= '</table>';

        return $output;
    }

    /**
     * Gets a response variable from the Webhook notification
     *
     * @param string $var
     * @return string          requested variable from the API call response
     */
    public function __get(string $var)
    {
        return $this->webhook->{$var};
    }

    /**
     * Validates a webhook signature to determine if the webhook is valid
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $hashedBody = strtoupper(hash_hmac('sha512', $this->webhookJson, $this->signature));
        return (isset($this->headers['X-ANET-SIGNATURE']) &&
            strtoupper(explode('=', $this->headers['X-ANET-SIGNATURE'])[1]) === $hashedBody);
    }

    /**
     * Validates a webhook signature to determine if the webhook is valid
     *
     * @return string|null
     */
    public function getRequestId(): ?string
    {
        return $this->headers['X-REQUEST-ID'] ?? null;
    }

    /**
     * Retrieves all HTTP headers of a given request
     *
     * @return array
     */
    protected function getAllHeaders(): array
    {
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            $headers = [];
            foreach ($_SERVER as $key => $value) {
                if (strpos($key, 'HTTP_') === 0) {
                    $headers[str_replace('_', '-', substr($key, 5))] = $value;
                }
            }
        }
        return $headers ?: [];
    }
}
