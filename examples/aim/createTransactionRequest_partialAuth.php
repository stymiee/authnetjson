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

Use the AIM JSON API to process a partial auth transaction

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------

{
   "createTransactionRequest":{
      "merchantAuthentication":{
         "name":"8zY2zT32",
         "transactionKey":"4WDx9a97v5DKY67a"
      },
      "refId":47222105,
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
         "billTo":{
            "firstName":"Ellen",
            "lastName":"Johnson",
            "company":"Souveniropolis",
            "address":"14 Main Street",
            "city":"Pecan Springs",
            "state":"TX",
            "zip":"46226",
            "country":"USA"
         },
         "transactionSettings":{
            "setting":[
               {
                  "settingName":"allowPartialAuth",
                  "settingValue":"true"
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
         }
      }
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------

{
   "transactionResponse":{
      "responseCode":"1",
      "authCode":"LYTVH0",
      "avsResultCode":"Y",
      "cvvResultCode":"P",
      "cavvResultCode":"2",
      "transId":"40033638873",
      "refTransID":"",
      "transHash":"",
      "testRequest":"0",
      "accountNumber":"XXXX1111",
      "accountType":"Visa",
      "prePaidCard":{
         "requestedAmount":"5.00",
         "approvedAmount":"5.00",
         "balanceOnCard":"1.23"
      },
      "messages":[
         {
            "code":"1",
            "description":"This transaction has been approved."
         }
      ],
      "transHashSha2":"5B69E7D68DE994D9A60A0F684BEBA11EE5C97DC22A45BEF70C558D0D9A3476597566EAB841A7B7A63F7768B3458C0E345BACE75AA97462220E16A6DC94F6361C",
      "SupplementalDataQualificationIndicator":0
   },
   "refId":"47222105",
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

    namespace JohnConde\Authnet;

    require '../../config.inc.php';

    $request  = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $response = $request->createTransactionRequest([
        'refId' => random_int(1000000, 100000000),
        'transactionRequest' => [
            'transactionType' => 'authCaptureTransaction',
            'amount' => 5,
            'payment' => [
                'creditCard' => [
                    'cardNumber' => '4111111111111111',
                    'expirationDate' => '122020',
                    'cardCode' => '999',
                ],
            ],
            'order' => [
                'invoiceNumber' => '1324567890',
                'description' => 'this is a test transaction',
            ],
            'billTo' => [
               'firstName' => 'Ellen',
               'lastName' => 'Johnson',
               'company' => 'Souveniropolis',
               'address' => '14 Main Street',
               'city' => 'Pecan Springs',
               'state' => 'TX',
               'zip' => '46226',
               'country' => 'USA',
            ],
            'transactionSettings' => [
                [
                    0 => [
                        'settingName' =>'allowPartialAuth',
                        'settingValue' => 'true'
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
        ],
    ]);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>AIM :: Authorize and Capture</title>
    <style type="text/css">
        table { border: 1px solid #cccccc; margin: auto; border-collapse: collapse; max-width: 90%; }
        table td { padding: 3px 5px; vertical-align: top; border-top: 1px solid #cccccc; }
        pre { white-space: pre-wrap; }
        table th { background: #e5e5e5; color: #666666; }
        h1, h2 { text-align: center; }
    </style>
    </head>
    <body>
        <h1>
            AIM :: Partial Auth
        </h1>
        <h2>
            Results
        </h2>
        <table>
            <tr>
                <th>Response</th>
                <td><?php echo $response->messages->resultCode; ?></td>
            </tr>
            <tr>
                <th>Successful?</th>
                <td><?php echo $response->isSuccessful() ? 'yes' : 'no'; ?></td>
            </tr>
            <tr>
                <th>Error?</th>
                <td><?php echo $response->isError() ? 'yes' : 'no'; ?></td>
            </tr>
            <?php if ($response->isSuccessful()) : ?>
            <tr>
                <th>Is Prepaid Card?</th>
                <td><?php echo $response->isPrePaidCard() ? 'yes' : 'no' ; ?></td>
            </tr>
            <tr>
                <th>Remaining Balance</th>
                <td><?php echo $response->transactionResponse->prePaidCard->balanceOnCard; ?></td>
            </tr>
            <tr>
                <th>Approved Amount</th>
                <td><?php echo $response->transactionResponse->prePaidCard->approvedAmount; ?></td>
            </tr>
            <?php elseif ($response->isError()) : ?>
            <tr>
                <th>Error Code</th>
                <td><?php echo $response->getErrorCode(); ?></td>
            </tr>
            <tr>
                <th>Error Message</th>
                <td><?php echo  $response->getErrorText(); ?></td>
            </tr>
            <?php endif; ?>
        </table>
        <h2>
            Raw Input/Output
        </h2>
<?php
    echo $request, $response;
?>
    </body>
</html>
