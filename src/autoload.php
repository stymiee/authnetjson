<?php

spl_autoload_register(
    function($class) {
        static $classes = null;
        if ($classes === null) {
            $classes = array(
                'johnconde\\authnet\\authnetapifactory'                  => '/authnet/AuthnetApiFactory.php',
                'johnconde\\authnet\\authnetjsonrequest'                 => '/authnet/AuthnetJsonRequest.php',
                'johnconde\\authnet\\authnetjsonresponse'                => '/authnet/AuthnetJsonResponse.php',
                'johnconde\\authnet\\curlwrapper'                        => '/authnet/CurlWrapper.php',
                'johnconde\\authnet\\authnetcannotsetparamsexception'    => '/exceptions/AuthnetCannotSetParamsException.php',
                'johnconde\\authnet\\authnetcurlexception'               => '/exceptions/AuthnetCurlException.php',
                'johnconde\\authnet\\authnetexception'                   => '/exceptions/AuthnetException.php',
                'johnconde\\authnet\\authnetinvalidcredentialsexception' => '/exceptions/AuthnetInvalidCredentialsException.php',
                'johnconde\\authnet\\authnetinvalidjsonexception'        => '/exceptions/AuthnetInvalidJsonException.php',
                'johnconde\\authnet\\authnetinvalidserverexception'      => '/exceptions/AuthnetInvalidServerException.php'
            );
        }
        $cn = strtolower($class);
        if (isset($classes[$cn])) {
            require(__DIR__ . $classes[$cn]);
        }
    }
);
