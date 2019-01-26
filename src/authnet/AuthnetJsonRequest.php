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
 * Creates a request to the Authorize.Net JSON endpoints
 *
 * @package     AuthnetJSON
 * @author      John Conde <stymiee@gmail.com>
 * @copyright   John Conde <stymiee@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link        https://github.com/stymiee/authnetjson
 * @see         https://developer.authorize.net/api/reference/
 *
 * @method      AuthnetJsonResponse createTransactionRequest(array $array)                                 process a payment
 * @method      AuthnetJsonResponse sendCustomerTransactionReceiptRequest(array $array)                    get a list of unsettled transactions
 * @method      AuthnetJsonResponse ARBCancelSubscriptionRequest(array $array)                             cancel a subscription
 * @method      AuthnetJsonResponse ARBCreateSubscriptionRequest(array $array)                             create a subscription
 * @method      AuthnetJsonResponse ARBGetSubscriptionStatusRequest(array $array)                          get a subscription's status
 * @method      AuthnetJsonResponse ARBUpdateSubscriptionRequest(array $array)                             update a subscription
 * @method      AuthnetJsonResponse createCustomerPaymentProfileRequest(array $array)                      create a payment profile
 * @method      AuthnetJsonResponse createCustomerProfileRequest(array $array)                             create a customer profile
 * @method      AuthnetJsonResponse createCustomerProfileTransactionRequest_authCapture(array $array)      process an Authorization and Capture transaction (Sale)
 * @method      AuthnetJsonResponse createCustomerProfileTransactionRequest_authOnly(array $array)         process an Authorization Only transaction
 * @method      AuthnetJsonResponse createCustomerProfileTransactionRequest_captureOnly(array $array)      process a Capture Only transaction
 * @method      AuthnetJsonResponse createCustomerProfileTransactionRequest_priorAuthCapture(array $array) process a Prior Authorization Capture transaction
 * @method      AuthnetJsonResponse createCustomerProfileTransactionRequest_refund(array $array)           process a Refund (credit)
 * @method      AuthnetJsonResponse createCustomerProfileTransactionRequest_void(array $array)             void a transaction
 * @method      AuthnetJsonResponse createCustomerShippingAddressRequest(array $array)                     create a shipping profile
 * @method      AuthnetJsonResponse deleteCustomerPaymentProfileRequest(array $array)                      delete a payment profile
 * @method      AuthnetJsonResponse deleteCustomerProfileRequest(array $array)                             delete a customer profile
 * @method      AuthnetJsonResponse deleteCustomerShippingAddressRequest(array $array)                     delete a shipping profile
 * @method      AuthnetJsonResponse getCustomerPaymentProfileRequest(array $array)                         retrieve a payment profile
 * @method      AuthnetJsonResponse getCustomerProfileIdsRequest(array $array)                             retrieve a list of profile IDs
 * @method      AuthnetJsonResponse getCustomerProfileRequest(array $array)                                retrieve a customer profile
 * @method      AuthnetJsonResponse getCustomerShippingAddressRequest(array $array)                        retrieve a shipping address
 * @method      AuthnetJsonResponse getHostedProfilePageRequest(array $array)                              retrieve a hosted payment page token
 * @method      AuthnetJsonResponse updateCustomerPaymentProfileRequest(array $array)                      update a customer profile
 * @method      AuthnetJsonResponse updateCustomerProfileRequest(array $array)                             update a customer profile
 * @method      AuthnetJsonResponse updateCustomerShippingAddressRequest(array $array)                     update a shipping address
 * @method      AuthnetJsonResponse updateSplitTenderGroupRequest(array $array)                            update a split tender transaction
 * @method      AuthnetJsonResponse validateCustomerPaymentProfileRequest(array $array)                    validate a payment profile
 * @method      AuthnetJsonResponse getBatchStatisticsRequest(array $array)                                get a summary of a settled batch
 * @method      AuthnetJsonResponse getSettledBatchListRequest(array $array)                               get a list of settled batches
 * @method      AuthnetJsonResponse getTransactionDetailsRequest(array $array)                             get the details of a transaction
 * @method      AuthnetJsonResponse getTransactionListRequest(array $array)                                get a list of transaction in a batch
 * @method      AuthnetJsonResponse getUnsettledTransactionListRequest(array $array)                       get a list of unsettled transactions
 */
