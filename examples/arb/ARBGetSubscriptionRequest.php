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

Retrieves an existing ARB subscription.

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
  "ARBGetSubscriptionRequest": {
    "merchantAuthentication": {
      "name": "",
      "transactionKey": ""
    },
    "refId": "123456",
    "subscriptionId": "4818507",
    "includeTransactions": true
  }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
  "ARBGetSubscriptionResponse": {
    "refId": "123456",
    "messages": {
      "resultCode": "Ok",
      "message": {
        "code": "I00001",
        "text": "Successful."
      }
    },
    "subscription": {
      "name": "Sample subscription",
      "paymentSchedule": {
        "interval": {
          "length": "7",
          "unit": "days"
        },
        "startDate": "2017-09-09",
        "totalOccurrences": "9999",
        "trialOccurrences": "1"
      },
      "amount": "10.29",
      "trialAmount": "1.00",
      "status": "active",
      "profile": {
        "merchantCustomerId": "973",
        "description": "Profile description here",
        "email": "TestEmail5555@domain.com",
        "customerProfileId": "1812912918",
        "paymentProfile": {
          "customerType": "individual",
          "billTo": {
            "firstName": "Arte",
            "lastName": "Johnson",
            "company": "test Co.",
            "address": "123 Test St.",
            "city": "Testville",
            "state": "AZ",
            "zip": "85282",
            "country": "US"
          },
          "customerPaymentProfileId": "1807515631",
          "payment": {
            "creditCard": {
              "cardNumber": "XXXX1111",
              "expirationDate": "XXXX"
            }
          }
        },
        "shippingProfile": {
          "firstName": "Aaron",
          "lastName": "Wright",
          "company": "Testing, Inc.",
          "address": "123 Testing St.",
          "city": "Lehi",
          "state": "UT",
          "zip": "84043",
          "country": "US",
          "phoneNumber": "520-254-5038",
          "customerAddressId": "1811684122"
        }
      },
      "arbTransactions": {
        "arbTransaction": {
          "response": "The credit card has expired.",
          "submitTimeUTC": "2017-09-14T18:40:31.247",
          "payNum": "2",
          "attemptNum": "1"
        }
      }
    }
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
    $response = $request->ARBGetSubscriptionRequest([
        'refId' => 'Sample',
        'subscriptionId' => '6662897',
        'includeTransactions' => true
    ]);
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>ARB :: Get Subscription</title>
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
        ARB :: Get Subscription
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
            <th>code</th>
            <td><?= $response->messages->message[0]->code ?></td>
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
            <th>Subscription Status</th>
            <td><?= $response->subscription->status ?></td>
        </tr>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
