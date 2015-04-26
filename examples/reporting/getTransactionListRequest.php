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

Use the Transaction Details API to get a list of transaction in a batch

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{  
   "getTransactionListRequest":{  
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
   "transactions":[  
      {  
         "transId":"2162566217",
         "submitTimeUTC":"2011-09-01T16:30:49Z",
         "submitTimeLocal":"2011-09-01T10:30:49",
         "transactionStatus":"settledSuccessfully",
         "invoiceNumber":"60",
         "firstName":"Matteo",
         "lastName":"Bignotti",
         "accountType":"MasterCard",
         "accountNumber":"XXXX4444",
         "settleAmount":1018.88,
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

    namespace JohnConde\Authnet;

    require('../../config.inc.php');
    require('../../src/autoload.php');

    $request  = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $response = $request->getTransactionListRequest(array(
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
            Transaction Detail :: Get Transactions List
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
            <?php foreach ($json->transactions as $transaction) : ?>
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
<?php
    echo $request, $response;
?>
    </body>
</html>
