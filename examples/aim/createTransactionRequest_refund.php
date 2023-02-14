<?php
/*************************************************************************************************

Use the AIM JSON API to a Refund transaction (credit)

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "createTransactionRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "refId":95063294,
      "transactionRequest":{
         "transactionType":"refundTransaction",
         "amount":5,
         "payment":{
            "creditCard":{
               "cardNumber":"4111111111111111",
               "expirationDate":"122020"
            }
         },
         "authCode":"2165668159"
      }
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "transactionResponse":{
      "responseCode":"1",
      "authCode":"",
      "avsResultCode":"P",
      "cvvResultCode":"",
      "cavvResultCode":"",
      "transId":"2230581367",
      "refTransID":"",
      "transHash":"E659A47D6DCC71D618533E17A80E818A",
      "testRequest":"0",
      "accountNumber":"XXXX1111",
      "accountType":"Visa",
      "messages":[
         {
            "code":"1",
            "description":"This transaction has been approved."
         }
      ]
   },
   "refId":"95063294",
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
            'transactionType' => 'refundTransaction',
            'amount' => 5,
            'payment' => [
                'creditCard' => [
                    'cardNumber' => '4111111111111111',
                    'expirationDate' => '122020',
                ]
            ],
            'authCode' => '2165668159'
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
    <title>Payment :: Refund (Credit)</title>
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
        Payment :: Refund (Credit)
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
            <th>Reference Transaction ID</th>
            <td><?= $response->transactionResponse->refTransID ?></td>
        </tr>
        <tr>
            <th>Transaction Hash</th>
            <td><?= $response->transactionResponse->transHash ?></td>
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
            <th>Is Test Request?</th>
            <td><?= $response->transactionResponse->testRequest ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <th>Account Type</th>
            <td><?= $response->transactionResponse->accountType ?></td>
        </tr>
        <?php elseif ($response->isError()) : ?>
        <tr>
            <th>Error Code</th>
            <td><?= $response->getErrorCode() ?></td>
        </tr>
        <tr>
            <th>Error Message</th>
            <td><?= $response->getErrorText() ?></td>
        </tr>
        <?php endif; ?>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
