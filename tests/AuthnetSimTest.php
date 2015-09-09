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

class AuthnetJsonSimTest extends \PHPUnit_Framework_TestCase
{
    private $login;
    private $transactionKey;
    private $server;

    protected function setUp()
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
        $this->server         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetSim::__construct()
     */
    public function testConstructor()
    {
        $request = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);

        $reflectionOfSim = new \ReflectionObject($request);
        $login = $reflectionOfSim->getProperty('login');
        $login->setAccessible(true);
        $key = $reflectionOfSim->getProperty('transactionKey');
        $key->setAccessible(true);

        $this->assertEquals($login->getValue($request), $this->login);
        $this->assertEquals($key->getValue($request), $this->transactionKey);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetSim::getFingerprint()
     */
    public function testGetFingerprint()
    {
        $amount    = 9.01;

        $sim       = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);
        $hash      = $sim->getFingerprint($amount);
        $sequence  = $sim->getSequence();
        $timestamp = $sim->getTimestamp();

        $this->assertEquals($hash, hash_hmac('md5', sprintf('%s^%s^%s^%s^',
            $this->login,
            $sequence,
            $timestamp,
            $amount
        ),  $this->transactionKey));
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetSim::getFingerprint()
     * @expectedException \JohnConde\Authnet\AuthnetInvalidAmountException
     */
    public function testGetFingerprintException()
    {
        $amount    = 0;
        $sim       = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);
        $hash      = $sim->getFingerprint($amount);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetSim::getSequence()
     */
    public function testGetSequence()
    {
        $sim = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);
        $sequence = $sim->getSequence();

        $reflectionOfRequest = new \ReflectionObject($sim);
        $sequenceReflection = $reflectionOfRequest->getProperty('sequence');
        $sequenceReflection->setAccessible(true);

        $this->assertEquals($sequence, $sequenceReflection->getValue($sim));
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetSim::getTimestamp()
     */
    public function testGetTimestamp()
    {
        $sim = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);
        $timestamp = $sim->getTimestamp();

        $reflectionOfRequest = new \ReflectionObject($sim);
        $timestampReflection = $reflectionOfRequest->getProperty('timestamp');
        $timestampReflection->setAccessible(true);

        $this->assertEquals($timestamp, $timestampReflection->getValue($sim));
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetSim::getLogin()
     */
    public function testGetLogin()
    {
        $sim   = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);
        $login = $sim->getLogin();

        $reflectionOfRequest = new \ReflectionObject($sim);
        $loginReflection = $reflectionOfRequest->getProperty('login');
        $loginReflection->setAccessible(true);

        $this->assertEquals($login, $loginReflection->getValue($sim));
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetSim::getEndpoint()
     */
    public function testGetEndpoint()
    {
        $sim = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);
        $url = $sim->getEndpoint();

        $reflectionOfRequest = new \ReflectionObject($sim);
        $endpointReflection = $reflectionOfRequest->getProperty('url');
        $endpointReflection->setAccessible(true);

        $this->assertEquals($url, $endpointReflection->getValue($sim));
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetSim::resetParameters()
     */
    public function testResetParameters()
    {
        $sim = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);

        $sequence = $sim->getSequence();
        $timestamp = $sim->getTimestamp();
        sleep(1);
        $sim->resetParameters();

        $reflectionOfRequest = new \ReflectionObject($sim);
        $timestampReflection = $reflectionOfRequest->getProperty('timestamp');
        $timestampReflection->setAccessible(true);
        $sequenceReflection = $reflectionOfRequest->getProperty('sequence');
        $sequenceReflection->setAccessible(true);

        $this->assertNotEquals($timestamp, $timestampReflection->getValue($sim));
        $this->assertNotEquals($timestamp, $sequenceReflection->getValue($sim));
    }
}