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
        Transaction Detail :: Get Held Transaction List
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
            <th>Response Code</th>
            <td><?= $response->responseCode ?></td>
        </tr>
        <tr>
            <th>Authorization Code</th>
            <td><?= $response->authCode ?></td>
        </tr>
        <tr>
            <th>AVS Result Code</th>
            <td><?= $response->avsResultCode ?></td>
        </tr>
        <tr>
            <th>CVV Result Code</th>
            <td><?= $response->cvvResultCode ?></td>
        </tr>
        <tr>
            <th>CAVV Result Code</th>
            <td><?= $response->cavvResultCode ?></td>
        </tr>
        <tr>
            <th>Transaction Id</th>
            <td><?= $response->transId ?></td>
        </tr>
        <tr>
            <th>Reference Transaction ID</th>
            <td><?= $response->refTransID ?></td>
        </tr>
        <tr>
            <th>Transaction Hash</th>
            <td><?= $response->transHash ?></td>
        </tr>
        <tr>
            <th>Account Number</th>
            <td><?= $response->accountNumber ?></td>
        </tr>
        <tr>
            <th>Account Type</th>
            <td><?= $response->accountType ?></td>
        </tr>
        <tr>
            <th>Trans Hash Sha2</th>
            <td><?= $response->transHashSha2 ?></td>
        </tr>
        <tr>
            <th>Message Code</th>
            <td><?= $response->messages->code ?></td>
        </tr>
        <tr>
            <th>Message Description</th>
            <td><?= $response->messages->description ?></td>
        </tr>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
