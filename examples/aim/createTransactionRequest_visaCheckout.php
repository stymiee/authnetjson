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

Use the AIM JSON API to process a Visa Checkout transaction

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
    "createTransactionRequest": {
        "merchantAuthentication": {
            "name": "cnpdev4289",
            "transactionKey": "SR2P8g4jdEn7vFLQ"
        },
        "refId": "123456",
        "transactionRequest": {
            "transactionType": "authCaptureTransaction",
            "amount": "5",
            "payment": {
                "opaqueData": {
                    "dataDescriptor": "COMMON.VCO.ONLINE.PAYMENT",
                    "dataValue": "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",
                    "dataKey": "k5qdgh6t5BnvuBz3Xs0f+iraXB0tXO+6WewMUVTBsqYN3G16HHYwlz8To602G/pZ+Sng0mjTAy3mwhpOb/5nJktiCTpKL4wvn0exJxGt9GIAODBYz4PFbxuenmuQE9O5"
                }
            },
            "callId": "4859677641513545101"
        }
    }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
    "transactionResponse": {
        "responseCode": "1",
        "authCode": "C1E3I6",
        "avsResultCode": "Y",
        "cvvResultCode": "S",
        "cavvResultCode": "9",
        "transId": "2149186775",
        "refTransID": "",
        "transHash": "C85B15CED28462974F1114DB07A16C39",
        "testRequest": "0",
        "accountNumber": "XXXX0015",
        "accountType": "MasterCard",
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

    require('../../config.inc.php');
    require('../../src/autoload.php');

    $request  = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $response = $request->createTransactionRequest(array(
        "refId" => rand(1000000, 100000000),
        "transactionRequest" => array(
            "transactionType" => "authCaptureTransaction",
            "amount" => "5",
            "payment" => array(
                "opaqueData" => array(
                    "dataDescriptor" => "COMMON.VCO.ONLINE.PAYMENT",
                    "dataValue" => "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",
                    "dataKey" => "k5qdgh6t5BnvuBz3Xs0f+iraXB0tXO+6WewMUVTBsqYN3G16HHYwlz8To602G/pZ+Sng0mjTAy3mwhpOb/5nJktiCTpKL4wvn0exJxGt9GIAODBYz4PFbxuenmuQE9O5"
                ),
            ),
            "callId" => "4859677641513545101"
        ),
    ));
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>AIM :: Visa Checkout</title>
        <style type="text/css">
            table
            {
                border: 1px solid #cccccc;
                margin: auto;
                border-collapse: collapse;
                max-width: 90%;
            }

            table td
            {
                padding: 3px 5px;
                vertical-align: top;
                border-top: 1px solid #cccccc;
            }

            pre
            {
            	overflow-x: auto; /* Use horizontal scroller if needed; for Firefox 2, not needed in Firefox 3 */
            	white-space: pre-wrap; /* css-3 */
            	white-space: -moz-pre-wrap !important; /* Mozilla, since 1999 */
            	white-space: -pre-wrap; /* Opera 4-6 */
            	white-space: -o-pre-wrap; /* Opera 7 */ /*
            	width: 99%; */
            	word-wrap: break-word; /* Internet Explorer 5.5+ */
            }

            table th
            {
                background: #e5e5e5;
                color: #666666;
            }

            h1, h2
            {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <h1>
            AIM :: Authorize and Capture
        </h1>
        <h2>
            Results
        </h2>
        <table>
            <tr>
                <th>Response</th>
                <td><?php echo $response->messages->resultCode; ?></td>
            </tr>
            <tr>
                <th>Successful?</th>
                <td><?php echo ($response->isSuccessful()) ? 'yes' : 'no'; ?></td>
            </tr>
            <tr>
                <th>Error?</th>
                <td><?php echo ($response->isError()) ? 'yes' : 'no'; ?></td>
            </tr>
            <?php if ($response->isSuccessful()) : ?>
            <tr>
                <th>Description</th>
                <td><?php echo $response->transactionResponse->messages[0]->description; ?></td>
            </tr>
            <tr>
                <th>authCode</th>
                <td><?php echo $response->transactionResponse->authCode; ?></td>
            </tr>
            <tr>
                <th>transId</th>
                <td><?php echo $response->transactionResponse->transId; ?></td>
            </tr>
            <?php elseif ($response->isError()) : ?>
            <tr>
                <th>Error Code</th>
                <td><?php echo $response->getErrorCode(); ?></td>
            </tr>
            <tr>
                <th>Error Message</th>
                <td><?php echo  $response->getErrorText(); ?></td>
            </tr>
            <?php endif; ?>
        </table>
        <h2>
            Raw Input/Output
        </h2>
<?php
    echo $request, $response;
?>
    </body>
</html>
