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

Use the ARB API to get a subscription's status

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "ARBGetSubscriptionStatusRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "refId":"Sample",
      "subscriptionId":"1207505"
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "note":"Status with a capital 'S' is obsolete.",
   "status":"canceled",
   "Status":"canceled",
   "statusSpecified":true,
   "StatusSpecified":true,
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

namespace JohnConde\Authnet;

use Exception;

require '../../config.inc.php';

try {
    $request = AuthnetApiFactory::getJsonApiHandler(
        AUTHNET_LOGIN,
        AUTHNET_TRANSKEY,
        AuthnetApiFactory::USE_DEVELOPMENT_SERVER
    );
    $response = $request->ARBGetSubscriptionStatusRequest([
        'refId' => 'Sample',
        'subscriptionId' => '1207505'
    ]);
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>ARB :: Get Subscription Status</title>
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
        ARB :: Get Subscription Status
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
            <th>Message Code</th>
            <td><?= $response->messages->message[0]->code ?></td>
        </tr>
        <tr>
            <th>Message Text</th>
            <td><?= $response->messages->message[0]->text ?></td>
        </tr>
        <tr>
            <th>Successful?</th>
            <td><?= $response->isSuccessful() ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <th>Error?</th>
            <td><?= $response->isError() ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <th>Reference ID</th>
            <td><?= $response->refId ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?= $response->status ?></td>
        </tr>
        <tr>
            <th>Status Specified</th>
            <td><?= $response->statusSpecified ?></td>
        </tr>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
