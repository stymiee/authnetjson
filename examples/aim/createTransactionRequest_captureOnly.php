<?php
/*************************************************************************************************

Use the AIM JSON API to a Unlinked Capture (refund for a transaction NOT performed through the API)

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "createTransactionRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "refId":99120820,
      "transactionRequest":{
         "transactionType":"captureOnlyTransaction",
         "amount":5,
         "payment":{
            "creditCard":{
               "cardNumber":"5424000000000015",
               "expirationDate":"122020"
            }
         },
         "authCode":"123456"
      }
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "transactionResponse":{
      "responseCode":"1",
      "authCode":"123456",
      "avsResultCode":"P",
      "cvvResultCode":"",
      "cavvResultCode":"",
      "transId":"2230581248",
      "refTransID":"",
      "transHash":"6636DE0003D951E48B05DC0AAB0FD633",
      "testRequest":"0",
      "accountNumber":"XXXX0015",
      "accountType":"MasterCard",
      "messages":[
         {
            "code":"1",
            "description":"This transaction has been approved."
         }
      ]
   },
   "refId":"99120820",
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
    $response = $request->createTransactionRequest([
        'refId' => random_int(1000000, 100000000),
        'transactionRequest' => [
            'transactionType' => 'captureOnlyTransaction',
            'amount' => 5,
            'payment' => [
                'creditCard' => [
                    'cardNumber' => '5424000000000015',
                    'expirationDate' => '122020'
                ]
            ],
            'authCode' => '123456'
        ],
    ]);
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment :: Capture Only (CAPTURE_ONLY)</title>
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
        Payment :: Capture Only (CAPTURE_ONLY)
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
