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

namespace JohnConde\Authnet;

use \Curl\Curl;

/**
 * Creates a request to the Authorize.Net JSON endpoints.
 *
 * @package   AuthnetJSON
 * @author    John Conde <stymiee@gmail.com>
 * @copyright John Conde <stymiee@gmail.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link      https://github.com/stymiee/authnetjson
 * @see       https://developer.authorize.net/api/reference/
 *
 * @method AuthnetJsonResponse createTransactionRequest(array $array)
 * @method AuthnetJsonResponse sendCustomerTransactionReceiptRequest(array $array)
 * @method AuthnetJsonResponse ARBCancelSubscriptionRequest(array $array)
 * @method AuthnetJsonResponse ARBCreateSubscriptionRequest(array $array)
 * @method AuthnetJsonResponse ARBGetSubscriptionStatusRequest(array $array)
 * @method AuthnetJsonResponse ARBUpdateSubscriptionRequest(array $array)
 * @method AuthnetJsonResponse getAUJobDetailsRequest(array $array)
 * @method AuthnetJsonResponse getAUJobSummaryRequest(array $array)
 * @method AuthnetJsonResponse createCustomerPaymentProfileRequest(array $array)
 * @method AuthnetJsonResponse createCustomerProfileRequest(array $array)
 * @method AuthnetJsonResponse createCustomerProfileTransactionRequest_authCapture(array $array)
 * @method AuthnetJsonResponse createCustomerProfileTransactionRequest_authOnly(array $array)
 * @method AuthnetJsonResponse createCustomerProfileTransactionRequest_captureOnly(array $array)
 * @method AuthnetJsonResponse createCustomerProfileTransactionRequest_priorAuthCapture(array $array)
 * @method AuthnetJsonResponse createCustomerProfileTransactionRequest_refund(array $array)
 * @method AuthnetJsonResponse createCustomerProfileTransactionRequest_void(array $array)
 * @method AuthnetJsonResponse createCustomerShippingAddressRequest(array $array)
 * @method AuthnetJsonResponse deleteCustomerPaymentProfileRequest(array $array)
 * @method AuthnetJsonResponse deleteCustomerProfileRequest(array $array)
 * @method AuthnetJsonResponse deleteCustomerShippingAddressRequest(array $array)
 * @method AuthnetJsonResponse getCustomerPaymentProfileRequest(array $array)
 * @method AuthnetJsonResponse getCustomerProfileIdsRequest(array $array)
 * @method AuthnetJsonResponse getCustomerProfileRequest(array $array)
 * @method AuthnetJsonResponse getCustomerShippingAddressRequest(array $array)
 * @method AuthnetJsonResponse getHostedPaymentPageRequest(array $array)
 * @method AuthnetJsonResponse getHostedProfilePageRequest(array $array)
 * @method AuthnetJsonResponse getMerchantDetailsRequest(array $array)
 * @method AuthnetJsonResponse getUnsettledTransactionListRequest(array $array)
 * @method AuthnetJsonResponse updateCustomerPaymentProfileRequest(array $array)
 * @method AuthnetJsonResponse updateCustomerProfileRequest(array $array)
 * @method AuthnetJsonResponse updateCustomerShippingAddressRequest(array $array)
 * @method AuthnetJsonResponse updateHeldTransactionRequest(array $array)
 * @method AuthnetJsonResponse updateSplitTenderGroupRequest(array $array)
 * @method AuthnetJsonResponse validateCustomerPaymentProfileRequest(array $array)
 * @method AuthnetJsonResponse getBatchStatisticsRequest(array $array)
 * @method AuthnetJsonResponse getSettledBatchListRequest(array $array)
 * @method AuthnetJsonResponse getTransactionDetailsRequest(array $array)
 * @method AuthnetJsonResponse getTransactionListRequest(array $array)
 */
class AuthnetJsonRequest
{
    /**
     * @var int     Maximum number of retires making HTTP request before failure
     */
    private const MAX_RETRIES = 3;

    /**
     * @var string  Authorize.Net API login ID
     */
    private $login;

    /**
     * @var string  Authorize.Net API Transaction Key
     */
    private $transactionKey;

    /**
     * @var string  URL endpoint for processing a transaction
     */
    private $url;

    /**
     * @var string  JSON formatted API request
     */
    private $requestJson;

    /**
     * @var object  Wrapper object representing an endpoint
     */
    private $processor;

