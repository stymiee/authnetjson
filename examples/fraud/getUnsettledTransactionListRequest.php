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

Use this function to get data for suspicious transactions. The function will return data for up to
1000 of the most recent transactions in a single request. Paging options can be sent to limit the
result set or to retrieve additional transactions beyond the 1000 transaction limit. You can add
the sorting and paging options shown below to customize the result set.

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
  "getUnsettledTransactionListRequest": {
    "merchantAuthentication": {
      "name": "",
      "transactionKey": ""
    },
    "refId": "12345",
    "status": "pendingApproval",
    "sorting": {
      "orderBy": "submitTimeUTC",
      "orderDescending": false
    },
    "paging": {
      "limit": "100",
      "offset": "1"
    }
  }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
    "transactions": [{
        "transId": "60010736710",
        "submitTimeUTC": "2016-11-18T18:21:41Z",
        "submitTimeLocal": "2016-11-18T10:21:41",
        "transactionStatus": "FDSPendingReview",
        "invoiceNumber": "INV-12345",
        "firstName": "Ellen",
        "lastName": "Johnson",
        "accountType": "Mastercard",
        "accountNumber": "XXXX0015",
        "settleAmount": 50000,
        "marketType": "eCommerce",
        "product": "Card Not Present",
        "fraudInformation": {
            "fraudFilterList": [
                "Amount Filter"
            ],
            "fraudAction": "Review"
        }
    }],
    "totalNumInResultSet": 1,
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
    $response = $request->getUnsettledTransactionListRequest([
        "refId" => "12345",
        "status" => "pendingApproval",
        "sorting" => [
          "orderBy" => "submitTimeUTC",
          "orderDescending" => false
        ],
        "paging" => [
          "limit" => "100",
          "offset" => "1"
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
            <th>Number of Results</th>
            <td><?= $response->totalNumInResultSet ?></td>
        </tr>
        <?php foreach ($response->transactions as $transaction) : ?>
        <tr>
            <th>Transaction ID: <?= $transaction->transId ?></th>
            <td>
                Submit Time UTC: <?= $transaction->submitTimeUTC ?><br>
                Submit Time Local: <?= $transaction->submitTimeLocal ?><br>
                Transaction Status: <?= $transaction->transactionStatus ?><br>
                Invoice Number: <?= $transaction->invoiceNumber ?><br>
                First Name: <?= $transaction->firstName ?><br>
                Last Name: <?= $transaction->lastName ?><br>
                Account Type: <?= $transaction->accountType ?><br>
                Account Number: <?= $transaction->accountNumber ?><br>
                Settle Amount: <?= $transaction->settleAmount ?><br>
                Market Type: <?= $transaction->marketType ?><br>
                Product: <?= $transaction->product ?><br>
                Fraud Action: <?= $transaction->fraudInformation->fraudAction ?><br>
                Fraud Reasons: <?= implode(', ', $transaction->fraudInformation->fraudFilterList) ?><br>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
