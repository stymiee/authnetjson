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

class AuthnetJsonRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::__set()
     * @expectedException \JohnConde\Authnet\AuthnetCannotSetParamsException
     */
    public function testExceptionIsRaisedForCannotSetParamsException()
    {
        $request = new AuthnetJsonRequest(null, null, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->login = 'test';
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     * @expectedException \JohnConde\Authnet\AuthnetInvalidJsonException
     */
    public function testExceptionIsRaisedForInvalidJsonException()
    {
        $requestJson = array(
            'customerProfileId' => '123456789'
        );

        $http = $this->getMockBuilder('\JohnConde\Authnet\CurlWrapper')
            ->disableOriginalConstructor()
            ->getMock();
        $http->expects($this->once())
            ->method('process')
            ->will($this->returnValue(''));

        $request = AuthnetApiFactory::getJsonApiHandler('asdcfvgbhn', 'asdcfvgbhn', AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($http);
        $request->deleteCustomerProfileRequest($requestJson);
    }
}