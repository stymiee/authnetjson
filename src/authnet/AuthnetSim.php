<?php

/**
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnConde\Authnet;

/**
 * Wrapper to simplify the creation of SIM data
 *
 * @package    AuthnetJSON
 * @author     John Conde <stymiee@gmail.com>
 * @copyright  John Conde <stymiee@gmail.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link       https://github.com/stymiee/authnetjson
 */

class AuthnetSim
{
    /**
     * @var     string  Authorize.Net API login ID
     */
    private $login;

    /**
     * @var     string  Authorize.Net API Transaction Key
     */
    private $transactionKey;

    /**
     * @var     string  URL endpoint for processing a transaction
     */
    private $url;

    /**
     * @var     integer  Randomly generated number
     */
    private $sequence;

    /**
     * @var     integer  Unix timestamp the request was made
     */
    private $timestamp;

    /**
     * Creates a SIM wrapper by setting the Authorize.Net credentials and URL of the endpoint to be used
     * for the API call
     *
     * @param   string  $login              Authorize.Net API login ID
     * @param   string  $transactionKey     Authorize.Net API Transaction Key
     * @param   string  $api_url            URL endpoint for processing a transaction
     */
    public function __construct($login, $transactionKey, $api_url)
    {
        $this->login          = $login;
        $this->transactionKey = $transactionKey;
        $this->url            = $api_url;
        $this->resetParameters();
    }

    /**
     * Returns the hash for the SIM transaction
     *
     * @param   float  $amount   The amount of the transaction
     * @return  string           Hash of five different unique transaction parameters
     * @throws  \JohnConde\Authnet\AuthnetInvalidAmountException
     */
    public function getFingerprint($amount)
    {
        if (!filter_var($amount, FILTER_VALIDATE_FLOAT)) {
            throw new AuthnetInvalidAmountException('You must enter a valid amount greater than zero.');
        }

        return hash_hmac('md5', sprintf('%s^%s^%s^%s^',
            $this->login,
            $this->sequence,
            $this->timestamp,
            $amount
        ),  $this->transactionKey);
    }

    /**
     * Returns the sequence generated for a transaction
     *
     * @return  integer           Current sequence
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Returns the timestamp for a transaction
     *
     * @return  integer           Current timestamp
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Returns the account login ID
     *
     * @return  string           API login ID
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Returns the url endpoint for the transaction
     *
     * @return  string           url endpoint
     */
    public function getEndpoint()
    {
        return $this->url;
    }

    /**
     * Resets the sequence and timestamp
     */
    public function resetParameters()
    {
        $this->sequence  = rand(1, 1000);
        $this->timestamp = time();
    }
}