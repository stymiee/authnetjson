<?php
/*************************************************************************************************

Use the AIM JSON API to decrypt Visa Checkout data

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
    "decryptPaymentDataRequest": {
        "merchantAuthentication": {
            "name": "",
            "transactionKey": ""
        },
        "opaqueData": {
            "dataDescriptor": "COMMON.VCO.ONLINE.PAYMENT",
            "dataValue": "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",
            "dataKey": "k5qdgh6t5BnvuBz3Xs0f+iraXB0tXO+6WewMUVTBsqYN3G16HHYwlz8To602G/pZ+Sng0mjTAy3mwhpOb/5nJktiCTpKL4wvn0exJxGt9GIAODBYz4PFbxuenmuQE9O5"
        },
        "callId": "4859677641513545101"
    }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
    "shippingInfo": {
        "firstName": "John",
        "lastName": "Doe",
        "address": "5100 Main St",
        "city": "Bellevue",
        "state": "CA",
        "zip": "98104",
        "country": "US"
    },
    "billingInfo": {
        "email": "bmcmanus@visa.com",
        "firstName": "John",
        "lastName": "Doe",
        "address": "5100 Main St",
        "city": "Bellevue",
        "state": "CA",
        "zip": "98104",
        "country": "US"
    },
    "cardInfo": {
        "cardNumber": "XXXX4242",
        "expirationDate": "12/2018",
        "cardArt": {
            "cardBrand": "VISA",
            "cardImageHeight": "50",
            "cardImageUrl": "https://sandbox.secure.checkout.visa.com/VmeCardArts/wv87HR3X3jqlNXNJu_6YtLQyyO7mpu2aU6Yo3VWGKKM.png",
            "cardImageWidth": "77",
            "cardType": "DEBIT"
        }
    },
    "paymentDetails": {
        "currency": "USD",
        "amount": "16.00"
    },
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

 **************************************************************************************************/

namespace Authnetjson;

use Exception;

require '../../config.inc.php';

try {
    $request = AuthnetApiFactory::getJsonApiHandler(
        AUTHNET_LOGIN,
        AUTHNET_TRANSKEY,
        AuthnetApiFactory::USE_DEVELOPMENT_SERVER
    );
    $response = $request->decryptPaymentDataRequest([
        'opaqueData' => [
            'dataDescriptor' => 'COMMON.VCO.ONLINE.PAYMENT',
            'dataValue' => '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',
            'dataKey' => 'qGl+69iAWXJ13+18jHgBO2zHCuekawWcApfLhGnbYXD4GsI9EOM2Y5V8zGXvr1lF3hjGMT0NoD2thxzR7CrAvgfgw7lAJzlGIACOZnEkx70ywnJHAR3sGO8hyq9f1Fk'
        ],
        'callId' => '2186595692635007317'
    ]);
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment :: Decrypt Visa Checkout Data</title>
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
        Payment :: Decrypt Visa Checkout Data
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
        <?php if ($response->isSuccessful()) : ?>
        <tr>
            <th>Shipping Info</th>
            <td>
                firstName: <?= $response->shippingInfo->firstName ?><br>
                lastName: <?= $response->shippingInfo->lastName ?><br>
                address: <?= $response->shippingInfo->address ?><br>
                city: <?= $response->shippingInfo->city ?><br>
                state: <?= $response->shippingInfo->state ?><br>
                zip: <?= $response->shippingInfo->zip ?><br>
                country: <?= $response->shippingInfo->country ?>
            </td>
        </tr>
        <tr>
            <th>Billing Info</th>
            <td>
                email: <?= $response->shippingInfo->email ?><br>
                firstName: <?= $response->shippingInfo->firstName ?><br>
                lastName: <?= $response->shippingInfo->lastName ?><br>
                address: <?= $response->shippingInfo->address ?><br>
                city: <?= $response->shippingInfo->city ?><br>
                state: <?= $response->shippingInfo->state ?><br>
                zip: <?= $response->shippingInfo->zip ?><br>
                country: <?= $response->shippingInfo->country ?>
            </td>
        </tr>
        <tr>
            <th>Card Information</th>
            <td>
                cardNumber: <?= $response->shippingInfo->cardNumber ?><br>
                expirationDate: <?= $response->shippingInfo->expirationDate ?><br>
                cardBrand: <?= $response->shippingInfo->cardArt->cardBrand ?><br>
                cardType: <?= $response->shippingInfo->cardArt->cardType ?>
            </td>
        </tr>
        <tr>
            <th>Payment Details</th>
            <td>
                currency: <?= $response->shippingInfo->currency ?><br>
                amount: <?= $response->shippingInfo->amount ?>
            </td>
        </tr>
        <?php elseif ($response->isError()) : ?>
        <tr>
            <th>Error Code</th>
            <td><?= $response->getErrorCode() ?></td>
        </tr>
        <tr>
            <th>Error Message</th>
            <td><?= $response->getErrorText() ?></td>
        </tr>
        <?php endif; ?>
    </table>
    <h2>
        Raw Input/Output
    </h2>
    <?= $request, $response ?>
</body>
</html>
