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

Use the AIM JSON API to process an Auth Only transaction through Paypal

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
    "createTransactionRequest": {
        "merchantAuthentication": {
            "name": "cnpdev4289",
            "transactionKey": "SR2P8g4jdEn7vFLQ"
        },
        "transactionRequest": {
            "transactionType": "authOnlyTransaction",
            "amount": "5",
            "payment": {
                "payPal": {
                    "successUrl": "https://my.server.com/success.html",
                    "cancelUrl": "https://my.server.com/cancel.html"
                }
            }
        }
    }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
    "transactionResponse": {
        "responseCode": "5",
        "rawResponseCode": "0",
        "transId": "2149186954",
        "refTransID": "",
        "transHash": "A719785EE9752530FDCE67695E9A56EE",
        "testRequest": "0",
        "accountType": "PayPal",
        "messages": [
            {
                "code": "2000",
                "description": "Need payer consent."
            }
        ],
        "secureAcceptance": {
            "SecureAcceptanceUrl": "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-C506B0LGTG2J800OK"
        }
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
            'transactionType' => 'authOnlyTransaction',
            'amount' => 5,
            'payment' => [
                'payPal' => [
                    'successUrl' => 'https://my.server.com/success.html',
                    'cancelUrl' => 'https://my.server.com/cancel.html'
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
    <title>Payment :: Paypal :: Authorize Only</title>
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
        Payment :: Paypal :: Authorize Only
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
            <th>authCode</th>
            <td><?= $response->transactionResponse->authCode ?></td>
        </tr>
        <tr>
            <th>transId</th>
            <td><?= $response->transactionResponse->transId ?></td>
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
