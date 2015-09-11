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
 * Adapter for the Authorize.Net JSON API
 *
 * @package     AuthnetJSON
 * @author      John Conde <stymiee@gmail.com>
 * @copyright   John Conde <stymiee@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link        https://github.com/stymiee/authnetjson
 * @see         https://developer.authorize.net/api/reference/
 *
 * @property    object  $messages
 * @property    string  $directResponse
 * @property    string  $validationDirectResponse
 * @property    object  $transactionResponse
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
class AuthnetJsonResponse
{
    /**
     * @const Indicates the status code of an approved transaction
     */
    const STATUS_APPROVED = 1;

    /**
     * @const Indicates the status code of an declined transaction
     */
    const STATUS_DECLINED = 2;

    /**
     * @const Indicates the status code of an transaction which has encountered an error
     */
    const STATUS_ERROR = 3;

    /**
     * @const Indicates the status code of a transaction held for review
     */
    const STATUS_HELD = 4;

    /**
     * @const Indicates the status code of a transaction held for review
     */
    const STATUS_PAYPAL_NEED_CONSENT = 5;

    /**
     * @var     object  SimpleXML object representing the API response
     */
    private $response;

    /**
     * @var     string  JSON string that is the response sent by Authorize.Net
     */
    private $responseJson;

    /**
     * @var     object  TransactionResponse
     */
    private $transactionInfo;

    /**
     * Creates the response object with the response json returned from the API call
     *
     * @param   string  $responseJson   Response from Authorize.Net
     * @throws  \JohnConde\Authnet\AuthnetInvalidJsonException
     */
    public function __construct($responseJson)
    {
        $this->responseJson = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $responseJson);
        if (($this->response = json_decode($this->responseJson)) === null) {
            throw new AuthnetInvalidJsonException('Invalid JSON returned by the API');
        }

        $this->transactionInfo = null;
        if (@$this->directResponse || @$this->validationDirectResponse) {
            $dr = (@$this->directResponse) ? $this->directResponse : $this->validationDirectResponse;
            $this->transactionInfo = new TransactionResponse($dr);
        }
    }

    /**
     * Outputs the response JSON in a human readable format
     *
     * @return  string  HTML table containing debugging information
     */
    public function __toString()
    {
        $output  = '';
        $output .= '<table summary="Authorize.Net Response" id="authnet-response">' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<th colspan="2"><b>Response JSON</b></th>' . "\n" . '</tr>' . "\n";
        $output .= '<tr><td colspan="2"><pre>' . "\n";
        $output .= $this->responseJson . "\n";
        $output .= '</pre></td></tr>' . "\n";
        $output .= '</table>';

        return $output;
    }

    /**
     * Gets a response variable from the API response
     *
     * @param   string  $var    unused
     * @return  string          requested variable from the API call response
     */
    public function __get($var)
    {
        return $this->response->{$var};
    }

    /**
     * Checks if the API call is not in an error state
     *
     * @return  bool    Whether the transaction was in an successful state
     */
    public function isSuccessful()
    {
        return strtolower($this->messages->resultCode) === 'ok';
    }

    /**
     * Checks if the API is reporting an error with the API call
     *
     * @return  bool    Whether the transaction was in an error state
     */
    public function isError()
    {
        return strtolower($this->messages->resultCode) === 'error';
    }

    /**
     * Checks if a transaction was approved
     *
     * @return bool     true if the transaction is approved
     */
    public function isApproved()
    {
        return $this->isSuccessful() && $this->checkTransactionStatus(self::STATUS_APPROVED) ;
    }

    /**
     * Checks if a transaction was declined
     *
     * @return bool     true if the transaction is declined
     */
    public function isDeclined()
    {
        return $this->isSuccessful() && $this->checkTransactionStatus(self::STATUS_DECLINED) ;
    }

    /**
     * Check to see if the ResponseCode matches the expected value
     *
     * @param  integer $status
     * @return bool Check to see if the ResponseCode matches the expected value
     */
    protected function checkTransactionStatus($status)
    {
        if ($this->transactionInfo instanceof TransactionResponse) {
            $match = (int) $this->transactionInfo->getTransactionResponseField('ResponseCode') === (int) $status;
        }
        else {
            $match = (int) $this->transactionResponse->responseCode === $status;
        }
        return $match;
    }

    /**
     * Gets the transaction response field for AIM and CIM transactions.
     *
     * @param   mixed  $field  Name or key of the transaction field to be retrieved
     * @return  string Transaction field to be retrieved
     * @throws  \JohnConde\Authnet\AuthnetTransactionResponseCallException
     */
    public function getTransactionResponseField($field)
    {
        if ($this->transactionInfo instanceof TransactionResponse) {
            return $this->transactionInfo->getTransactionResponseField($field);
        }
        throw new AuthnetTransactionResponseCallException('This API call does not have any transaction response data');
    }

    /**
    * Gets the transaction response from Authorize.Net in JSON format for logging purposes
    *
    * @return  string transaction response from Authorize.Net in JSON format
    */
    public function getRawResponse()
    {
        return $this->responseJson;
    }

    /**
     * If an error has occurred, returns the error message
     *
     * @return  string Error response from Authorize.Net
     */
    public function getErrorText()
    {
        $message = '';
        if ($this->isError()) {
            $message = $this->messages->message[0]->text;
            if (@$this->transactionResponse->errors[0]->errorText) {
                $message = $this->transactionResponse->errors[0]->errorText;
            }
        }
        return $message;
    }

    /**
     * An alias of self::getErrorText()
     *
     * @return  string Error response from Authorize.Net
     */
    public function getErrorMessage()
    {
        return $this->getErrorText();
    }

    /**
     * If an error has occurred, returns the error message
     *
     * @return  string Error response from Authorize.Net
     */
    public function getErrorCode()
    {
        $code = '';
        if ($this->isError()) {
            $code = $this->messages->message[0]->code;
            if (@$this->transactionResponse->errors[0]->errorCode) {
                $code = $this->transactionResponse->errors[0]->errorCode;
            }
        }
        return $code;
    }
}