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

Use the Transaction Details API to get a summary of a settled batch

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{  
   "getBatchStatisticsRequest":{  
      "merchantAuthentication":{  
         "name":"",
         "transactionKey":""
      },
      "batchId":"1221577"
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{  
   "batch":{  
      "batchId":"1221577",
      "settlementTimeUTC":"2011-09-01T16:38:54Z",
      "settlementTimeUTCSpecified":true,
      "settlementTimeLocal":"2011-09-01T10:38:54",
      "settlementTimeLocalSpecified":true,
      "settlementState":"settledSuccessfully",
      "paymentMethod":"creditCard",
      "statistics":[  
         {  
            "accountType":"MasterCard",
            "chargeAmount":1018.88,
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
   },
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

    require('../../config.inc.php');
    require('../../src/autoload.php');

    $request  = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $response = $request->getBatchStatisticsRequest(array(
        'batchId' => '1221577'
    ));
?>

<!DOCTYPE html>
<html>
<html lang="en">
    <head>
        <title></title>
        <style type="text/css">
            table
            {
                border: 1px solid #cccccc;
                margin: auto;
                border-collapse: collapse;
                max-width: 90%;
            }

            table td
            {
                padding: 3px 5px;
                vertical-align: top;
                border-top: 1px solid #cccccc;
            }

            pre
            {
            	overflow-x: auto; /* Use horizontal scroller if needed; for Firefox 2, not needed in Firefox 3 */
            	white-space: pre-wrap; /* css-3 */
            	white-space: -moz-pre-wrap !important; /* Mozilla, since 1999 */
            	white-space: -pre-wrap; /* Opera 4-6 */
            	white-space: -o-pre-wrap; /* Opera 7 */ /*
            	width: 99%; */
            	word-wrap: break-word; /* Internet Explorer 5.5+ */
            }

            table th
            {
                background: #e5e5e5;
                color: #666666;
            }

            h1, h2
            {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <h1>
            Transaction Detail :: Get Batch Statistics
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
                <th>Successful?</th>
                <td><?php echo ($response->isSuccessful()) ? 'yes' : 'no'; ?></td>
            </tr>
            <tr>
                <th>Error?</th>
                <td><?php echo ($response->isError()) ? 'yes' : 'no'; ?></td>
            </tr>
            <tr>
                <th>Batch</th>
                <td>
                    batchId: <?php echo $response->batch->batchId; ?><br>
                    settlementTimeUTC: <?php echo $response->batch->settlementTimeUTC; ?><br>
                    settlementTimeLocal: <?php echo $response->batch->settlementTimeLocal; ?><br>
                    settlementState: <?php echo $response->batch->settlementState; ?><br>
                    paymentMethod: <?php echo $response->batch->paymentMethod; ?><br>
                    accountType: <?php echo $response->batch->statistics[0]->accountType; ?><br>
                    chargeAmount: <?php echo $response->batch->statistics[0]->chargeAmount; ?><br>
                    chargeCount: <?php echo $response->batch->statistics[0]->chargeCount; ?><br>
                    refundAmount: <?php echo $response->batch->statistics[0]->refundAmount; ?><br>
                    refundCount: <?php echo $response->batch->statistics[0]->refundCount; ?><br>
                    voidCount: <?php echo $response->batch->statistics[0]->voidCount; ?><br>
                    declineCount: <?php echo $response->batch->statistics[0]->declineCount; ?><br>
                    errorCount: <?php echo $response->batch->statistics[0]->errorCount; ?>
                </td>
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
