<?php

/*************************************************************************************************

Use this function to retrieve a form token which can be used to request the Authorize.Net Accept
hosted payment page.

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
    "getHostedPaymentPageRequest": {
        "merchantAuthentication": {
            "name": "",
            "transactionKey": ""
        },
        "transactionRequest": {
            "transactionType": "authCaptureTransaction",
            "amount": "20.00",
            "profile": {
                "customerProfileId": "123456789"
            },
            "customer": {
                "email": "ellen@mail.com"
            },
            "billTo": {
                "firstName": "Ellen",
                "lastName": "Johnson",
                "company": "Souveniropolis",
                "address": "14 Main Street",
                "city": "Pecan Springs",
                "state": "TX",
                "zip": "44628",
                "country": "USA"
            }
        },
        "hostedPaymentSettings": {
            "setting": [{
                "settingName": "hostedPaymentReturnOptions",
                "settingValue": "{\"showReceipt\": true, \"url\": \"https://mysite.com/receipt\", \"urlText\": \"Continue\", \"cancelUrl\": \"https://mysite.com/cancel\", \"cancelUrlText\": \"Cancel\"}"
            }, {
                "settingName": "hostedPaymentButtonOptions",
                "settingValue": "{\"text\": \"Pay\"}"
            }, {
                "settingName": "hostedPaymentStyleOptions",
                "settingValue": "{\"bgColor\": \"blue\"}"
            }, {
                "settingName": "hostedPaymentPaymentOptions",
                "settingValue": "{\"cardCodeRequired\": false, \"showCreditCard\": true, \"showBankAccount\": true}"
            }, {
                "settingName": "hostedPaymentSecurityOptions",
                "settingValue": "{\"captcha\": false}"
            }, {
                "settingName": "hostedPaymentShippingAddressOptions",
                "settingValue": "{\"show\": false, \"required\": false}"
            }, {
                "settingName": "hostedPaymentBillingAddressOptions",
                "settingValue": "{\"show\": true, \"required\": false}"
            }, {
                "settingName": "hostedPaymentCustomerOptions",
                "settingValue": "{\"showEmail\": false, \"requiredEmail\": false, \"addPaymentProfile\": true}"
            }, {
                "settingName": "hostedPaymentOrderOptions",
                "settingValue": "{\"show\": true, \"merchantName\": \"G and S Questions Inc.\"}"
            }, {
                "settingName": "hostedPaymentIFrameCommunicatorUrl",
                "settingValue": "{\"url\": \"https://mysite.com/iFrameCommunicator.html\"}"
            }]
        }
    }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
    "token": "FCfc6VbKGFztf8g4sI0B1bG35quHGGlnJx7G8zRpqV0gha2862KkqRQ/NaGa6y2SIhueCAsP/CQKQDQ0QJr8mOfnZD2D0EfogSWP6tQvG3xlv1LS28wFKZHt2U/DSH64eA3jLIwEdU+++++++++++++shortened_for_brevity++++++++WC1mNVQNKv2Z+ 1msH4oiwoXVleb2Q7ezqHYl1FgS8jDAYzA7ls+AYf05s=.89nE4Beh",
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
    $response = $request->getHostedPaymentPageRequest([
        "transactionRequest" => [
            "transactionType" => "authCaptureTransaction",
            "amount" => "20.00",
            "profile" => [
                "customerProfileId" => "123456789"
            ],
            "customer" => [
                "email" => "ellen@mail.com"
            ],
            "billTo" => [
                "firstName" => "Ellen",
                "lastName" => "Johnson",
                "company" => "Souveniropolis",
                "address" => "14 Main Street",
                "city" => "Pecan Springs",
                "state" => "TX",
                "zip" => "44628",
                "country" => "USA"
            ]
        ],
        "hostedPaymentSettings" => [
            "setting" => [[
                "settingName" => "hostedPaymentReturnOptions",
                "settingValue" => "{\"showReceipt\": true, \"url\": \"https://mysite.com/receipt\", \"urlText\": \"Continue\", \"cancelUrl\": \"https://mysite.com/cancel\", \"cancelUrlText\": \"Cancel\"}"
            ], [
                "settingName" => "hostedPaymentButtonOptions",
                "settingValue" => "{\"text\": \"Pay\"}"
            ], [
                "settingName" => "hostedPaymentStyleOptions",
                "settingValue" => "{\"bgColor\": \"blue\"}"
            ], [
                "settingName" => "hostedPaymentPaymentOptions",
                "settingValue" => "{\"cardCodeRequired\": false, \"showCreditCard\": true, \"showBankAccount\": true}"
            ], [
                "settingName" => "hostedPaymentSecurityOptions",
                "settingValue" => "{\"captcha\": false}"
            ], [
                "settingName" => "hostedPaymentShippingAddressOptions",
                "settingValue" => "{\"show\": false, \"required\": false}"
            ], [
                "settingName" => "hostedPaymentBillingAddressOptions",
                "settingValue" => "{\"show\": true, \"required\": false}"
            ], [
                "settingName" => "hostedPaymentCustomerOptions",
                "settingValue" => "{\"showEmail\": false, \"requiredEmail\": false, \"addPaymentProfile\": true}"
            ], [
                "settingName" => "hostedPaymentOrderOptions",
                "settingValue" => "{\"show\": true, \"merchantName\": \"G and S Questions Inc.\"}"
            ], [
                "settingName" => "hostedPaymentIFrameCommunicatorUrl",
                "settingValue" => "{\"url\": \"https://mysite.com/iFrameCommunicator.html\"}"
            ]]
        ]
    ]);
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Hosted Accept.js Payment Form</title>
</head>
<body>
    <form id="paymentForm" method="POST" action="<?= AuthnetAcceptJs::SANDBOX_HOSTED_PAYMENT_URL ?>">
        <input type="hidden" name="token" id="token" value="<?= $response->token ?>" />
        <button onclick="sendPaymentDataToAnet()">Go to Authorize.Net hosted payment form</button>
    </form>
</body>
</html>
