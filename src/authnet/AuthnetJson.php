<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace JohnConde\Authnet;

/**
 * Adapter for the Authorize.Net JSON API
 *
 * @package    AuthnetJSON
 * @author     John Conde <stymiee@gmail.com>
 * @copyright  John Conde <stymiee@gmail.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link       https://github.com/stymiee/Authorize.Net-JSON
 */
class AuthnetJson
{
    /**
     * @var     string  Authorize.Net API login ID
     */
    private $login;

    /**
     * @var     string  Authorize.Net API Transaction Key
     */
    private $transaction_key;

    /**
     * @var     string  URL endpoint for processing a transaction
     */
    private $url;

    /**
     * @var     string  JSON formatted API request
     */
    private $request_json;

    /**
     * @var     object  Wrapper object repsenting an endpoint
     */
    private $processor;

    /**
     * @var     object  SimpleXML object representing the API response
     */
    private $response;

    /**
     * @var     string  JSON response
     */
    private $response_json;

    /**
     * @param   string  $login              Authorize.Net API login ID
     * @param   string  $transaction_key    Authorize.Net API Transaction Key
     * @param   string  $api_url            URL endpoint for processing a transaction
     */
	public function __construct($login, $transaction_key, $api_url)
	{
		$this->login           = $login;
        $this->transaction_key = $transaction_key;
        $this->url             = $api_url;
	}

    /**
     * @return  string  HTML table containing debugging information
     */
	public function __toString()
	{
	    $output  = '';
        $output .= '<table summary="Authorize.Net Results" id="authnet">' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<th colspan="2"><b>Class Parameters</b></th>' . "\n" . '</tr>' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<td><b>API Login ID</b></td><td>' . $this->login . '</td>' . "\n" . '</tr>' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<td><b>Transaction Key</b></td><td>' . $this->transaction_key . '</td>' . "\n" . '</tr>' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<td><b>Authnet Server URL</b></td><td>' . $this->url . '</td>' . "\n" . '</tr>' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<th colspan="2"><b>Request JSON</b></th>' . "\n" . '</tr>' . "\n";
        if (!empty($this->request_json)) {
            $output .= '<tr><td colspan="2"><pre>' . "\n";
            $output .= $this->request_json . "\n";
            $output .= '</pre></td></tr>' . "\n";
        }
        if (!empty($this->response_json)) {
            $output .= '<tr>' . "\n\t\t" . '<th colspan="2"><b>Response JSON</b></th>' . "\n" . '</tr>' . "\n";
            $output .= '<tr><td colspan="2"><pre>' . "\n";
            $output .= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->response_json). "\n";
            $output .= '</pre></td></tr>' . "\n";
        }
        $output .= '</table>';

        return $output;
	}

    /**
     *
     */
    public function __get($var)
	{
	    return $this->response->{$var};
	}

    /**
     *
     */
    public function __set($key, $value)
	{
        throw new AuthnetCannotSetParamsException('You cannot set parameters directly in ' . __CLASS__ . '.');
	}

    /**
     *
     */
    public function __call($api_call, $args)
	{
        $authentication = array(
            'merchantAuthentication' => array(
                'name'           => $this->login,
                'transactionKey' => $this->transaction_key,
            )
        );
        $call = array();
        if (count($args)) {
            $call = $args[0];
        }
        $parameters = array(
            $api_call => $authentication + $call
        );
        $this->request_json = json_encode($parameters);

		$this->process();
	}

    /**
     *
     */
    private function process()
	{
        $this->response_json = $this->processor->process($this->url, $this->request_json);
        $this->response = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->response_json));
	}

    /**
     * @return  bool    Whether the transaction was in an successful state
     */
    public function isSuccessful()
    {
        return $this->messages->resultCode === 'Ok';
    }

    /**
     * @return  bool    Whether the transaction was in an error state
     */
    public function isError()
    {
        return $this->messages->resultCode === 'Error';
    }

    /**
     * @param   object  $processor  Instance of \JohnConde\Authnet\ProcessorInterface
     */
    public function setProcessHandler(ProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @return  string  The name of the processor wrapper class
     */
    public function identifyProcessorWrapper()
    {
        return $this->processor->getName();
    }
}