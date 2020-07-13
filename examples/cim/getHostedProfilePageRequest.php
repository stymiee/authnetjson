<?php
/*************************************************************************************************

Use the CIM JSON API to retrieve a hosted payment page token

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "getHostedProfilePageRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "customerProfileId":"31390172",
      "hostedProfileSettings":{
         "setting":{
            "settingName":"hostedProfilePageBorderVisible",
            "settingValue":"true"
         }
      }
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "token":"Mvwo9mTx2vS332eCFY3rFzh/x1x64henm7rppLYQxd2cOzNpw+bfp1ZTVKvu98XSIvL9VIEB65mCFtzchN/pFKBdBA0daBukS27pWYxZuo6QpBUpz2p6zLENX8qH9wCcAw6EJr0MZkNttPW6b+Iw9eKfcBtJayq6kdNm9m1ywANHsg9xME4qUccBXnY2cCf3kLaaLNJhhiNxJmcboKNlDn5HtIQ/wcRnxB4YbqddTN8=",
   "messages":{
      "resultCode":"Ok",
      "message":[
         {
            "code":"I00001",
            "text":"Successful."
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
    $response = $request->getHostedProfilePageRequest([
        'customerProfileId' => '31390172',
        'hostedProfileSettings' => [
            [
                'settingName' => 'hostedProfileReturnUrl',
                'settingValue' => 'https://blah.com/blah/',
            ],
            [
                'settingName' => 'hostedProfileReturnUrlText',
                'settingValue' => 'Continue to blah.',
            ],
            [
                'settingName' => 'hostedProfilePageBorderVisible',
                'settingValue' => 'true',
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
    <title>CIM :: Get Hosted Profile Page</title>
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
        CIM :: Get Hosted Profile Page
    </h1>
    <h2>
        Results
    </h2>
    <table>
        <tr>
            <th>Successful?</th>
            <td><?= $response->isSuccessful() ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <th>Error?</th>
            <td><?= $response->isError() ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <th>Result Code</th>
            <td><?= $response->messages->resultCode ?></td>
        </tr>
        <tr>
            <th>Message Code</th>
            <td><?= $response->messages->message[0]->code ?></td>
        </tr>
        <tr>
            <th>Message</th>
            <td><?= $response->messages->message[0]->text ?></td>
        </tr>
        <tr>
            <th>Token</th>
            <td><?= $response->token ?></td>
        </tr>
    </table>
    <h2>
        Raw Input/Output
    </h2>
    <?= $request, $response ?>
</body>
</html>
