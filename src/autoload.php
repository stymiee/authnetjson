<?php

/**
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

spl_autoload_register(
    function($class) {
        static $classes = null;
        if ($classes === null) {
            $classes = array(
                'johnconde\\authnet\\authnetapifactory'                       => '/authnet/AuthnetApiFactory.php',
                'johnconde\\authnet\\authnetjsonrequest'                      => '/authnet/AuthnetJsonRequest.php',
                'johnconde\\authnet\\authnetjsonresponse'                     => '/authnet/AuthnetJsonResponse.php',
                'johnconde\\authnet\\authnetsim'                              => '/authnet/AuthnetSim.php',
                'johnconde\\authnet\\authnetwebhooksrequest'                  => '/authnet/AuthnetWebhooksRequest.php',
                'johnconde\\authnet\\authnetwebhooksresponse'                 => '/authnet/AuthnetWebhooksResponse.php',
                'johnconde\\authnet\\transactionresponse'                     => '/authnet/TransactionResponse.php',
                'johnconde\\authnet\\authnetcannotsetparamsexception'         => '/exceptions/AuthnetCannotSetParamsException.php',
                'johnconde\\authnet\\authnetcurlexception'                    => '/exceptions/AuthnetCurlException.php',
                'johnconde\\authnet\\authnetexception'                        => '/exceptions/AuthnetException.php',
                'johnconde\\authnet\\authnetinvalidcredentialsexception'      => '/exceptions/AuthnetInvalidCredentialsException.php',
                'johnconde\\authnet\\authnetinvalidamountexception'           => '/exceptions/AuthnetInvalidAmountException.php',
                'johnconde\\authnet\\authnetinvalidjsonexception'             => '/exceptions/AuthnetInvalidJsonException.php',
                'johnconde\\authnet\\authnetinvalidserverexception'           => '/exceptions/AuthnetInvalidServerException.php',
                'johnconde\\authnet\\authnettransactionresponsecallexception' => '/exceptions/AuthnetTransactionResponseCallException.php'
            );
        }
        $cn = strtolower($class);
        if (isset($classes[$cn])) {
            require(__DIR__ . $classes[$cn]);
        }
    }
);
