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

Use the AIM JSON API to process an Authorize and Capture transaction through Paypal

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
    "createTransactionRequest": {
        "merchantAuthentication": {
            "name": "cnpdev4289",
            "transactionKey": "SR2P8g4jdEn7vFLQ"
        },
        "transactionRequest": {
            "transactionType": "authCaptureTransaction",
            "amount": "80.93",
            "payment": {
                "payPal": {
                    "successUrl": "https://my.server.com/success.html",
                    "cancelUrl": "https://my.server.com/cancel.html",
                    "paypalLc": "",
                    "paypalHdrImg": "",
                    "paypalPayflowcolor": "FFFF00"
                }
            },
            "lineItems": {
                "lineItem": {
                    "itemId": "item1",
                    "name": "golf balls",
                    "quantity": "1",
                    "unitPrice": "18.95"
                }
            }
        }
    }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
    "transactionResponse": {
        "responseCode": "1",
        "transId": "2149186848",
        "refTransID": "2149186775",
        "transHash": "D6C9036F443BADE785D57DA2B44CD190",
        "testRequest": "0",
        "accountType": "PayPal",
        "messages": [
            {
                "code": "1",
                "description": "This transaction has been approved."
            }
        ]
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

namespace JohnConde\Authnet;

use Exception;

require '../../config.inc.php';

try {
    $request = AuthnetApiFactory::getJsonApiHandler(
        AUTHNET_LOGIN,
        AUTHNET_TRANSKEY,
        AuthnetApiFactory::USE_DEVELOPMENT_SERVER
    );
    $response = $request->createTransactionRequest([
        'transactionRequest' => [
            'transactionType' => 'authCaptureTransaction',
            'amount' => '80.93',
            'payment' => [
                'payPal' => [
                    'successUrl' => 'https://my.server.com/success.html',
                    'cancelUrl' => 'https://my.server.com/cancel.html',
                    'paypalLc' => '',
                    'paypalHdrImg' => '',
                    'paypalPayflowcolor' => 'FFFF00'
                ]
            ],
            'lineItems' => [
                'lineItem' => [
                    'itemId' => 'item1',
                    'name' => 'golf balls',
                    'quantity' => '1',
                    'unitPrice' => '18.95'
                ]
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
    <title>Payment :: Paypal :: Authorize and Capture</title>
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
        Payment :: Authorize and Capture
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
            <th>Description</th>
            <td><?= $response->transactionResponse->messages[0]->description ?></td>
        </tr>
        <tr>
            <th>Authorization Code</th>
            <td><?= $response->transactionResponse->authCode ?></td>
        </tr>
        <tr>
            <th>Transaction ID</th>
            <td><?= $response->transactionResponse->transId ?></td>
        </tr>
        <tr>
            <th>Reference Transaction ID</th>
            <td><?= $response->transactionResponse->refTransID ?></td>
        </tr>
        <tr>
            <th>Transaction Hash</th>
            <td><?= $response->transactionResponse->transHash ?></td>
        </tr>
        <tr>
            <th>Is Test Request?</th>
            <td><?= $response->transactionResponse->testRequest ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <th>Account Type</th>
            <td><?= $response->transactionResponse->accountType ?></td>
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
