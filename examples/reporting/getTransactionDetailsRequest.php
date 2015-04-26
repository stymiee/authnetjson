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

Use the Transaction Details API to get the details of a transaction

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{  
   "getTransactionDetailsRequest":{  
      "merchantAuthentication":{  
         "name":"",
         "transactionKey":""
      },
      "transId":"2162566217"
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{  
   "transaction":{  
      "transId":"2162566217",
      "submitTimeUTC":"2011-09-01T16:30:49.39Z",
      "submitTimeLocal":"2011-09-01T10:30:49.39",
      "transactionType":"authCaptureTransaction",
      "transactionStatus":"settledSuccessfully",
      "responseCode":1,
      "responseReasonCode":1,
      "responseReasonDescription":"Approval",
      "authCode":"JPG9DJ",
      "AVSResponse":"Y",
      "batch":{  
         "batchId":"1221577",
         "settlementTimeUTC":"2011-09-01T16:38:54.52Z",
         "settlementTimeUTCSpecified":true,
         "settlementTimeLocal":"2011-09-01T10:38:54.52",
         "settlementTimeLocalSpecified":true,
         "settlementState":"settledSuccessfully"
      },
      "order":{  
         "invoiceNumber":"60",
         "description":"Auto-charge for Invoice #60"
      },
      "requestedAmountSpecified":false,
      "authAmount":1018.88,
      "settleAmount":1018.88,
      "prepaidBalanceRemainingSpecified":false,
      "taxExempt":false,
      "taxExemptSpecified":true,
      "payment":{  
         "creditCard":{  
            "cardNumber":"XXXX4444",
            "expirationDate":"XXXX",
            "cardType":"MasterCard"
         }
      },
      "customer":{  
         "typeSpecified":false,
         "id":"4"
      },
      "billTo":{  
         "phoneNumber":"(619) 274-0494",
         "firstName":"Matteo",
         "lastName":"Bignotti",
         "address":"625 Broadway\nSuite 1025",
         "city":"San Diego",
         "state":"CA",
         "zip":"92101",
         "country":"United States"
      },
      "recurringBilling":false,
      "recurringBillingSpecified":true,
      "product":"Card Not Present",
      "marketType":"eCommerce"
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
    $response = $request->getTransactionDetailsRequest(array(
        'transId' => '2162566217'
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
            Transaction Detail :: Transaction Details
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
                <th>Transaction</th>
                <td>
                    <b>ID</b>: <?php echo $response->transaction->transId; ?><br>
                    <b>Type</b>: <?php echo $response->transaction->transactionType; ?><br>
                    <b>Status</b>: <?php echo $response->transaction->transactionStatus; ?><br>
                    <b>Authorization Code</b>: <?php echo $response->transaction->authCode; ?><br>
                    <b>AVS Response</b>: <?php echo $response->transaction->AVSResponse; ?><br>
                    <b>Batch Id</b>: <?php echo $response->transaction->batch->batchId; ?>
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
