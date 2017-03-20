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

Use the Webhooks API to send a test webhook

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------

POST https://apitest.authorize.net/rest/v1/webhooks/<webhookId>/pings


SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------

HTTP 200 response for success
HTTP 500 response for connection error

*************************************************************************************************/

namespace JohnConde\Authnet;

require('../../config.inc.php');
require('../../src/autoload.php');

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