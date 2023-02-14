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

Use the SIM tools to create a SIM transaction form

*************************************************************************************************/

namespace Authnetjson;

use Exception;

require '../../config.inc.php';

try {
    $sim = AuthnetApiFactory::getSimHandler(
        AUTHNET_LOGIN,
        AUTHNET_SIGNATURE,
        AuthnetApiFactory::USE_DEVELOPMENT_SERVER
    );
    $amount = 10.00;
    $login = $sim->getLogin();
    $url = $sim->getEndpoint();
    $fingerprint = $sim->getFingerprint($amount);
    $sequence = $sim->getSequence();
    $timestamp = $sim->getTimestamp();
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SIM (Deprecated)</title>
</head>
<body>
    <h1>
        SIM (Deprecated)
    </h1>
    <pre>
        Login: <?= $login ?><br>
        Endpoint: <?= $url ?><br>
        Hash: <?= $fingerprint ?><br>
        Sequence: <?= $sequence ?><br>
        Timestamp: <?= $timestamp ?>
    </pre>
    <form method="post" action="<?= $url ?>">
        <input type='hidden' name='x_login' value='<?= $login ?>' />
        <input type='hidden' name='x_amount' value='<?= $amount ?>' />
        <input type='hidden' name='x_description' value='<?= 'Test Transaction' ?>' />
        <input type='hidden' name='x_invoice_num' value='<?= '123456789' ?>' />
        <input type='hidden' name='x_fp_sequence' value='<?= $sequence ?>' />
        <input type='hidden' name='x_fp_timestamp' value='<?= $timestamp ?>' />
        <input type='hidden' name='x_fp_hash' value='<?= $fingerprint ?>' />
        <input type='hidden' name='x_show_form' value='PAYMENT_FORM' />
        <input type='submit' value='Submit' />
    </form>
</body>
</html>
