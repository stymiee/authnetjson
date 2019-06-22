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

use PHPUnit\Framework\TestCase;

class AuthnetJsonResponseTest extends TestCase
{
    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::__set()
     * @covers            \JohnConde\Authnet\AuthnetCannotSetParamsException::__construct()
     */
    public function testExceptionIsRaisedForCannotSetParamsException() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetCannotSetParamsException');

        $request = new AuthnetJsonRequest('', '', AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->login = 'test';
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::__construct()
     * @covers            \JohnConde\Authnet\AuthnetInvalidJsonException::__construct()
     */
    public function testExceptionIsRaisedForInvalidJsonException() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetInvalidJsonException');

        $responseJson = 'I am invalid';
        new AuthnetJsonResponse($responseJson);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::isSuccessful()
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::isError()
     */
    public function testSuccessfulApiCall() : void
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
    public function testFailedApiCall() : void
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
    public function testTransactionResponse() : void
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
    public function testIsApproved() : void
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
    public function testIsDeclined() : void
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
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::isPrePaidCard()
     */
    public function testIsPrePaidCard() : void
    {
        $responseJson = '{
           "transactionResponse":{
              "responseCode":"1",
              "authCode":"LYTVH0",
              "avsResultCode":"Y",
              "cvvResultCode":"P",
              "cavvResultCode":"2",
              "transId":"40033638873",
              "refTransID":"",
              "transHash":"",
              "testRequest":"0",
              "accountNumber":"XXXX1111",
              "accountType":"Visa",
              "prePaidCard":{
                 "requestedAmount":"5.00",
                 "approvedAmount":"5.00",
                 "balanceOnCard":"1.23"
              },
              "messages":[
                 {
                    "code":"1",
                    "description":"This transaction has been approved."
                 }
              ],
              "transHashSha2":"5B69E7D68DE994D9A60A0F684BEBA11EE5C97DC22A45BEF70C558D0D9A3476597566EAB841A7B7A63F7768B3458C0E345BACE75AA97462220E16A6DC94F6361C",
              "SupplementalDataQualificationIndicator":0
           },
           "refId":"47222105",
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

        $this->assertTrue($response->isPrePaidCard());
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::__toString()
     */
    public function testToString() : void
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

        $this->assertStringContainsString('validationDirectResponse":"2,2,205,This transaction has been declined,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $string);
        $this->assertStringContainsString('28821903', $string);
        $this->assertStringContainsString('I00001', $string);
        $this->assertStringContainsString('Successful', $string);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::getRawResponse()
     */
    public function testGetRawResponse() : void
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
        $responseJson = json_encode(json_decode($responseJson, false));

        $response = new AuthnetJsonResponse($responseJson);
        $response = json_encode(json_decode($response->getRawResponse(), false));

        $this->assertSame($responseJson, $response);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::__get()
     */
    public function testGet() : void
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
     * @covers            \JohnConde\Authnet\AuthnetTransactionResponseCallException::__construct()
     */
    public function testExceptionIsRaisedForTransactionResponseCall() : void
    {
        $this->expectException('\JohnConde\Authnet\AuthnetTransactionResponseCallException');

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
    public function testCheckTransactionStatusCim() : void
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
    public function testCheckTransactionStatusAim() : void
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
    public function testGetErrorMethods() : void
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
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::getError()
     */
    public function testGetErrorTextAim() : void
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
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::getErrorMessage()
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::getError()
     */
    public function testGetErrorMessage() : void
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
