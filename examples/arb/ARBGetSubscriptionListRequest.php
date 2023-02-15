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

You can use the following method to request a list of subscriptions. The function will return up to 1000 results in a
single request. Paging options can be sent to limit the result set or to retrieve additional results beyond the
1000 item limit. You can add the sorting and paging options shown below to customize the result set.

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
    "ARBGetSubscriptionListRequest": {
        "merchantAuthentication": {
            "name": "",
            "transactionKey": ""
        },
        "refId": "123456",
        "searchType": "subscriptionActive",
        "sorting": {
            "orderBy": "id",
            "orderDescending": "false"
        },
        "paging": {
            "limit": "1000",
            "offset": "1"
        }
    }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
    "totalNumInResultSet": 1273,
    "totalNumInResultSetSpecified": true,
    "subscriptionDetails": [
        {
            "id": 100188,
            "name": "subscription",
            "status": "canceled",
            "createTimeStampUTC": "2004-04-28T23:59:47.33",
            "firstName": "Joe",
            "lastName": "Tester",
            "totalOccurrences": 12,
            "pastOccurrences": 6,
            "paymentMethod": "creditCard",
            "accountNumber": "XXXX5454",
            "invoice": "42820041325496571",
            "amount": 10,
            "currencyCode": "USD"
        },
        {
            "id": 100222,
            "name": "",
            "status": "canceled",
            "createTimeStampUTC": "2004-10-22T21:00:15.503",
            "firstName": "asdf",
            "lastName": "asdf",
            "totalOccurrences": 12,
            "pastOccurrences": 0,
            "paymentMethod": "creditCard",
            "accountNumber": "XXXX1111",
            "invoice": "",
            "amount": 1,
            "currencyCode": "USD"
        },
        {
            "id": 100223,
            "name": "",
            "status": "canceled",
            "createTimeStampUTC": "2004-10-22T21:01:27.69",
            "firstName": "asdf",
            "lastName": "asdf",
            "totalOccurrences": 12,
            "pastOccurrences": 1,
            "paymentMethod": "eCheck",
            "accountNumber": "XXXX3888",
            "invoice": "",
            "amount": 10,
            "currencyCode": "USD"
        }
    ],
    "refId": "123456",
    "messages": {
        "resultCode": "Ok",
        "message": [
            {
                "code": "I00001",
                "text": "Successful."
            }
        ]
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
    $response = $request->ARBGetSubscriptionListRequest([
        'refId' => '123456',
        'searchType' => 'subscriptionActive',
        'sorting' => [
        'orderBy' => 'id',
            "orderDescending" => 'false'
        ],
        'paging' => [
            'limit' => '1000',
            'offset' => '1'
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
    <title>ARB :: Get Subscription List</title>
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
        ARB :: Get Subscription List
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
        <tr>
            <th>Number of subscriptions</th>
            <td><?= $response->totalNumInResultSet ?></td>
        </tr>
        <tr>
            <th>Subscription Details</th>
            <td>
                <table>
                    <?php foreach ($response->subscriptionDetails as $subscription) : ?>
                    <tr>
                        <th>ID:</th><td><?= $subscription->id ?></td>
                        <th>Name:</th><td><?= $subscription->name ?></td>
                        <th>Status:</th><td><?= $subscription->status ?></td>
                        <th>Created Timestamp:</th><td><?= $subscription->createTimeStampUTC ?></td>
                        <th>First Name:</th><td><?= $subscription->firstName ?></td>
                        <th>Last Name:</th><td><?= $subscription->lastName ?></td>
                        <th>Total Occurrences:</th><td><?= $subscription->totalOccurrences ?></td>
                        <th>Past Occurrences:</th><td><?= $subscription->pastOccurrences ?></td>
                        <th>Payment Method:</th><td><?= $subscription->paymentMethod ?></td>
                        <th>Account Number:</th><td><?= $subscription->accountNumber ?></td>
                        <th>Invoice:</th><td><?= $subscription->invoice ?></td>
                        <th>Amount:</th><td><?= $subscription->amount ?></td>
                        <th>Currency Code:</th><td><?= $subscription->currencyCode ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </td>
        </tr>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
