<?php
/*************************************************************************************************

Use the AIM JSON API to process an Prior Authorization Capture transaction

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "createTransactionRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "refId":14254181,
      "transactionRequest":{
         "transactionType":"priorAuthCaptureTransaction",
         "refTransId":"2165665234"
      }
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "transactionResponse":{
      "responseCode":"1",
      "authCode":"1VT65S",
      "avsResultCode":"P",
      "cvvResultCode":"",
      "cavvResultCode":"",
      "transId":"2230581333",
      "refTransID":"2230581333",
      "transHash":"414220CECDB539F68435A4830246BDA5",
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
   "refId":"34913421",
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
    $response = $request->createTransactionRequest([
        'refId' => random_int(1000000, 100000000),
        'transactionRequest' => [
            'transactionType' => 'priorAuthCaptureTransaction',
            'refTransId' => '2230581333'
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
    <title>Payment :: Prior Authorization Capture</title>
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
    Payment :: Prior Authorization Capture
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
        <th>code</th>
        <td><?= $response->messages->message[0]->code ?></td>
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
        <th>transId</th>
        <td><?= $response->transactionResponse->transId ?></td>
    </tr>
</table>
<h2>
    Raw Input/Output
</h2>
<?= $request, $response ?>
</body>
</html>
