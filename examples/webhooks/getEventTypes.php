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

Use the Webhooks API to retreive a list of all available webhooks

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------

GET https://apitest.authorize.net/rest/v1/eventtypes


SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------

[
    {
        "name": "net.authorize.customer.created"
    },
    {
        "name": "net.authorize.customer.deleted"
    },
    {
        "name": "net.authorize.customer.updated"
    },
    {
        "name": "net.authorize.customer.paymentProfile.created"
    },
    {
        "name": "net.authorize.customer.paymentProfile.deleted"
    },
    {
        "name": "net.authorize.customer.paymentProfile.updated"
    },
    {
        "name": "net.authorize.customer.subscription.cancelled"
    },
    {
        "name": "net.authorize.customer.subscription.created"
    },
    {
        "name": "net.authorize.customer.subscription.expiring"
    },
    {
        "name": "net.authorize.customer.subscription.suspended"
    },
    {
        "name": "net.authorize.customer.subscription.terminated"
    },
    {
        "name": "net.authorize.customer.subscription.updated"
    },
    {
        "name": "net.authorize.payment.authcapture.created"
    },
    {
        "name": "net.authorize.payment.authorization.created"
    },
    {
        "name": "net.authorize.payment.capture.created"
    },
    {
        "name": "net.authorize.payment.fraud.approved"
    },
    {
        "name": "net.authorize.payment.fraud.declined"
    },
    {
        "name": "net.authorize.payment.fraud.held"
    },
    {
        "name": "net.authorize.payment.priorAuthCapture.created"
    },
    {
        "name": "net.authorize.payment.refund.created"
    },
    {
        "name": "net.authorize.payment.void.created"
    }
]

 *************************************************************************************************/

namespace JohnConde\Authnet;

require('../../config.inc.php');
require('../../src/autoload.php');

$successful = false;
$error      = true;
try {
    $request    = AuthnetApiFactory::getWebhooksHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $response   = $request->getEventTypes();
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
    <title>Webhooks :: Get Event Types</title>
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
    Webhooks :: Get Event Types
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