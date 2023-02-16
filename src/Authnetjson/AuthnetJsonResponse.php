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

use JohnConde\Authnet\Exception\AuthnetCannotSetParamsException;
use JohnConde\Authnet\Exception\AuthnetInvalidJsonException;
use JohnConde\Authnet\Exception\AuthnetTransactionResponseCallException;

/**
 * Adapter for the Authorize.Net JSON API
 *
 * @package   AuthnetJSON
 * @author    John Conde <stymiee@gmail.com>
 * @copyright 2015 - 2023 John Conde <stymiee@gmail.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link      https://github.com/stymiee/authnetjson
 * @see       https://developer.authorize.net/api/reference/
 *
 * @property object $messages
 * @property string $directResponse
 * @property string $validationDirectResponseList
 * @property object $transactionResponse
 *
 * @method null authenticateTestRequest(array $array)
 * @method null createTransactionRequest(array $array)
 * @method null sendCustomerTransactionReceiptRequest(array $array)
 * @method null ARBCancelSubscriptionRequest(array $array)
 * @method null ARBCreateSubscriptionRequest(array $array)
 * @method null ARBGetSubscriptionStatusRequest(array $array)
 * @method null ARBUpdateSubscriptionRequest(array $array)
 * @method null createCustomerPaymentProfileRequest(array $array)
 * @method null createCustomerProfileRequest(array $array)
 * @method null createCustomerProfileTransactionRequest_authCapture(array $array)
 * @method null createCustomerProfileTransactionRequest_authOnly(array $array)
 * @method null createCustomerProfileTransactionRequest_captureOnly(array $array)
 * @method null createCustomerProfileTransactionRequest_priorAuthCapture(array $array)
 * @method null createCustomerProfileTransactionRequest_refund(array $array)
 * @method null createCustomerProfileTransactionRequest_void(array $array)
 * @method null createCustomerShippingAddressRequest(array $array)
 * @method null deleteCustomerPaymentProfileRequest(array $array)
 * @method null deleteCustomerProfileRequest(array $array)
 * @method null deleteCustomerShippingAddressRequest(array $array)
 * @method null getCustomerPaymentProfileRequest(array $array)
 * @method null getCustomerProfileIdsRequest(array $array)
 * @method null getCustomerProfileRequest(array $array)
 * @method null getCustomerShippingAddressRequest(array $array)
 * @method null getHostedProfilePageRequest(array $array)
 * @method null updateCustomerPaymentProfileRequest(array $array)
 * @method null updateCustomerProfileRequest(array $array)
 * @method null updateCustomerShippingAddressRequest(array $array)
 * @method null updateSplitTenderGroupRequest(array $array)
 * @method null validateCustomerPaymentProfileRequest(array $array)
 * @method null getBatchStatisticsRequest(array $array)
 * @method null getSettledBatchListRequest(array $array)
 * @method null getTransactionDetailsRequest(array $array)
 * @method null getTransactionListRequest(array $array)
 * @method null getUnsettledTransactionListRequest(array $array)
 */
class AuthnetJsonResponse
{
    /**
     * @const Indicates the status code of an approved transaction
     */
    const STATUS_APPROVED = 1;

    /**
     * @const Indicates the status code of a declined transaction
     */
    const STATUS_DECLINED = 2;

    /**
     * @const Indicates the status code of a transaction which has encountered an error
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
     * @var object  SimpleXML object representing the API response
     */
    private $response;

    /**
     * @var string  JSON string that is the response sent by Authorize.Net
     */
    private $responseJson;

    /**
     * @var object  TransactionResponse
     */
    private $transactionInfo;

    /**
     * @var array  TransactionResponse
     */
    private $transactionInfoArray;

    /**
     * Creates the response object with the response json returned from the API call
     *
     * @param string $responseJson Response from Authorize.Net
     * @throws AuthnetInvalidJsonException
     */
    public function __construct($responseJson)
    {
        $this->responseJson = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $responseJson);
        if (($this->response = json_decode($this->responseJson, false)) === null) {
            throw new AuthnetInvalidJsonException('Invalid JSON returned by the API');
        }

