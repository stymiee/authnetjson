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

class CurlWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testCurlWrapperGetName()
    {
        $wrapper = new CurlWrapper();

        $this->assertEquals('JohnConde\Authnet\CurlWrapper', $wrapper->getName());
    }
}