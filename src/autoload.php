<?php

spl_autoload_register(
    function($class) {
        static $classes = null;
        if ($classes === null) {
            $classes = array(
                'johnconde\\authnet\\authnetapifactory'                  => '/authnet/AuthnetApiFactory.php',
                'johnconde\\authnet\\authnetjson'                        => '/authnet/AuthnetJson.php',
                'johnconde\\authnet\\curlwrapper'                        => '/authnet/CurlWrapper.php',
                'johnconde\\authnet\\authnetcannotsetparamsexception'    => '/exceptions/AuthnetCannotSetParamsException.php',
                'johnconde\\authnet\\authnetcurlexception'               => '/exceptions/AuthnetCurlException.php',
                'johnconde\\authnet\\authnetexception'                   => '/exceptions/AuthnetException.php',
                'johnconde\\authnet\\authnetinvalidcredentialsexception' => '/exceptions/AuthnetInvalidCredentialsException.php',
                'johnconde\\authnet\\authnetinvalidserverexception'      => '/exceptions/AuthnetInvalidServerException.php'
            );
        }
        $cn = strtolower($class);
        if (isset($classes[$cn])) {
            require(__DIR__ . $classes[$cn]);
        }
    }
);
