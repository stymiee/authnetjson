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

Use the ARB JSON API to create a subscription

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "ARBCreateSubscriptionRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "refId":"Sample",
      "subscription":{
         "name":"Sample subscription",
         "paymentSchedule":{
            "interval":{
               "length":"1",
               "unit":"months"
            },
            "startDate":"2012-03-15",
            "totalOccurrences":"12",
            "trialOccurrences":"1"
         },
         "amount":"10.29",
         "trialAmount":"0.00",
         "payment":{
            "creditCard":{
               "cardNumber":"4111111111111111",
               "expirationDate":"2016-08"
            }
         },
         "billTo":{
            "firstName":"John",
            "lastName":"Smith"
         }
      }
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "subscriptionId":"2341621",
   "refId":"Sample",
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
    $response = $request->ARBCreateSubscriptionRequest([
        'refId' => 'Sample',
        'subscription' => [
            'name' => 'Sample subscription',
            'paymentSchedule' => [
                'interval' => [
                    'length' => '1',
                    'unit' => 'months'
                ],
                'startDate' => '2015-04-18',
                'totalOccurrences' => AuthnetJson::BOUNDLESS_OCCURRENCES,
                'trialOccurrences' => '1'
            ],
            'amount' => '10.29',
            'trialAmount' => '0.00',
            'payment' => [
                'creditCard' => [
                    'cardNumber' => '4111111111111111',
                    'expirationDate' => '2016-08'
                ]
            ],
            'billTo' => [
                'firstName' => 'John',
                'lastName' => 'Smith'
            ]
        ]
    ]);
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>ARB :: Create Subscription</title>
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
        ARB :: Create Subscription
    </h1>
    <h2>
        Results
    </h2>
    <table>
        <tr>
            <th>Successful?</th>
            <td><?= $response->isSuccessful() ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <th>Error?</th>
            <td><?= $response->isError() ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <th>Result Code</th>
            <td><?= $response->messages->resultCode ?></td>
        </tr>
        <tr>
            <th>Message Code</th>
            <td><?= $response->messages->message[0]->code ?></td>
        </tr>
        <tr>
            <th>Message</th>
            <td><?= $response->messages->message[0]->text ?></td>
        </tr>
        <tr>
            <th>Subscription ID</th>
            <td><?= $response->subscriptionId ?></td>
        </tr>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
