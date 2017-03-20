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

Use the Webhooks API to retrieve a webhook

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------

GET https://apitest.authorize.net/rest/v1/webhooks/72a55c78-66e6-4b1e-a4d6-3f925c00561f


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
    $request    = AuthnetApiFactory::getWebhooksHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $response   = $request->getWebhook('cd2c262f-2723-4848-ae92-5d317902441c');
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
    <title>Webhooks :: Get A Webhook</title>
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
    Webhooks :: Get A Webhook
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