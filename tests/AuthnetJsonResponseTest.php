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
        $this->assertEquals('I00004', $response->messages->message[0]->code);
        $this->assertEquals('No records found.', $response->messages->message[0]->text);
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
        $this->assertEquals('E00027', $response->messages->message[0]->code);
        $this->assertEquals('The transaction was unsuccessful.', $response->messages->message[0]->text);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::getTransactionResponseField()
     * @covers            \JohnConde\Authnet\TransactionResponse::getTransactionResponseField()
     */
    public function testTransactionResponse()
    {
        $responseJson = '{
           "directResponse":"1,1,1,This transaction has been approved.,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
           "messages":{
              "resultCode":"Ok",
              "message":[
                 {
                    "code":"I00001",
                    "text":"Successful."
                 }
              ]
           }
        }';

        $response = new AuthnetJsonResponse($responseJson);

        $this->assertEquals('2230582306', $response->getTransactionResponseField('TransactionID'));
    }
}