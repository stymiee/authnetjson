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

class UnitTestWrapperTest extends \PHPUnit_Framework_TestCase
{
    protected $wrapper;

    protected function setUp()
    {
        $this->wrapper = new UnitTestWrapper();
    }

    public function testResponse()
    {
        $json = '{test}';
        $this->wrapper->setResponse($json);
        $response = $this->wrapper->process(null, $json);

        $this->assertEquals('{test}', $response);
    }

    public function testGetName()
    {
        $this->assertEquals('JohnConde\Authnet\UnitTestWrapper', $this->wrapper->getName());
    }
}