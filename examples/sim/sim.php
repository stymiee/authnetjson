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

    namespace JohnConde\Authnet;

    require('../../config.inc.php');
    require('../../src/autoload.php');

    $sim         = AuthnetApiFactory::getSimHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $amount      = 10.00;
    $login       = $sim->getLogin();
    $url         = $sim->getEndpoint();
    $fingerprint = $sim->getFingerprint($amount);
    $sequence    = $sim->getSequence();
    $timestamp   = $sim->getTimestamp();
?>

<!DOCTYPE html>
<html>
<html lang="en">
    <head>
        <title>SIM </title>
    </head>
    <body>
        <h1>
            SIM
        </h1>
        <pre>
            Login: <?php echo $login; ?><br>
            Endpoint: <?php echo $url; ?><br>
            Hash: <?php echo $fingerprint; ?><br>
            Sequence: <?php echo $sequence; ?><br>
            Timestamp: <?php echo $timestamp; ?>
        </pre>
        <form method="post" action="<?php echo $url; ?>">
            <input type='hidden' name='x_login' value='<?php echo $login; ?>' />
            <input type='hidden' name='x_amount' value='<?php echo $amount; ?>' />
            <input type='hidden' name='x_description' value='<?php echo 'Test Transaction'; ?>' />
            <input type='hidden' name='x_invoice_num' value='<?php echo '123456789'; ?>' />
            <input type='hidden' name='x_fp_sequence' value='<?php echo $sequence; ?>' />
            <input type='hidden' name='x_fp_timestamp' value='<?php echo $timestamp; ?>' />
            <input type='hidden' name='x_fp_hash' value='<?php echo $fingerprint; ?>' />
            <input type='hidden' name='x_show_form' value='PAYMENT_FORM' />
            <input type='submit' value='Submit' />
        </form>
    </body>
</html>
