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

Use the Webhooks API to

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------

GET https://apitest.authorize.net/rest/v1/notifications?offset=0&limit=1000


SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------

{
    "_links": {
        "self": {
            "href": "/rest/v1/notifications?offset=0&limit=100"
        }
    },
    "notifications": [
    {
        "_links": {
            "self": {
                "href": "/rest/v1/notifications/e35d5ede-27c5-46cc-aabb-131f10154ed3"
            }
        },
        "notificationId": "e35d5ede-27c5-46cc-aabb-131f10154ed3",
        "deliveryStatus": "Delivered",
        "eventType": "net.authorize.payment.authcapture.created",
        "eventDate": "2017-02-09T19:18:42.167"
        }
    ]
}


 *************************************************************************************************/

namespace JohnConde\Authnet;

require('../../config.inc.php');
require('../../src/autoload.php');

$successful = false;
$error      = true;
try {
    $request    = AuthnetApiFactory::getWebhooksHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $response   = $request->getNotificationHistory();
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
    <title>Webhooks :: Notification History</title>
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
    Webhooks :: Notification History
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
        foreach($response->getNotificationHistory() as $notification) {
            ?>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>
            <tr>
                <th>Notification ID</th>
                <td><?= $notification->getNotificationId(); ?></td>
            </tr>
            <tr>
                <th>Delivery Status</th>
                <td><?= $notification->getDeliveryStatus(); ?></td>
            </tr>
            <tr>
                <th>Event Type</th>
                <td><?= $notification->getEventType(); ?></td>
            </tr>
            <tr>
                <th>Event Date</th>
                <td><?= $notification->getEventDate(); ?></td>
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