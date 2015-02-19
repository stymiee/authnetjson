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
 *
 *
 * @package    AuthnetJSON
 * @author     John Conde <stymiee@gmail.com>
 * @copyright  John Conde <stymiee@gmail.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       https://github.com/stymiee/Authorize.Net-JSON
 */
class AuthnetJson
{
    private $login;
    private $transaction_key;
    private $url;
    private $json;
    private $processor;
    private $response;
    private $response_json;

	public function __construct($login, $transaction_key, $api_url)
	{
		$this->login           = $login;
        $this->transaction_key = $transaction_key;
        $this->url             = $api_url;
	}

	public function __toString()
	{
	    $output  = '';
        $output .= '<table summary="Authorize.Net Results" id="authnet">' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<th colspan="2"><b>Class Parameters</b></th>' . "\n" . '</tr>' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<td><b>API Login ID</b></td><td>' . $this->login . '</td>' . "\n" . '</tr>' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<td><b>Transaction Key</b></td><td>' . $this->transaction_key . '</td>' . "\n" . '</tr>' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<td><b>Authnet Server URL</b></td><td>' . $this->url . '</td>' . "\n" . '</tr>' . "\n";
        $output .= '<tr>' . "\n\t\t" . '<th colspan="2"><b>Request JSON</b></th>' . "\n" . '</tr>' . "\n";
        if (!empty($this->json)) {
            $output .= '<tr><td colspan="2"><pre>' . "\n";
            $output .= $this->json . "\n";
            $output .= '</pre></td></tr>' . "\n";
        }
        if (!empty($this->response)) {
            $output .= '<tr>' . "\n\t\t" . '<th colspan="2"><b>Response JSON</b></th>' . "\n" . '</tr>' . "\n";
            $output .= '<tr><td colspan="2"><pre>' . "\n";
            $output .= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->response). "\n";
            $output .= '</pre></td></tr>' . "\n";
        }
        $output .= '</table>';

        return $output;
	}

    public function __get($var)
	{
	    return $this->response_json->{$var};
	}

	public function __set($key, $value)
	{
        throw new AuthnetCannotSetParamsException('You cannot set parameters directly in ' . __CLASS__ . '.');
	}

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
        $this->json = json_encode($parameters);

		$this->process();
	}

	private function process()
	{
        $this->response = $this->processor->process($this->url, $this->json);
        $this->response_json = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $this->response));
	}

	public function isSuccessful()
    {
        return $this->messages->resultCode === 'Ok';
    }

    public function isError()
    {
        return $this->messages->resultCode === 'Error';
    }

    public function setProcessHandler($processor)
    {
        $this->processor = $processor;
    }

    public function identifyProcessorWrapper()
    {
        return $this->processor->getName();
    }
}