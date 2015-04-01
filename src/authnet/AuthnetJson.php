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

/**
 * Adapter for the Authorize.Net JSON API
 *
 * @package     AuthnetJSON
 * @author      John Conde <stymiee@gmail.com>
 * @copyright   John Conde <stymiee@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link        https://github.com/stymiee/authnetjson
 * @see         https://developer.authorize.net/api/reference/
 *
 * @method      null createTransactionRequest(array $array)                                 process a payment
 * @method      null sendCustomerTransactionReceiptRequest(array $array)                    get a list of unsettled transactions
 * @method      null ARBCancelSubscriptionRequest(array $array)                             cancel a subscription
 * @method      null ARBCreateSubscriptionRequest(array $array)                             create a subscription
 * @method      null ARBGetSubscriptionStatusRequest(array $array)                          get a subscription's status
 * @method      null ARBUpdateSubscriptionRequest(array $array)                             update a subscription
 * @method      null createCustomerPaymentProfileRequest(array $array)                      create a payment profile
 * @method      null createCustomerProfileRequest(array $array)                             create a customer profile
 * @method      null createCustomerProfileTransactionRequest_authCapture(array $array)      process an Authorization and Capture transaction (Sale)
 * @method      null createCustomerProfileTransactionRequest_authOnly(array $array)         process an Authorization Only transaction
 * @method      null createCustomerProfileTransactionRequest_captureOnly(array $array)      process a Capture Only transaction
 * @method      null createCustomerProfileTransactionRequest_priorAuthCapture(array $array) process a Prior Authorization Capture transaction
 * @method      null createCustomerProfileTransactionRequest_refund(array $array)           process a Refund (credit)
 * @method      null createCustomerProfileTransactionRequest_void(array $array)             void a transaction
 * @method      null createCustomerShippingAddressRequest(array $array)                     create a shipping profile
 * @method      null deleteCustomerPaymentProfileRequest(array $array)                      delete a payment profile
 * @method      null deleteCustomerProfileRequest(array $array)                             delete a customer profile
 * @method      null deleteCustomerShippingAddressRequest(array $array)                     delete a shipping profile
 * @method      null getCustomerPaymentProfileRequest(array $array)                         retrieve a payment profile
 * @method      null getCustomerProfileIdsRequest(array $array)                             retrieve a list of profile IDs
 * @method      null getCustomerProfileRequest(array $array)                                retrieve a customer profile
 * @method      null getCustomerShippingAddressRequest(array $array)                        retrieve a shipping address
 * @method      null getHostedProfilePageRequest(array $array)                              retrieve a hosted payment page token
 * @method      null updateCustomerPaymentProfileRequest(array $array)                      update a customer profile
 * @method      null updateCustomerProfileRequest(array $array)                             update a customer profile
 * @method      null updateCustomerShippingAddressRequest(array $array)                     update a shipping address
 * @method      null updateSplitTenderGroupRequest(array $array)                            update a split tender transaction
 * @method      null validateCustomerPaymentProfileRequest(array $array)                    validate a payment profile
 * @method      null getBatchStatisticsRequest(array $array)                                get a summary of a settled batch
 * @method      null getSettledBatchListRequest(array $array)                               get a list of settled batches
 * @method      null getTransactionDetailsRequest(array $array)                             get the details of a transaction
 * @method      null getTransactionListRequest(array $array)                                get a list of transaction in a batch
 * @method      null getUnsettledTransactionListRequest(array $array)                       get a list of unsettled transactions
 */
class AuthnetJson
{
    /**
     * @var     string  Authorize.Net API login ID
     */
    private $login;

    /**
     * @var     string  Authorize.Net API Transaction Key
     */
    private $transaction_key;

    /**
     * @var     string  URL endpoint for processing a transaction
     */
    private $url;

    /**
     * @var     string  JSON formatted API request
     */
    private $request_json;

    /**
     * @var     object  Wrapper object repsenting an endpoint
     */
    private $processor;

    /**
     * @var     object  SimpleXML object representing the API response
     */
    private $response;

    /**
     * @var     string  JSON response
     */
    private $response_json;

    /**
     * @param   string  $login              Authorize.Net API login ID
     * @param   string  $transaction_key    Authorize.Net API Transaction Key
     * @param   string  $api_url            URL endpoint for processing a transaction
     */
	public function __construct($login, $transaction_key, $api_url)
	{
		$this->login           = $login;
        $this->transaction_key = $transaction_key;
        $this->url             = $api_url;
	}

    /**
     * @return  string  HTML table containing debugging information
     */
	public function __toString()
	{
	    $output  = '';
        $output .= '<table summary="Authorize.Net Results" id="authnet">' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<th colspan="2"><b>Class Parameters</b></th>' . "\n" . '</tr>' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<td><b>API Login ID</b></td><td>' . $this->login . '</td>' . "\n" . '</tr>' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<td><b>Transaction Key</b></td><td>' . $this->transaction_key . '</td>' . "\n" . '</tr>' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<td><b>Authnet Server URL</b></td><td>' . $this->url . '</td>' . "\n" . '</tr>' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<th colspan="2"><b>Request JSON</b></th>' . "\n" . '</tr>' . "\n";
        if (!empty($this->request_json)) {
            $output .= '<tr><td colspan="2"><pre>' . "\n";
            $output .= $this->request_json . "\n";
            $output .= '</pre></td></tr>' . "\n";
        }
        if (!empty($this->response_json)) {
            $output .= '<tr>' . "\n\t\t" . '<th colspan="2"><b>Response JSON</b></th>' . "\n" . '</tr>' . "\n";
            $output .= '<tr><td colspan="2"><pre>' . "\n";
            $output .= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->response_json). "\n";
            $output .= '</pre></td></tr>' . "\n";
        }
        $output .= '</table>';

        return $output;
	}

    /**
     * @return  string  requested variable from the API call response
     */
    public function __get($var)
	{
	    return $this->response->{$var};
	}

    /**
     * @throws  \JohnConde\Authnet\AuthnetCannotSetParamsException
     */
    public function __set($key, $value)
	{
        throw new AuthnetCannotSetParamsException('You cannot set parameters directly in ' . __CLASS__ . '.');
	}

    /**
     * @returns null
     */
    public function __call($api_call, $args)
	{
        $authentication = array(
            'merchantAuthentication' => array(
                'name'           => $this->login,
                'transactionKey' => $this->transaction_key,
            )
        );
        $call = array();
        if (count($args)) {
            $call = $args[0];
        }
        $parameters = array(
            $api_call => $authentication + $call
        );
        $this->request_json = json_encode($parameters);

		$this->process();
	}

    /**
     * @throws  \JohnConde\Authnet\AuthnetInvalidJsonException
     */
    private function process()
    {
        $this->response_json = $this->processor->process($this->url, $this->request_json);
        if(($this->response = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->response_json))) === null) {
            throw new AuthnetInvalidJsonException('Invalid JSON returned by the API');
        }
    }

    /**
     * @return  bool    Whether the transaction was in an successful state
     */
    public function isSuccessful()
    {
        return strtolower($this->messages->resultCode) === 'ok';
    }

    /**
     * @return  bool    Whether the transaction was in an error state
     */
    public function isError()
    {
        return strtolower($this->messages->resultCode) === 'error';
    }

    /**
     * @param   object  $processor
     */
    public function setProcessHandler($processor)
    {
        $this->processor = $processor;
    }
}