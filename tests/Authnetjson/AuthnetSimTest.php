<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Authnetjson;

use PHPUnit\Framework\TestCase;

class AuthnetSimTest extends TestCase
{
    private $login;
    private $transactionKey;
    private $signature;
    private $server;

    protected function setUp() : void
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
        $this->signature      = '546C62A5B61434BCE2FA4C8EC86E4B85FB2AC34957C894C89927800F878EA20154A0903FA3A5B3DD1C219053789874F4F96E88850CDC246F049119F6AB71CD21';
        $this->server         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;
    }

    /**
     * @covers            \Authnetjson\AuthnetSim::__construct()
     */
    public function testConstructor() : void
    {
        $request = AuthnetApiFactory::getSimHandler($this->login, $this->signature, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);

        $reflectionOfSim = new \ReflectionObject($request);
        $login = $reflectionOfSim->getProperty('login');
        $login->setAccessible(true);
        $key = $reflectionOfSim->getProperty('signature');
        $key->setAccessible(true);

        self::assertEquals($login->getValue($request), $this->login);
        self::assertEquals($key->getValue($request), $this->signature);
    }

    /**
     * @covers            \Authnetjson\AuthnetSim::getFingerprint()
     */
    public function testGetFingerprint() : void
    {
        $amount    = 9.01;

        $sim       = AuthnetApiFactory::getSimHandler($this->login, $this->signature, $this->server);
        $hash      = $sim->getFingerprint($amount);
        $sequence  = $sim->getSequence();
        $timestamp = $sim->getTimestamp();

        self::assertEquals($hash, strtoupper(hash_hmac('sha512', sprintf('%s^%s^%s^%s^',
            $this->login,
            $sequence,
            $timestamp,
            $amount
        ), hex2bin($this->signature))));
    }

    /**
     * @covers            \Authnetjson\AuthnetSim::getFingerprint()
     * @covers            \Authnetjson\AuthnetInvalidAmountException::__construct()
     */
    public function testGetFingerprintException() : void
    {
        $this->expectException(AuthnetInvalidAmountException::class);

        $amount    = 0;
        $sim       = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);
        $hash      = $sim->getFingerprint($amount);
    }

    /**
     * @covers            \Authnetjson\AuthnetSim::getSequence()
     */
    public function testGetSequence() : void
    {
        $sim = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);
        $sequence = $sim->getSequence();

        $reflectionOfRequest = new \ReflectionObject($sim);
        $sequenceReflection = $reflectionOfRequest->getProperty('sequence');
        $sequenceReflection->setAccessible(true);

        self::assertEquals($sequence, $sequenceReflection->getValue($sim));
    }

    /**
     * @covers            \Authnetjson\AuthnetSim::getTimestamp()
     */
    public function testGetTimestamp() : void
    {
        $sim = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);
        $timestamp = $sim->getTimestamp();

        $reflectionOfRequest = new \ReflectionObject($sim);
        $timestampReflection = $reflectionOfRequest->getProperty('timestamp');
        $timestampReflection->setAccessible(true);

        self::assertEquals($timestamp, $timestampReflection->getValue($sim));
    }

    /**
     * @covers            \Authnetjson\AuthnetSim::getLogin()
     */
    public function testGetLogin() : void
    {
        $sim   = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);
        $login = $sim->getLogin();

        $reflectionOfRequest = new \ReflectionObject($sim);
        $loginReflection = $reflectionOfRequest->getProperty('login');
        $loginReflection->setAccessible(true);

        self::assertEquals($login, $loginReflection->getValue($sim));
    }

    /**
     * @covers            \Authnetjson\AuthnetSim::getEndpoint()
     */
    public function testGetEndpoint() : void
    {
        $sim = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);
        $url = $sim->getEndpoint();

        $reflectionOfRequest = new \ReflectionObject($sim);
        $endpointReflection = $reflectionOfRequest->getProperty('url');
        $endpointReflection->setAccessible(true);

        self::assertEquals($url, $endpointReflection->getValue($sim));
    }

    /**
     * @covers            \Authnetjson\AuthnetSim::resetParameters()
     */
    public function testResetParameters() : void
    {
        $sim = AuthnetApiFactory::getSimHandler($this->login, $this->transactionKey, $this->server);

        $timestamp = $sim->getTimestamp();
        sleep(1);
        $sim->resetParameters();

        $reflectionOfRequest = new \ReflectionObject($sim);
        $timestampReflection = $reflectionOfRequest->getProperty('timestamp');
        $timestampReflection->setAccessible(true);
        $sequenceReflection = $reflectionOfRequest->getProperty('sequence');
        $sequenceReflection->setAccessible(true);

        self::assertNotEquals($timestamp, $timestampReflection->getValue($sim));
        self::assertNotEquals($timestamp, $sequenceReflection->getValue($sim));
    }
}
