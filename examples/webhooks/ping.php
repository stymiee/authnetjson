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

Use the Webhooks API to send a test webhook.

When a webhook is inactive, you can send a test event to the Webhooks endpoint using this method:

POST https://apitest.authorize.net/rest/v1/webhooks/72a55c78-66e6-4b1e-a4d6-3f925c00561f/pings

The POST request message body should be empty. Construct the request URL containing the webhook ID that you want to
test. Then, make an HTTP POST request to that URL. Authorize.Net receives the request and sends a notification to
the registered URL for that webhook, emulating the event for that webhook.

Note: This request works only on webhooks that are inactive. To test an active webhook, you must first set the webhook
status to inactive.

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------

POST https://apitest.authorize.net/rest/v1/webhooks/ba4c73f3-0808-48bf-ae2f-f49064770e60/pings


SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------

HTTP 200 response for success
HTTP 500 response for connection error

*************************************************************************************************/

namespace JohnConde\Authnet;

require('../../config.inc.php');

$successful = false;
$error      = true;
try {
    $request    = AuthnetApiFactory::getWebhooksHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $request->testWebhook('ba4c73f3-0808-48bf-ae2f-f49064770e60');
    $successful = true;
    $error      = false;
}
catch (\Exception $e) {
    $errorMessage = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Webhooks :: Create Webhooks</title>
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
    Webhooks :: Ping
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
        if ($error) {
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
echo $request;
?>
</body>
</html>