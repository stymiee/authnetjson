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

    namespace JohnConde\Authnet;

    require '../../config.inc.php';

    $request = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title></title>
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
    AIM :: Capture Only
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
        <th>code</th>
        <td><?php echo $response->messages->message[0]->code; ?></td>
    </tr>
    <tr>
        <th>Successful?</th>
        <td><?php echo $response->isSuccessful() ? 'yes' : 'no'; ?></td>
    </tr>
    <tr>
        <th>Error?</th>
        <td><?php echo $response->isError() ? 'yes' : 'no'; ?></td>
    </tr>
    <tr>
        <th>transId</th>
        <td><?php echo $response->transactionResponse->transId; ?></td>
    </tr>
</table>
<h2>
    Raw Input/Output
</h2>
<?php
echo $request, $response;
?>
</body>
</html>
