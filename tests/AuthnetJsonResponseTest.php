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
        new AuthnetJsonResponse($responseJson);
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


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::isApproved()
     */
    public function testIsApproved()
    {
        $responseJson = '{
           "customerPaymentProfileId":"28821903",
           "validationDirectResponse":"1,1,1,This transaction has been approved.,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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

        $this->assertEquals(AuthnetJsonResponse::STATUS_APPROVED, $response->getTransactionResponseField('ResponseCode'));
        $this->assertTrue($response->isApproved());
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::isDeclined()
     */
    public function testIsDeclined()
    {
        $responseJson = '{
           "customerPaymentProfileId":"28821903",
           "validationDirectResponse":"2,2,205,This transaction has been declined,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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

        $this->assertEquals(AuthnetJsonResponse::STATUS_DECLINED, $response->getTransactionResponseField('ResponseCode'));
        $this->assertTrue($response->isDeclined());
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::__toString()
     */
    public function testToString()
    {
        $responseJson = '{
           "customerPaymentProfileId":"28821903",
           "validationDirectResponse":"2,2,205,This transaction has been declined,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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

        ob_start();
        echo $response;
        $string = ob_get_clean();

        $this->assertContains('validationDirectResponse":"2,2,205,This transaction has been declined,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $string);
        $this->assertContains('28821903', $string);
        $this->assertContains('I00001', $string);
        $this->assertContains('Successful', $string);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::getRawResponse()
     */
    public function testGetRawResponse()
    {
        $responseJson = '{
           "customerPaymentProfileId":"28821903",
           "validationDirectResponse":"2,2,205,This transaction has been declined,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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

        $this->assertSame(str_replace("\r\n", '', $responseJson), $response->getRawResponse());
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::__get()
     */
    public function testGet()
    {
        $responseJson = '{
           "customerPaymentProfileId":"28821903",
           "validationDirectResponse":"2,2,205,This transaction has been declined,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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

        $this->assertEquals(28821903, $response->customerPaymentProfileId);
    }


    /**
     * @expectedException \JohnConde\Authnet\AuthnetTransactionResponseCallException
     */
    public function testExceptionIsRaisedForTransactionResponseCall()
    {
        $responseJson = '{
           "refId":"2241729",
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
        $response->getTransactionResponseField('ResponseCode');
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::checkTransactionStatus()
     */
    public function testCheckTransactionStatusCim()
    {
        $responseJson = '{
           "customerPaymentProfileId":"28821903",
           "validationDirectResponse":"2,2,205,This transaction has been declined,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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
        $reflectionMethod = new \ReflectionMethod($response, 'checkTransactionStatus');
        $reflectionMethod->setAccessible(true);
        $match    = $reflectionMethod->invoke($response, AuthnetJsonResponse::STATUS_DECLINED);

        $this->assertTrue($match);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::checkTransactionStatus()
     */
    public function testCheckTransactionStatusAim()
    {
        $responseJson = '{
           "transactionResponse":{
              "responseCode":"1",
              "authCode":"QWX20S",
              "avsResultCode":"Y",
              "cvvResultCode":"P",
              "cavvResultCode":"2",
              "transId":"2228446239",
              "refTransID":"",
              "transHash":"56B2D50D73CAB8C6EDE7A92B9BB235BD",
              "testRequest":"0",
              "accountNumber":"XXXX1111",
              "accountType":"Visa",
              "messages":[
                 {
                    "code":"1",
                    "description":"This transaction has been approved."
                 }
              ],
              "userFields":[
                 {
                    "name":"favorite_color",
                    "value":"blue"
                 }
              ]
           },
           "refId":"94564789",
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
        $reflectionMethod = new \ReflectionMethod($response, 'checkTransactionStatus');
        $reflectionMethod->setAccessible(true);
        $match    = $reflectionMethod->invoke($response, AuthnetJsonResponse::STATUS_APPROVED);

        $this->assertTrue($match);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::getErrorText()
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::getErrorCode()
     */
    public function testGetErrorMethods()
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

        $this->assertTrue($response->isError());
        $this->assertEquals('E00027', $response->getErrorCode());
        $this->assertEquals('The transaction was unsuccessful.', $response->getErrorText());
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::getErrorText()
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::getErrorCode()
     */
    public function testGetErrorTextAim()
    {
        $responseJson = '{
           "transactionResponse":{
              "responseCode":"3",
              "authCode":"",
              "avsResultCode":"P",
              "cvvResultCode":"",
              "cavvResultCode":"",
              "transId":"0",
              "refTransID":"",
              "transHash":"9F18DE7ABDD09076F9BADB594EFC4611",
              "testRequest":"0",
              "accountNumber":"XXXX0015",
              "accountType":"MasterCard",
              "errors":[
                 {
                    "errorCode":"11",
                    "errorText":"A duplicate transaction has been submitted."
                 }
              ],
              "userFields":[
                 {
                    "name":"favorite_color",
                    "value":"blue"
                 }
              ]
           },
           "refId":"14290435",
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

        $this->assertTrue($response->isError());
        $this->assertEquals('11', $response->getErrorCode());
        $this->assertEquals('A duplicate transaction has been submitted.', $response->getErrorText());
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::getErrorMessage()
     */
    public function testGetErrorMessage()
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

        $this->assertEquals($response->getErrorText(), $response->getErrorMessage());
    }
}