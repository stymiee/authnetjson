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

Use the Transaction Details API to get a list of unsettled transactions

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "getUnsettledTransactionListRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      }
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "transactions":[
      {
         "transId":"2228546203",
         "submitTimeUTC":"2015-02-16T03:34:43Z",
         "submitTimeLocal":"2015-02-15T20:34:43",
         "transactionStatus":"authorizedPendingCapture",
         "invoiceNumber":"1324567890",
         "firstName":"Ellen",
         "lastName":"Johnson",
         "accountType":"MasterCard",
         "accountNumber":"XXXX0015",
         "settleAmount":5.00,
         "marketType":"eCommerce",
         "product":"Card Not Present",
         "hasReturnedItemsSpecified":false
      },
      {
         "transId":"2228546083",
         "submitTimeUTC":"2015-02-16T03:31:42Z",
         "submitTimeLocal":"2015-02-15T20:31:42",
         "transactionStatus":"authorizedPendingCapture",
         "invoiceNumber":"1324567890",
         "firstName":"Ellen",
         "lastName":"Johnson",
         "accountType":"MasterCard",
         "accountNumber":"XXXX0015",
         "settleAmount":5.00,
         "marketType":"eCommerce",
         "product":"Card Not Present",
         "hasReturnedItemsSpecified":false
      },
      {
         "transId":"2228545865",
         "submitTimeUTC":"2015-02-16T03:25:00Z",
         "submitTimeLocal":"2015-02-15T20:25:00",
         "transactionStatus":"authorizedPendingCapture",
         "invoiceNumber":"1324567890",
         "firstName":"Ellen",
         "lastName":"Johnson",
         "accountType":"MasterCard",
         "accountNumber":"XXXX0015",
         "settleAmount":5.00,
         "marketType":"eCommerce",
         "product":"Card Not Present",
         "hasReturnedItemsSpecified":false
      }
   ],
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
    $response = $request->getUnsettledTransactionListRequest();
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Transaction Detail :: Get Unsettled Transactions List</title>
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
        Transaction Detail :: Get Unsettled Transactions List
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
        <tr>
            <th>Code</th>
            <td><?= $response->messages->message[0]->code ?></td>
        </tr>
        <tr>
            <th>Message</th>
            <td><?= $response->messages->message[0]->text ?></td>
        </tr>
        <?php foreach ($response->transactions as $transaction) : ?>
        <tr>
            <th>Transaction</th>
            <td>
                transId: <?php echo $transaction->transId; ?><br>
                submitTimeUTC: <?php echo $transaction->submitTimeUTC; ?><br>
                submitTimeLocal: <?php echo $transaction->submitTimeLocal; ?><br>
                transactionStatus: <?php echo $transaction->transactionStatus; ?><br>
                invoiceNumber: <?php echo $transaction->invoiceNumber; ?><br>
                firstName: <?php echo $transaction->firstName; ?><br>
                accountType: <?php echo $transaction->accountType; ?><br>
                accountNumber: <?php echo $transaction->accountNumber; ?><br>
                settleAmount: <?php echo $transaction->settleAmount; ?><br>
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
