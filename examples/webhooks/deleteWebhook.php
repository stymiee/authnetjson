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

DELETE https://apitest.authorize.net/rest/v1/webhooks/<webhookId>


SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------

HTTP response code 200 will be returned for a successful deletion


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
    $request->deleteWebhook('0550f061-59a1-4f13-a9da-3e8bfc50e80b');
    $successful = true;
    $error      = false;
} catch (Exception $e) {
    echo $e;
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Webhooks :: Delete Webhooks</title>
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
    Webhooks :: Delete Webhooks
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
            <td><?= $response->errorMessage ?></td>
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
