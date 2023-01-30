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

This request enables you to create a recurring billing subscription from an existing customer profile. NOTE: The
customer payment profile first and last name fields must be populated, these are required for a subscription.
For subscriptions with a monthly interval, whose payments begin on the 31st of a month, payments for months with
fewer than 31 days occur on the last day of the month.

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
    "ARBCreateSubscriptionRequest": {
        "merchantAuthentication": {
            "name": "",
            "transactionKey": ""
        },
        "refId": "123456",
        "subscription": {
            "name": "Sample subscription",
            "paymentSchedule": {
                "interval": {
                    "length": "1",
                    "unit": "months"
                },
                "startDate": "2020-08-30",
                "totalOccurrences": "12",
                "trialOccurrences": "1"
            },
            "amount": "10.29",
            "trialAmount": "0.00",
            "profile": {
                "customerProfileId": "39931060",
                "customerPaymentProfileId": "36223863",
                "customerAddressId": "37726371"
            }
        }
    }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
    "subscriptionId": "158383",
    profile": {
        "customerProfileId": "39931060",
        "customerPaymentProfileId": "36223863",
        "customerAddressId": "37726371"
    },
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

namespace Authnetjson;

use Exception;

require '../../config.inc.php';

try {
    $request = AuthnetApiFactory::getJsonApiHandler(
        AUTHNET_LOGIN,
        AUTHNET_TRANSKEY,
        AuthnetApiFactory::USE_DEVELOPMENT_SERVER
    );
    $response = $request->ARBCreateSubscriptionRequest([
        'refId' => 'Sample',
        'subscription' => [
            'name' => 'Sample subscription',
            'paymentSchedule' => [
                'interval' => [
                    'length' => '1',
                    'unit' => 'months'
                ],
                'startDate' => '2020-07-15',
                'totalOccurrences' => AuthnetJson::BOUNDLESS_OCCURRENCES,
                'trialOccurrences' => '1'
            ],
            'amount' => '10.29',
            'trialAmount' => '0.00',
            'profile' => [
                'customerProfileId' => '1512256927',
                'customerPaymentProfileId' => '1512285006',
            ]
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
    <title>ARB :: Create Subscription from Customer Profile</title>
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
        ARB :: Create Subscription from Customer Profile
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
            <th>Subscription ID</th>
            <td><?= $response->subscriptionId ?></td>
        </tr>
        <tr>
            <th>Profile</th>
            <td>
                Customer Profile Id: <?= $response->profile->customerProfileId ?><br>
                Customer PaymentProfile Id: <?= $response->profile->customerPaymentProfileId ?><br>
                Customer Address Id: <?= $response->profile->customerAddressId ?><br>
            </td>
        </tr>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
