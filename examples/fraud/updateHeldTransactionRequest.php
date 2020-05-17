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

Approve or Decline a held Transaction.

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
  "updateHeldTransactionRequest": {
    "merchantAuthentication": {
      "name": "",
      "transactionKey": ""
    },
    "refId": "12345",
    "heldTransactionRequest": {
      "action": "approve",
      "refTransId": "12345"
    }
  }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
    "transactionResponse": {
        "responseCode": "1",
        "authCode": "40C68K",
        "avsResultCode": "Y",
        "cvvResultCode": "P",
        "cavvResultCode": "2",
        "transId": "60010736710",
        "refTransID": "60010736710",
        "transHash": "722F2079BDC5500935D32BEDDF6165B1",
        "accountNumber": "XXXX0015",
        "accountType": "Mastercard",
        "messages": [{
            "code": "1",
            "description": "This transaction has been approved."
        }],
        "transHashSha2": "EFF9481A54853F79C37DF2602339102DBB15D9B42D56FC20373B2E48E6918D2FD4B8334C916301AF01E41A4FC7159FD434725BE9471DF285243F6B0A63A99F76"
    },
    "refId": "12345",
    "messages": {
        "resultCode": "Ok",
        "message": [{
            "code": "I00001",
            "text": "Successful."
        }]
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
    $response = $request->updateHeldTransactionRequest([
        "refId" => "12345",
        "heldTransactionRequest" => [
            "action" => "approve",
            "refTransId" => "12345"
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
    <title>Transaction Detail :: Get Held Transaction List</title>
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
        Transaction Detail :: Get Held Transaction List
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
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