        if ($this->directResponse
            || $this->validationDirectResponseList
            || isset($this->response->validationDirectResponse)
        ) {
            $response = $this->directResponse ?:
                $this->validationDirectResponseList ?:
                $this->response->validationDirectResponse;
            if (is_array($response)) {
                $this->transactionInfoArray = array_map(
                    static function ($r) {
                        return new TransactionResponse($r);
                    },
                    $response
                );
            } else {
                $this->transactionInfo = new TransactionResponse($response);
                $this->transactionInfoArray = [$this->transactionInfo];
            }
        }
    }

    /**
     * Outputs the response JSON in a human-readable format
     *
     * @return string  HTML table containing debugging information
     */
    public function __toString()
    {
        $output = '<table id="authnet-response">' . "\n";
        $output .= '<caption>Authorize.Net Response</caption>' . "\n";
        $output .= '<tr><th colspan="2"><b>Response JSON</b></th></tr>' . "\n";
        $output .= '<tr><td colspan="2"><pre>' . "\n";
        $output .= $this->responseJson . "\n";
        $output .= '</pre></td></tr>' . "\n";
        $output .= '</table>';

        return $output;
    }

    /**
     * Gets a response variable from the API response
     *
     * @param string $key Name of the response key to be retrieved if it exists
     * @return string requested variable from the API call response
     */
    public function __get($key)
    {
        return isset($this->response->{$key}) ? $this->response->{$key} : null;
    }

    /**
     * @throws AuthnetCannotSetParamsException
     */
    public function __set($key, $value)
    {
        throw new AuthnetCannotSetParamsException(sprintf('You cannot set parameters directly in %s.', __CLASS__));
    }

    /**
     * Checks if a value exists in the response JSON object
     *
     * @param string $key Name of the response key to check existence of
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->response->{$key});
    }

    /**
     * Checks if the API call is not in an error state
     *
     * @return bool    Whether the transaction was in a successful state
     */
    public function isSuccessful()
    {
        return strtolower($this->messages->resultCode) === 'ok';
    }

    /**
     * Checks if the API is reporting an error with the API call
     *
     * @return bool    Whether the transaction was in an error state
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
        return $this->isSuccessful() && $this->checkTransactionStatus(self::STATUS_APPROVED);
    }

    /**
     * Checks if a transaction was completed using a prepaid card
     *
     * @return bool     true if the transaction was completed using a prepaid card
     */
    public function isPrePaidCard()
    {
        return isset($this->transactionResponse->prePaidCard);
    }

    /**
     * Checks if a transaction was declined
     *
     * @return bool     true if the transaction is declined
     */
    public function isDeclined()
    {
        return $this->isSuccessful() && $this->checkTransactionStatus(self::STATUS_DECLINED);
    }

    /**
     * Check to see if the ResponseCode matches the expected value
     *
     * @param int $status
     * @return bool Check to see if the ResponseCode matches the expected value
     */
    protected function checkTransactionStatus($status)
    {
        if ($this->transactionInfo instanceof TransactionResponse) {
            $match = (int)$this->transactionInfo->getTransactionResponseField('ResponseCode') === $status;
        } else {
            $match = (int)$this->transactionResponse->responseCode === $status;
        }
        return $match;
    }

    /**
     * Gets the transaction response field for AIM and CIM transactions.
     *
     * @param mixed $field Name or key of the transaction field to be retrieved
     * @return null|string Transaction field to be retrieved
     * @throws AuthnetTransactionResponseCallException
     */
    public function getTransactionResponseField($field)
    {
        if ($this->transactionInfo instanceof TransactionResponse) {
            return $this->transactionInfo->getTransactionResponseField($field);
        }
        throw new AuthnetTransactionResponseCallException('This API call does not have any transaction response data');
    }

    /**
     * Returns the results of a test charge for each payment account provided when created a customer profile
     *
     * @return array
     */
    public function getTransactionResponses()
    {
        return $this->transactionInfoArray;
    }

    /**
     * Gets the transaction response from Authorize.Net in JSON format for logging purposes
     *
     * @return string transaction response from Authorize.Net in JSON format
     */
    public function getRawResponse()
    {
        return $this->responseJson;
    }

    /**
     * An alias of self::getErrorText()
     *
     * @return string Error response from Authorize.Net
     */
    public function getErrorMessage()
    {
        return $this->getErrorText();
    }

    /**
     * If an error has occurred, returns the error message
     *
     * @return string Error response from Authorize.Net
     */
    public function getErrorText()
    {
        return $this->getError('text');
    }

    /**
     * If an error has occurred, returns the error message
     *
     * @return string Error response from Authorize.Net
     */
    public function getErrorCode()
    {
        return $this->getError('code');
    }

    /**
     * @param string $type Whether to get the error code or text
     * @return string
     */
    private function getError($type)
    {
        $msg = '';
        if ($this->isError()) {
            $prop = sprintf('error%s', ucfirst($type));
            $msg = $this->messages->message[0]->{$type};
            if (@$this->transactionResponse->errors[0]->{$prop}) {
                $msg = $this->transactionResponse->errors[0]->{$prop};
            }
        }
        return $msg;
    }
}
