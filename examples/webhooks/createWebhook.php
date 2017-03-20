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

Use the Webhooks API to create a webhook

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------

POST https://apitest.authorize.net/rest/v1/webhooks

{
    "url": "http://localhost:55950/api/webhooks",
    "eventTypes": [
        "net.authorize.payment.authcapture.created",
        "net.authorize.customer.created",
        "net.authorize.customer.paymentProfile.created",
        "net.authorize.customer.subscription.expiring"
    ],
    "status": "active"
}


SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------

{
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
}

*************************************************************************************************/

namespace JohnConde\Authnet;

require('../../config.inc.php');
require('../../src/autoload.php');

$successful = false;
$error      = true;
try {
    $request  = AuthnetApiFactory::getWebhooksHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $response = $request->createWebhooks([
        'net.authorize.customer.created',
        'net.authorize.customer.deleted',
        'net.authorize.customer.updated',
        'net.authorize.customer.paymentProfile.created',
        'net.authorize.customer.paymentProfile.deleted',
        'net.authorize.customer.paymentProfile.updated',
        'net.authorize.customer.subscription.cancelled',
        'net.authorize.customer.subscription.created',
        'net.authorize.customer.subscription.expiring',
        'net.authorize.customer.subscription.suspended',
        'net.authorize.customer.subscription.terminated',
        'net.authorize.customer.subscription.updated',
        'net.authorize.payment.authcapture.created',
        'net.authorize.payment.authorization.created',
        'net.authorize.payment.capture.created',
        'net.authorize.payment.fraud.approved',
        'net.authorize.payment.fraud.declined',
        'net.authorize.payment.fraud.held',
        'net.authorize.payment.priorAuthCapture.created',
        'net.authorize.payment.refund.created',
        'net.authorize.payment.void.created'
    ], 'http://requestb.in/', 'active');
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
        <title>Webhooks :: Create Webhooks</title>
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
            Webhooks :: Create Webhooks
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
                ?>
                <tr>
                    <th>Event Types</th>
                    <td>
                        <?php
                        foreach ($response->getEventTypes() as $eventType) {
                            echo $eventType, "<br>\n";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Webhook ID</th>
                    <td><?= $response->getWebhooksId(); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?= $response->getStatus(); ?></td>
                </tr>
                <tr>
                    <th>URL</th>
                    <td><?= $response->getUrl(); ?></td>
                </tr>
                <?php
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