    /**
     * @var int Counts number of retries to make API call. Up to self::MAX_RETRIES
     */
    private $retries;

    /**
     * Creates the request object by setting the Authorize.Net credentials and URL of the endpoint to be used
     * for the API call.
     *
     * @param string $login          Authorize.Net API login ID
     * @param string $transactionKey Authorize.Net API Transaction Key
     * @param string $api_url        URL endpoint for processing a transaction
     */
    public function __construct(string $login, string $transactionKey, string $api_url)
    {
        $this->login          = $login;
        $this->transactionKey = $transactionKey;
        $this->url            = $api_url;
    }

    /**
     * Outputs the account credentials, endpoint URL, and request JSON in a human readable format
     *
     * @return string  HTML table containing debugging information
     */
    public function __toString()
    {
        $output  = '<table id="authnet-request">'."\n";
        $output .= '<caption>Authorize.Net Request</caption>'."\n";
        $output .= '<tr><th colspan="2"><b>Class Parameters</b></th></tr>'."\n";
        $output .= '<tr><td><b>API Login ID</b></td><td>'.$this->login.'</td></tr>'."\n";
        $output .= '<tr><td><b>Transaction Key</b></td><td>'.$this->transactionKey.'</td></tr>'."\n";
        $output .= '<tr><td><b>Authnet Server URL</b></td><td>'.$this->url.'</td></tr>'."\n";
        $output .= '<tr><th colspan="2"><b>Request JSON</b></th></tr>'."\n";
        if (!empty($this->requestJson)) {
            $output .= '<tr><td colspan="2"><pre>'."\n";
            $output .= $this->requestJson."\n";
            $output .= '</pre></td></tr>'."\n";
        }
        $output .= '</table>';

        return $output;
    }

    /**
     * The __set() method should never be used as all values to be made in the API call must be passed as an array
     *
     * @param  string $name  unused
     * @param  mixed  $value unused
     * @throws AuthnetCannotSetParamsException
     */
    public function __set($name, $value)
    {
        throw new AuthnetCannotSetParamsException(sprintf('You cannot set parameters directly in %s.', __CLASS__));
    }

    /**
     * Magic method that dynamically creates our API call based on the name of the method in the client code and
     * the array passed as its parameter.
     *
     * @param  string $api_call name of the API call to be made
     * @param  array  $args     the array to be passed to the API
     * @return AuthnetJsonResponse
     * @throws AuthnetCurlException
     * @throws AuthnetInvalidJsonException
     */
    public function __call($api_call, array $args)
    {
        $authentication = [
            'merchantAuthentication' => [
                'name'           => $this->login,
                'transactionKey' => $this->transactionKey,
            ]
        ];
        $call = [];
        if (count($args)) {
            $call = $args[0];
        }
        $parameters = [
            $api_call => $authentication + $call
        ];
        $this->requestJson = json_encode($parameters);

        $response = $this->process();
        return new AuthnetJsonResponse($response);
    }

    /**
     * Makes POST request with retry logic.
     */
    private function makeRequest() : void
    {
        $this->retries = 0;
        while ($this->retries < self::MAX_RETRIES) {
            $this->processor->post($this->url, $this->requestJson);
            if (!$this->processor->error) {
                break;
            }
            $this->retries++;
        }
    }

    /**
     * Tells the handler to make the API call to Authorize.Net.
     *
     * @return string  JSON string containing API response
     * @throws AuthnetCurlException
     */
    private function process() : string
    {
        $this->makeRequest();
        if (!$this->processor->error && isset($this->processor->response)) {
            return $this->processor->response;
        }
        $error_message = null;
        $error_code    = null;
        if ($this->processor->error_code || $this->processor->error_message) {
            $error_message = $this->processor->error_message;
            $error_code    = $this->processor->error_code;
        }
        throw new AuthnetCurlException(sprintf('Connection error: %s (%s)', $error_message, $error_code));
    }

    /**
     * Sets the handler to be used to handle our API call. Mainly used for unit testing as Curl is used by default.
     *
     * @param Curl $processor
     */
    public function setProcessHandler($processor) : void
    {
        $this->processor = $processor;
    }

    /**
     * Gets the request sent to Authorize.Net in JSON format for logging purposes.
     *
     * @return string transaction request sent to Authorize.Net in JSON format
     */
    public function getRawRequest() : string
    {
        return $this->requestJson;
    }
}
