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

    require '../../config.inc.php';

    $request  = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
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
            Transaction Detail :: Get Held Transaction List
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
                <td><?php echo $response->isSuccessful() ? 'yes' : 'no'; ?></td>
            </tr>
            <tr>
                <th>Error?</th>
                <td><?php echo $response->isError() ? 'yes' : 'no'; ?></td>
            </tr>
            <tr>
                <th>Number of Results</th>
                <td><?php echo $response->totalNumInResultSet; ?></td>
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