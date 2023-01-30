<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*************************************************************************************************

Use the AIM JSON API to process an Authorization and Capture transaction (Sale)

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "createTransactionRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "refId":94564789,
      "transactionRequest":{
         "transactionType":"authCaptureTransaction",
         "amount":5,
         "payment":{
            "creditCard":{
               "cardNumber":"4111111111111111",
               "expirationDate":"122020",
               "cardCode":"999"
            }
         },
         "order":{
            "invoiceNumber":"1324567890",
            "description":"this is a test transaction"
         },
         "lineItems":{
            "lineItem":[
               {
                  "itemId":"1",
                  "name":"vase",
                  "description":"Cannes logo",
                  "quantity":"18",
                  "unitPrice":"45.00"
               },
               {
                  "itemId":"2",
                  "name":"desk",
                  "description":"Big Desk",
                  "quantity":"10",
                  "unitPrice":"85.00"
               }
            ]
         },
         "tax":{
            "amount":"4.26",
            "name":"level2 tax name",
            "description":"level2 tax"
         },
         "duty":{
            "amount":"8.55",
            "name":"duty name",
            "description":"duty description"
         },
         "shipping":{
            "amount":"4.26",
            "name":"level2 tax name",
            "description":"level2 tax"
         },
         "poNumber":"456654",
         "customer":{
            "id":"18",
            "email":"someone@blackhole.tv"
         },
         "billTo":{
            "firstName":"Ellen",
            "lastName":"Johnson",
            "company":"Souveniropolis",
            "address":"14 Main Street",
            "city":"Pecan Springs",
            "state":"TX",
            "zip":"44628",
            "country":"USA"
         },
         "shipTo":{
            "firstName":"China",
            "lastName":"Bayles",
            "company":"Thyme for Tea",
            "address":"12 Main Street",
            "city":"Pecan Springs",
            "state":"TX",
            "zip":"44628",
            "country":"USA"
         },
         "customerIP":"192.168.1.1",
         "transactionSettings":{
            "setting":[
               {
                  "settingName":"allowPartialAuth",
                  "settingValue":"false"
               },
               {
                  "settingName":"duplicateWindow",
                  "settingValue":"0"
               },
               {
                  "settingName":"emailCustomer",
                  "settingValue":"false"
               },
               {
                  "settingName":"recurringBilling",
                  "settingValue":"false"
               },
               {
                  "settingName":"testRequest",
                  "settingValue":"false"
               }
            ]
         },
         "userFields":{
            "userField":{
               "name":"favorite_color",
               "value":"blue"
            }
         }
      }
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "transactionResponse":{
      "responseCode":"1",
      "authCode":"QWX20S",
      "avsResultCode":"Y",
      "cvvResultCode":"P",
      "cavvResultCode":"2",
      "transId":"2228446239",
      "refTransID":"",
      "transHash":"56B2D50D73CAB8C6EDE7A92B9BB235BD",
      "testRequest":"0",
      "accountNumber":"XXXX1111",
      "accountType":"Visa",
      "messages":[
         {
            "code":"1",
            "description":"This transaction has been approved."
         }
      ],
      "userFields":[
         {
            "name":"favorite_color",
            "value":"blue"
         }
      ]
   },
   "refId":"94564789",
   "messages":{
      "resultCode":"Ok",
      "message":[
         {
            "code":"I00001",
            "text":"Successful."
         }
      ]
   }
}

*************************************************************************************************/

namespace Authnetjson;

use Exception;

require '../../config.inc.php';

