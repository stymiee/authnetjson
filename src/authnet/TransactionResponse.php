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
 */
class TransactionResponse {
    /**
     * @var     array Transaction response fields to map to values parsed from a transaction response string
     */
    private $fieldMap = array(
        1 => 'ResponseCode',
        2 => 'ResponseSubcode',
        3 => 'ResponseReasonCode',
        4 => 'ResponseReasonText',
        5 => 'AuthorizationCode',
        6 => 'AVSResponse',
        7 => 'TransactionID',
        8 => 'InvoiceNumber',
        9 => 'Description',
        10 => 'Amount',
        11 => 'Method',
        12 => 'TransactionType',
        13 => 'CustomerID',
        14 => 'FirstName',
        15 => 'LastName',
        16 => 'Company',
        17 => 'Address',
        18 => 'City',
        19 => 'State',
        20 => 'ZipCode',
        21 => 'Country',
        22 => 'Phone',
        23 => 'Fax',
        24 => 'EmailAddress',
        25 => 'ShipToFirstName',
        26 => 'ShipToLastName',
        27 => 'ShipToCompany',
        28 => 'ShipToAddress',
        29 => 'ShipToCity',
        30 => 'ShipToState',
        31 => 'ShipToZip',
        32 => 'ShipToCountry',
        33 => 'Tax',
        34 => 'Duty',
        35 => 'Freight',
        36 => 'TaxExempt',
        37 => 'PurchaseOrderNumber',
        38 => 'MD5Hash',
        39 => 'CardCodeResponse',
        40 => 'CardholderAuthenticationVerificationResponse',
        51 => 'AccountNumber',
        52 => 'CardType',
        53 => 'SplitTenderID',
        54 => 'AmountRequested',
        55 => 'BalanceOnCard'
    );

    /**
     * @var     array Transaction response fields to map to values parsed from a transaction response string
     */
    private $responseArray = array();

    /**
     * Creates out TransactionResponse object and assigns the response variables to an array
     *
     * @param   string  $response   Comma delimited transaction response string
     */
    public function __construct($response)
    {
        $this->responseArray = array_merge(array(null), explode(',', $response));
    }

    /**
     * Gets the requested value out of the response array using the provided key. The location of that value
     * can be accessed via it's numerical location in the array (starting at zero) or using the key for that field
     * as defined by Authorize.Net and mapped in self::$fieldMap.
     *
     * @param   mixed  $field  Name or key of the transaction field to be retrieved
     * @return  string Transaction field to be retrieved
     */
    public function getTransactionResponseField($field)
    {
        $value = null;
        if (is_int($field)) {
            $value = (isset($this->responseArray[$field])) ? $this->responseArray[$field] : $value;
        }
        else {
            if ($key = array_search($field, $this->fieldMap)) {
                $value = $this->responseArray[$key];
            }
        }
        return $value;
    }
}