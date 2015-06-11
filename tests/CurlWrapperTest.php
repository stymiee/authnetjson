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
    protected function setUp()
    {
        $this->http = $this->getMockBuilder('\JohnConde\Authnet\CurlWrapper')
            ->setMethods(array('makeRequest'))
            ->getMock();
    }

    /**
     * @uses              \JohnConde\Authnet\CurlWrapper::makeRequest()
     * @covers            \JohnConde\Authnet\CurlWrapper::process()
     */
    public function testCurlWrapperMakeRequest()
    {
        $url  = 'http://www.example.com';
        $json = '{}';

        $this->http->method('makeRequest')
            ->will($this->returnValue($json));

        $response = $this->http->process($url, $json);

        $this->assertEquals($json, $response);
    }

    /**
     * @uses              \JohnConde\Authnet\CurlWrapper::makeRequest()
     * @covers            \JohnConde\Authnet\CurlWrapper::process()
     * @expectedException \JohnConde\Authnet\AuthnetCurlException
     */
    public function testCurlWrapperMakeRequestException()
    {
        $url  = 'http://www.example.com';
        $json = false;

        $this->http->method('makeRequest')
            ->willReturn($json);

        $this->http->process($url, $json);
    }
}