try {
    $request = AuthnetApiFactory::getJsonApiHandler(
        AUTHNET_LOGIN,
        AUTHNET_TRANSKEY,
        AuthnetApiFactory::USE_DEVELOPMENT_SERVER
    );
    $response = $request->createTransactionRequest(
        [
            'refId' => random_int(1000000, 100000000),
            'transactionRequest' => [
                'transactionType' => 'authCaptureTransaction',
                'amount' => 5,
                'payment' => [
                    'creditCard' => [
                        'cardNumber' => '4111111111111111',
                        'expirationDate' => '122025',
                        'cardCode' => '999',
                    ],
                ],
                'order' => [
                    'invoiceNumber' => '1324567890',
                    'description' => 'this is a test transaction',
                ],
                'lineItems' => [
                    'lineItem' => [
                        [
                            'itemId' => '1',
                            'name' => 'vase',
                            'description' => 'Cannes logo',
                            'quantity' => '18',
                            'unitPrice' => '45.00'
                        ],
                        [
                            'itemId' => '2',
                            'name' => 'desk',
                            'description' => 'Big Desk',
                            'quantity' => '10',
                            'unitPrice' => '85.00'
                        ]
                    ]
                ],
                'tax' => [
                    'amount' => '4.26',
                    'name' => 'level2 tax name',
                    'description' => 'level2 tax',
                ],
                'duty' => [
                    'amount' => '8.55',
                    'name' => 'duty name',
                    'description' => 'duty description',
                ],
                'shipping' => [
                    'amount' => '4.26',
                    'name' => 'level2 tax name',
                    'description' => 'level2 tax',
                ],
                'poNumber' => '456654',
                'customer' => [
                    'id' => '18',
                    'email' => 'someone@blackhole.tv',
                ],
                'billTo' => [
                    'firstName' => 'Ellen',
                    'lastName' => 'Johnson',
                    'company' => 'Souveniropolis',
                    'address' => '14 Main Street',
                    'city' => 'Pecan Springs',
                    'state' => 'TX',
                    'zip' => '44628',
                    'country' => 'USA',
                ],
                'shipTo' => [
                    'firstName' => 'China',
                    'lastName' => 'Bayles',
                    'company' => 'Thyme for Tea',
                    'address' => '12 Main Street',
                    'city' => 'Pecan Springs',
                    'state' => 'TX',
                    'zip' => '44628',
                    'country' => 'USA',
                ],
                'customerIP' => '192.168.1.1',
                'transactionSettings' => [
                    'setting' => [
                        0 => [
                            'settingName' => 'allowPartialAuth',
                            'settingValue' => 'false'
                        ],
                        1 => [
                            'settingName' => 'duplicateWindow',
                            'settingValue' => '0'
                        ],
                        2 => [
                            'settingName' => 'emailCustomer',
                            'settingValue' => 'false'
                        ],
                        3 => [
                            'settingName' => 'recurringBilling',
                            'settingValue' => 'false'
                        ],
                        4 => [
                            'settingName' => 'testRequest',
                            'settingValue' => 'false'
                        ]
                    ]
                ],
                'userFields' => [
                    'userField' => [
                        0 => [
                            'name' => 'MerchantDefinedFieldName1',
                            'value' => 'MerchantDefinedFieldValue1',
                        ],
                        1 => [
                            'name' => 'favorite_color',
                            'value' => 'blue',
                        ],
                    ],
                ],
            ],
        ]
    );
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment :: Authorize and Capture (AUTH_CAPTURE)</title>
    <style>
        table { border: 1px solid #cccccc; margin: auto; border-collapse: collapse; max-width: 90%; }
        table td { padding: 3px 5px; vertical-align: top; border-top: 1px solid #cccccc; }
        pre { white-space: pre-wrap; }
        table th { background: #e5e5e5; color: #666666; }
        h1, h2 { text-align: center; }
    </style>
</head>
<body>
    <h1>
        Payment :: Authorize and Capture (AUTH_CAPTURE)
    </h1>
    <h2>
        Results
    </h2>
    <table>
        <tr>
            <th>Response</th>
            <td><?= $response->messages->resultCode ?></td>
        </tr>
        <tr>
            <th>Successful?</th>
            <td><?= $response->isSuccessful() ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <th>Error?</th>
            <td><?= $response->isError() ? 'yes' : 'no' ?></td>
        </tr>
        <?php if ($response->isSuccessful()) : ?>
        <tr>
            <th>Description</th>
            <td><?= $response->transactionResponse->messages[0]->description ?></td>
        </tr>
        <tr>
            <th>Authorization Code</th>
            <td><?= $response->transactionResponse->authCode ?></td>
        </tr>
        <tr>
            <th>Transaction ID</th>
            <td><?= $response->transactionResponse->transId ?></td>
        </tr>
        <tr>
            <th>AVS Result Code</th>
            <td><?= $response->transactionResponse->avsResultCode ?></td>
        </tr>
        <tr>
            <th>CVV Result Code</th>
            <td><?= $response->transactionResponse->cvvResultCode ?></td>
        </tr>
        <tr>
            <th>CAVV Result Code</th>
            <td><?= $response->transactionResponse->cavvResultCode ?></td>
        </tr>
        <tr>
            <th>Account Number</th>
            <td><?= $response->transactionResponse->accountNumber ?></td>
        </tr>
        <tr>
            <th>Account Type</th>
            <td><?= $response->transactionResponse->accountType ?></td>
        </tr>
        <tr>
            <th>refId</th>
            <td><?= $response->refId ?></td>
        </tr>
            <?php foreach ($response->transactionResponse->userFields as $userField) : ?>
                <tr>
                    <th><?= $userField->name ?></th>
                    <td><?= $userField->value ?></td>
                </tr>
            <?php endforeach; ?>
        <?php elseif ($response->isError()) : ?>
        <tr>
            <th>Error Code</th>
            <td><?= $response->getErrorCode() ?></td>
        </tr>
        <tr>
            <th>Error Message</th>
            <td><?=  $response->getErrorText() ?></td>
        </tr>
        <?php endif; ?>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
