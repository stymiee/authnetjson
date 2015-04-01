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

class AuthnetJsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers            \JohnConde\Authnet\AuthnetJson::__set()
     * @expectedException \JohnConde\Authnet\AuthnetCannotSetParamsException
     */
    public function testExceptionIsRaisedForCannotSetParamsException()
    {
        $authnet = new AuthnetJson(null, null, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $authnet->login = 'test';
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJson::process()
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @expectedException \JohnConde\Authnet\AuthnetInvalidJsonException
     */
    public function testExceptionIsRaisedForInvalidJsonException()
    {
        $request = array(
            'customerProfileId' => '123456789'
        );

        $http = $this->getMockBuilder('\JohnConde\Authnet\CurlWrapper')
            ->disableOriginalConstructor()
            ->getMock();
        $http->expects($this->once())
            ->method('process')
            ->will($this->returnValue(''));

        $authnet = AuthnetApiFactory::getJsonApiHandler('asdcfvgbhn', 'asdcfvgbhn', AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $authnet->setProcessHandler($http);
        $authnet->deleteCustomerProfileRequest($request);
    }
}