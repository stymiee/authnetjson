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

Use the Transaction Details API to get a list of settled batches

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "getSettledBatchListRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "includeStatistics":"true",
      "firstSettlementDate":"2015-01-01T08:15:30",
      "lastSettlementDate":"2015-01-30T08:15:30"
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "batchList":[
      {
         "batchId":"3990061",
         "settlementTimeUTC":"2015-01-02T08:40:29Z",
         "settlementTimeUTCSpecified":true,
         "settlementTimeLocal":"2015-01-02T01:40:29",
         "settlementTimeLocalSpecified":true,
         "settlementState":"settledSuccessfully",
         "paymentMethod":"creditCard",
         "marketType":"eCommerce",
         "product":"Card Not Present",
         "statistics":[
            {
               "accountType":"MasterCard",
               "chargeAmount":98.00,
               "chargeCount":2,
               "refundAmount":0.00,
               "refundCount":0,
               "voidCount":0,
               "declineCount":0,
               "errorCount":0,
               "returnedItemAmountSpecified":false,
               "returnedItemCountSpecified":false,
               "chargebackAmountSpecified":false,
               "chargebackCountSpecified":false,
               "correctionNoticeCountSpecified":false,
               "chargeChargeBackAmountSpecified":false,
               "chargeChargeBackCountSpecified":false,
               "refundChargeBackAmountSpecified":false,
               "refundChargeBackCountSpecified":false,
               "chargeReturnedItemsAmountSpecified":false,
               "chargeReturnedItemsCountSpecified":false,
               "refundReturnedItemsAmountSpecified":false,
               "refundReturnedItemsCountSpecified":false
            },
            {
               "accountType":"Visa",
               "chargeAmount":2255.50,
               "chargeCount":4,
               "refundAmount":0.00,
               "refundCount":0,
               "voidCount":0,
               "declineCount":0,
               "errorCount":0,
               "returnedItemAmountSpecified":false,
               "returnedItemCountSpecified":false,
               "chargebackAmountSpecified":false,
               "chargebackCountSpecified":false,
               "correctionNoticeCountSpecified":false,
               "chargeChargeBackAmountSpecified":false,
               "chargeChargeBackCountSpecified":false,
               "refundChargeBackAmountSpecified":false,
               "refundChargeBackCountSpecified":false,
               "chargeReturnedItemsAmountSpecified":false,
               "chargeReturnedItemsCountSpecified":false,
               "refundReturnedItemsAmountSpecified":false,
               "refundReturnedItemsCountSpecified":false
            }
         ]
      },
      {
         "batchId":"3990090",
         "settlementTimeUTC":"2015-01-02T08:55:35Z",
         "settlementTimeUTCSpecified":true,
         "settlementTimeLocal":"2015-01-02T01:55:35",
         "settlementTimeLocalSpecified":true,
         "settlementState":"settledSuccessfully",
         "paymentMethod":"creditCard",
         "marketType":"eCommerce",
         "product":"Card Not Present",
         "statistics":[
            {
               "accountType":"MasterCard",
               "chargeAmount":19.95,
               "chargeCount":1,
               "refundAmount":0.00,
               "refundCount":0,
               "voidCount":0,
               "declineCount":0,
               "errorCount":0,
               "returnedItemAmountSpecified":false,
               "returnedItemCountSpecified":false,
               "chargebackAmountSpecified":false,
               "chargebackCountSpecified":false,
               "correctionNoticeCountSpecified":false,
               "chargeChargeBackAmountSpecified":false,
               "chargeChargeBackCountSpecified":false,
               "refundChargeBackAmountSpecified":false,
               "refundChargeBackCountSpecified":false,
               "chargeReturnedItemsAmountSpecified":false,
               "chargeReturnedItemsCountSpecified":false,
               "refundReturnedItemsAmountSpecified":false,
               "refundReturnedItemsCountSpecified":false
            },
            {
               "accountType":"Visa",
               "chargeAmount":5.00,
               "chargeCount":1,
               "refundAmount":0.00,
               "refundCount":0,
               "voidCount":0,
               "declineCount":0,
               "errorCount":0,
               "returnedItemAmountSpecified":false,
               "returnedItemCountSpecified":false,
               "chargebackAmountSpecified":false,
               "chargebackCountSpecified":false,
               "correctionNoticeCountSpecified":false,
               "chargeChargeBackAmountSpecified":false,
               "chargeChargeBackCountSpecified":false,
               "refundChargeBackAmountSpecified":false,
               "refundChargeBackCountSpecified":false,
               "chargeReturnedItemsAmountSpecified":false,
               "chargeReturnedItemsCountSpecified":false,
               "refundReturnedItemsAmountSpecified":false,
               "refundReturnedItemsCountSpecified":false
            }
         ]
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
    $response = $request->getSettledBatchListRequest([
        'includeStatistics'   => 'true',
        'firstSettlementDate' => '2018-01-01T08:15:30',
        'lastSettlementDate'  => '2018-01-30T08:15:30',
    ]);
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Transaction Detail :: Get Settled Batch List</title>
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
        Transaction Detail :: Get Settled Batch List
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
        <?php foreach ($response->batchList as $batch) : ?>
        <tr>
            <th>Batch</th>
            <td>
                batchId: <?php echo $batch->batchId; ?><br>
                settlementTimeUTC: <?php echo $batch->settlementTimeUTC; ?><br>
                settlementTimeLocal: <?php echo $batch->settlementTimeLocal; ?><br>
                settlementState: <?php echo $batch->settlementState; ?><br>
                paymentMethod: <?php echo $batch->paymentMethod; ?><br>
                <?php foreach ($batch->statistics as $statistic) : ?>
                accountType: <?php echo $statistic->accountType; ?><br>
                chargeAmount: <?php echo $statistic->chargeAmount; ?><br>
                chargeCount: <?php echo $statistic->chargeCount; ?><br>
                refundAmount: <?php echo $statistic->refundAmount; ?><br>
                refundCount: <?php echo $statistic->refundCount; ?><br>
                voidCount: <?php echo $statistic->voidCount; ?><br>
                declineCount: <?php echo $statistic->declineCount; ?><br>
                errorCount: <?php echo $statistic->errorCount; ?>
                <?php endforeach; ?>
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
