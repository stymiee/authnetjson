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

class AuthnetJsonResponseTest extends \PHPUnit_Framework_TestCase
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
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::__construct()
     * @expectedException \JohnConde\Authnet\AuthnetInvalidJsonException
     */
    public function testExceptionIsRaisedForInvalidJsonException()
    {
        $responseJson = 'I am invalid';
        $response = new AuthnetJsonResponse($responseJson);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::isSuccessful()
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::isError()
     */
    public function testSuccessfulApiCall()
    {
        $responseJson = '{
           "messages":{
              "resultCode":"Ok",
              "message":[
                 {
                    "code":"I00004",
                    "text":"No records found."
                 }
              ]
           }
        }';

        $response = new AuthnetJsonResponse($responseJson);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertEquals('I00004', $response->transactionResponse->messages[0]->code);
        $this->assertEquals('No records found.', $response->transactionResponse->messages[0]->text);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::isSuccessful()
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::isError()
     */
    public function testFailedApiCall()
    {
        $responseJson = '{
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00027",
                    "text":"The transaction was unsuccessful."
                 }
              ]
           }
        }';

        $response = new AuthnetJsonResponse($responseJson);

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertEquals('E00027', $response->transactionResponse->messages[0]->code);
        $this->assertEquals('The transaction was unsuccessful.', $response->transactionResponse->messages[0]->text);
    }
}