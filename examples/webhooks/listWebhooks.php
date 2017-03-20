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

Use the Webhooks API to list all of my webhooks

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------

GET https://apitest.authorize.net/rest/v1/webhooks


SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------

[{
        "_links": {
            "self": {
                "href": "/rest/v1/webhooks/72a55c78-66e6-4b1e-a4d6-3f925c00561f"
            }
        },
        "webhookId": "72a55c78-66e6-4b1e-a4d6-3f925c00561f",
        "eventTypes": [
            "net.authorize.payment.authcapture.created",
            "net.authorize.customer.created",
            "net.authorize.customer.paymentProfile.created",
            "net.authorize.customer.subscription.expiring"
        ],
        "status": "active",
        "url": "http://localhost:55950/api/webhooks"
    }, {
        "_links": {
            "self": {
                "href": "/rest/v1/webhooks/7be120d3-2247-4706-b9b1-98931fdfdcce"
            }
        },
        "webhookId": "7be120d3-2247-4706-b9b1-98931fdfdcce",
        "eventTypes": [
            "net.authorize.customer.subscription.expiring",
            "net.authorize.customer.paymentProfile.created",
            "net.authorize.payment.authcapture.created",
            "net.authorize.customer.created"
        ],
        "status": "inactive",
        "url": "http://localhost:55950/api/webhooks"
    }, {
        "_links": {
            "self": {
                "href": "/rest/v1/webhooks/62c68677-0d71-43a7-977a-f4dea3827fac"
            }
        },
        "webhookId": "62c68677-0d71-43a7-977a-f4dea3827fac",
        "eventTypes": [
            "net.authorize.customer.subscription.expiring",
            "net.authorize.customer.created",
            "net.authorize.customer.paymentProfile.created",
            "net.authorize.payment.authcapture.created"
        ],
        "status": "active",
        "url": "http://localhost:55950/api/webhooks"
}]


 *************************************************************************************************/

namespace JohnConde\Authnet;

require('../../config.inc.php');
require('../../src/autoload.php');

$successful = false;
$error      = true;
try {
    $request    = AuthnetApiFactory::getWebhooksHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $response   = $request->getWebhooks();
    $successful = true;
    $error      = false;
}
catch (\Exception $e) {
    $errorMessage = $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
<html lang="en">
<head>
    <title>Webhooks :: List Webhooks</title>
    <style type="text/css">
        table {
            border: 1px solid #cccccc;
            margin: auto;
            border-collapse: collapse;
            max-width: 90%;
        }

        table td {
            padding: 3px 5px;
            vertical-align: top;
            border-top: 1px solid #cccccc;
        }

        pre {
            white-space: pre-wrap; /* css-3 */
            word-wrap: break-word; /* Internet Explorer 5.5+ */
        }

        table th {
            background: #e5e5e5;
            color: #666666;
        }

        h1, h2 {
            text-align: center;
        }
    </style>
</head>
<body>
<h1>
    Webhooks :: List Webhooks
</h1>
<h2>
    Results
</h2>
<table>
    <tr>
        <th>Successful</th>
        <td><?= ($successful) ? 'Yes' : 'No';?></td>
    </tr>
    <?php
    if ($successful) {
        foreach($response->getWebhooks() as $webhook) {
            ?>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>
            <tr>
                <th>Event Types</th>
                <td>
                    <?php
                    foreach ($webhook->getEventTypes() as $eventType) {
                        echo $eventType, "<br>\n";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Webhook ID</th>
                <td><?= $webhook->getWebhooksId(); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?= $webhook->getStatus(); ?></td>
            </tr>
            <tr>
                <th>URL</th>
                <td><?= $webhook->getUrl(); ?></td>
            </tr>
            <?php
        }
    }
    elseif ($error) {
        ?>
        <tr>
            <th>Error message</th>
            <td><?= $errorMessage; ?></td>
        </tr>
        <?php
    }
    ?>
</table>
<h2>
    Raw Input/Output
</h2>
<?php
echo $request, $response;
?>
</body>
</html>