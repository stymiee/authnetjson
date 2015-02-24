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
 * Mock object to represent a connection and response to the Authorize.Net API endpoint
 *
 * @package    AuthnetJSON
 * @author     John Conde <stymiee@gmail.com>
 * @copyright  John Conde <stymiee@gmail.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link       https://github.com/stymiee/Authorize.Net-JSON
 */
class UnitTestWrapper Implements ProcessorInterface
{
    /**
     * @var     string  JSON response string
     */
    private $json;

    /**
     * @param   string  $url    The URL to connect to process a transaction (ignored)
     * @param   string  $json   A JSON response to be sent as payload (ignored)
     * @return  string          A JSON response string
     */
    public function process($url, $json)
    {
        return $this->json;
    }

    /**
     * @param   string  $json   Sets a JSON string as a response to be returned to the
     *                          AuthnetJson class
     */
    public function setResponse($json)
    {
        $this->json = $json;
    }

    /**
     * @return string   Returns the name of this class
     */
    public function getName()
    {
        return __CLASS__;
    }
} 