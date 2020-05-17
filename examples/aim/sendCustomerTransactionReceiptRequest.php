<?php
/*************************************************************************************************

Use the AIM JSON API to send a customer a transaction receipt

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "sendCustomerTransactionReceiptRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "refId":2241729,
      "transId":"2165665581",
      "customerEmail":"user@example.com",
      "emailSettings":{
         "setting":{
            "settingName":"footerEmailReceipt",
            "settingValue":"some FOOTER stuff"
         }
      }
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "refId":"2241729",
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

**************************************************************************************************/

namespace JohnConde\Authnet;

use Exception;

require '../../config.inc.php';

try {
    $request = AuthnetApiFactory::getJsonApiHandler(
        AUTHNET_LOGIN,
        AUTHNET_TRANSKEY,
        AuthnetApiFactory::USE_DEVELOPMENT_SERVER
    );
    $response = $request->sendCustomerTransactionReceiptRequest([
        'refId' => random_int(1000000, 100000000),
        'transId' => '2165665581',
        'customerEmail' => 'user@example.com',
        'emailSettings' => [
            [
                'settingName' => 'headerEmailReceipt',
                'settingValue' => 'some HEADER stuff'
            ],
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
    <title> Payment :: Receipt Request</title>
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
        Payment :: Receipt Request
    </h1>
    <h2>
        Results
    </h2>
    <table>
        <tr>
            <th>Response</th>
            <td><?= $response->messages->resultCode ?></td>
        </tr>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
