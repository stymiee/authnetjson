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

Call this function and supply your authentication information to receive merchant details in the response.
The information that is returned is helpful for OAuth and Accept integrations. Generate a PublicClientKey only
if one is not generated or is not active. Only the most recently generated active key is returned.

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
    "getMerchantDetailsRequest": {
        "merchantAuthentication": {
            "name": "",
            "transactionKey": ""
        }
    }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
    "isTestMode": false,
    "processors": [
        {
            "name": "First Data Nashville"
        }
    ],
    "merchantName": "fwHGwSdCaR",
    "gatewayId": "565697",
    "marketTypes": [
        "eCommerce"
    ],
    "productCodes": [
        "CNP"
    ],
    "paymentMethods": [
        "AmericanExpress",
        "DinersClub",
        "Discover",
        "EnRoute",
        "JCB",
        "Mastercard",
        "Visa"
    ],
    "currencies": [
        "USD"
    ],
    "publicClientKey": "9aptdYwtHt2F22XLRgr4B9AM4Pkt5eb6b6MC9d2Nn3m3YEptx3RFFuXmpYWDLHev",
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
    $response = $request->getMerchantDetailsRequest();
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Transaction Detail :: Get Merchant Details</title>
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
        Transaction Detail :: Get Merchant Details
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
            <th>Public Client Key</th>
            <td><?= $response->publicClientKey ?></td>
        </tr>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
