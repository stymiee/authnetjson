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

Use this function to get details of each card updated or deleted by the Account Updater process
for a particular month. The function will return data for up to 1000 of the most recent transactions
in a single request. Paging options can be sent to limit the result set or to retrieve additional
transactions beyond the 1000 transaction limit. No input parameters are required other than the
authentication information and a batch ID. However, you can add the sorting and paging options shown
below to customize the result set.

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
  "getAUJobDetailsRequest": {
    "merchantAuthentication": {
      "name": "",
      "transactionKey": ""
    },
    "refId": "123456",
    "month": "2017-06",
    "modifiedTypeFilter": "all",
    "paging": {
      "limit": "100",
      "offset": "1"
    }
  }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
  "totalNumInResultSet": 4,
  "auDetails":
  {
    "auDelete":
    {
      "customerProfileID": 2,
      "customerPaymentProfileID": 2,
      "firstName": "",
      "lastName": "",
      "updateTimeUTC": "6/28/2017 1:31:01 PM",
      "auReasonCode": "ACL",
      "reasonDescription": "AccountClosed",
      "creditCard":
      {
        "cardNumber": "XXXX1111",
        "expirationDate": "XXXX"
      }
    },
    "auUpdate":
    [
      {
        "customerProfileID": 88,
        "customerPaymentProfileID": 117,
        "firstName": "",
        "lastName": "Last name to bill_123",
        "updateTimeUTC": "6/27/2017 9:24:47 AM",
        "auReasonCode": "NED",
        "reasonDescription": "NewExpirationDate",
        "newCreditCard":
        {
          "cardNumber": "XXXX2222",
          "expirationDate": "XXXX"
        },
        "oldCreditCard":
        {
          "cardNumber": "XXXX1111",
          "expirationDate": "XXXX"
        }
      },
      {
        "customerProfileID": 89,
        "customerPaymentProfileID": 118,
        "firstName": "First name to bill_123",
        "lastName": "Last name to bill_123",
        "updateTimeUTC": "6/27/2017 9:25:09 AM",
        "auReasonCode": "NED",
        "reasonDescription": "NewExpirationDate",
        "newCreditCard":
        {
          "cardNumber": "XXXX1212",
          "expirationDate": "XXXX"
        },
        "oldCreditCard":
        {
          "cardNumber": "XXXX1111",
          "expirationDate": "XXXX"
        }
      },
      {
        "customerProfileID": 90,
        "customerPaymentProfileID": 119,
        "firstName": "First name to bill_123",
        "lastName": "Last name to bill_123",
        "updateTimeUTC": "6/27/2017 9:40:35 AM",
        "auReasonCode": "NAN",
        "reasonDescription": "NewAccountNumber",
        "newCreditCard":
        {
          "cardNumber": "XXXX3333",
          "expirationDate": "XXXX"
        },
        "oldCreditCard":
        {
          "cardNumber": "XXXX1111",
          "expirationDate": "XXXX"
        }
      }
    ]
  },
  "refId": 123456,
  "messages":
  {
    "resultCode": "Ok",
    "message":
    {
      "code": "I00001",
      "text": "Successful."
    }
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
    $response = $request->getAUJobDetailsRequest([
        'refId' => "123456",
        'month' => "2020-05",
        'modifiedTypeFilter' => "all",
        'paging' => [
          'limit' => 100,
          'offset' => 1
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
    <title>Transaction Detail :: Get Account Updater Job Details</title>
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
        Transaction Detail :: Get Account Updater Job Details
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
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