class AuthnetJsonRequest
{
    /**
     * @var     string  Authorize.Net API login ID
     */
    private $login;

    /**
     * @var     string  Authorize.Net API Transaction Key
     */
    private $transactionKey;

    /**
     * @var     string  URL endpoint for processing a transaction
     */
    private $url;

    /**
     * @var     string  JSON formatted API request
     */
    private $requestJson;

    /**
     * @var     object  Wrapper object representing an endpoint
     */
    private $processor;

    /**
     * Creates the request object by setting the Authorize.Net credentials and URL of the endpoint to be used
     * for the API call
     *
     * @param   string  $login              Authorize.Net API login ID
     * @param   string  $transactionKey     Authorize.Net API Transaction Key
     * @param   string  $api_url            URL endpoint for processing a transaction
     */
    public function __construct($login, $transactionKey, $api_url)
    {
        $this->login          = $login;
        $this->transactionKey = $transactionKey;
        $this->url            = $api_url;
    }

    /**
     * Outputs the account credentials, endpoint URL, and request JSON in a human readable format
     *
     * @return  string  HTML table containing debugging information
     */
    public function __toString()
    {
        $output  = '<table summary="Authorize.Net Request" id="authnet-request">'."\n";
        $output .= '<tr>'."\n\t\t".'<th colspan="2"><b>Class Parameters</b></th>'."\n".'</tr>'."\n";
        $output .= '<tr>'."\n\t\t".'<td><b>API Login ID</b></td><td>'.$this->login.'</td>'."\n".'</tr>'."\n";
        $output .= '<tr>'."\n\t\t".'<td><b>Transaction Key</b></td><td>'.$this->transactionKey.'</td>'."\n".'</tr>'."\n";
        $output .= '<tr>'."\n\t\t".'<td><b>Authnet Server URL</b></td><td>'.$this->url.'</td>'."\n".'</tr>'."\n";
        $output .= '<tr>'."\n\t\t".'<th colspan="2"><b>Request JSON</b></th>'."\n".'</tr>'."\n";
        if (!empty($this->requestJson)) {
            $output .= '<tr><td colspan="2"><pre>'."\n";
            $output .= $this->requestJson."\n";
            $output .= '</pre></td></tr>'."\n";
        }
        $output .= '</table>';

        return $output;
    }

    /**
     * The __set() method should never be used as all values to ba made in the APi call must be passed as an array
     *
     * @param   string  $name       unused
     * @param   mixed   $value      unused
     * @throws  \JohnConde\Authnet\AuthnetCannotSetParamsException
     */
    public function __set($name, $value)
    {
        throw new AuthnetCannotSetParamsException(sprintf('You cannot set parameters directly in %s.', __CLASS__));
    }

    /**
     * Magic method that dynamically creates our API call based on the name of the method in the client code and
     * the array passed as its parameter
     *
     * @param   string  $api_call   name of the API call to be made
     * @param   array   $args       the array to be passed to the API
     * @return  \JohnConde\Authnet\AuthnetJsonResponse
     * @throws  \JohnConde\Authnet\AuthnetCurlException
     * @throws  \JohnConde\Authnet\AuthnetInvalidJsonException
     */
    public function __call($api_call, Array $args)
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
     * Tells the handler to make the API call to Authorize.Net
     *
     * @throws  \JohnConde\Authnet\AuthnetCurlException
     */
    private function process()
    {
        $this->processor->post($this->url, $this->requestJson);

        if (!$this->processor->error) {
            return $this->processor->response;
        }
        $error_message = null;
        $error_code    = null;
        if ($this->processor->error_code) {
            $error_message = $this->processor->error_message;
            $error_code    = $this->processor->error_code;
        }
        throw new AuthnetCurlException(sprintf('Connection error: %s (%s)', $error_message, $error_code));
    }

    /**
     * Sets the handler to be used to handle our API call. Mainly used for unit testing as Curl is used by default.
     *
     * @param   object  $processor
     */
    public function setProcessHandler($processor)
    {
        $this->processor = $processor;
    }

    /**
     * Gets the request sent to Authorize.Net in JSON format for logging purposes
     *
     * @return  string transaction request sent to Authorize.Net in JSON format
     */
    public function getRawRequest()
    {
        return $this->requestJson;
    }
}