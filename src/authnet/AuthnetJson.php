<?php

/*************************************************************************************************

This class allows for easy use of any Authorize.Net JSON based APIs. More information
about these APIs can be found at http://developer.authorize.net/api/.

PHP version 5

LICENSE: This program is free software: you can redistribute it and/or modify
it under the terms of the MIT License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with this program.  If not, see http://www.gnu.org/licenses/.

@package    AuthnetJson
@author     John Conde <authnet@johnconde.net>
@copyright  2015 John Conde
@license    MIT License
@version    1.0
@link       http://www.johnconde.net/

**************************************************************************************************/

namespace JohnConde\Authnet;

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