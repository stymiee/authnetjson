<?php

/*************************************************************************************************

Use this function to initiate a request for direct access to the Authorize.Net website.

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
    "getHostedProfilePageRequest": {
        "merchantAuthentication": {
            "name": "5KP3u95bQpv",
            "transactionKey": "346HZ32z3fP4hTG2"
        },
        "customerProfileId": "YourProfileID",
        "hostedProfileSettings": {
            "setting": [
                {
                    "settingName": "hostedProfileReturnUrl",
                    "settingValue": "https://returnurl.com/return/"
                },
                {
                    "settingName": "hostedProfileReturnUrlText",
                    "settingValue": "Continue to confirmation page."
                },
                {
                    "settingName": "hostedProfilePageBorderVisible",
                    "settingValue": "true"
                }
            ]
        }
    }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
    "token": "e3X1JmlCM01EV4HVLqJhdbfStNUmKMkeQ/bm+jBGrFwpeLnaX3E6wmquJZtLXEyMHlcjhNPx471VoGzyrYF1/VIDKk/qcDKT9BShN64Noft0toiYq07nn1CD+w4AzK2kwpSJkjS3I92h9YompnDXSkPKJWopwUesi6n/trJ96CP/m4rf4Xv6vVQqS0DEu+e+foNGkobJwjop2qHPYOp6e+oNGNIYcGYc06VkwE3kQ+ZbBpBhlkKRYdjJdBYRwdSRtcE7YPia2ENTFGNuMYZvFv7rBaoBftWMvapK7Leb1QcE1uQ+t/9X0wlamazbJmubdiE4Gg5GSiFFeVMcMEhUGJyloDCkTzY/Yv1tg0kAK7GfLXLcD+1pwu+YAR4MasCwnFMduwOc3sFOEWmhnU/cvQ==",
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
    $response = $request->getHostedProfilePageRequest([
        "customerProfileId" => "1511887405",
        "hostedProfileSettings" => [
            "setting" => [[
                "settingName" => "hostedProfileReturnUrl",
                "settingValue" => "https://returnurl.com/return/"
            ], [
                "settingName" => "hostedProfileReturnUrlText",
                "settingValue" => "Continue to confirmation page."
            ], [
                "settingName" => "hostedProfilePageBorderVisible",
                "settingValue" => "true"
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
    <form id="paymentForm" method="POST" action="<?= AuthnetAcceptJs::PRODUCTION_HOSTED_CIM_URL ?>">
        <input type="hidden" name="token" id="token" value="<?= $response->token ?>" />
        <button>Go to Authorize.Net hosted payment form</button>
    </form>
</body>
</html>
