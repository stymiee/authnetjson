<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnConde\Authnet\tests;

use JohnConde\Authnet\AuthnetApiFactory;
use JohnConde\Authnet\AuthnetJsonRequest;
use JohnConde\Authnet\AuthnetJsonResponse;
use JohnConde\Authnet\Exception\AuthnetCannotSetParamsException;
use JohnConde\Authnet\Exception\AuthnetInvalidJsonException;
use JohnConde\Authnet\Exception\AuthnetTransactionResponseCallException;
use JohnConde\Authnet\TransactionResponse;
use Curl\Curl;
use PHPUnit\Framework\TestCase;

class AuthnetJsonResponseTest extends TestCase
{
    /**
     * @covers \JohnConde\Authnet\AuthnetJsonRequest::__set()
     * @covers \JohnConde\Authnet\Exception\AuthnetCannotSetParamsException::__construct()
     */
    public function testExceptionIsRaisedForCannotSetParamsException()
    {
        $this->expectException(AuthnetCannotSetParamsException::class);

        $request = new AuthnetJsonRequest('', '', AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->login = 'test';
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::__construct()
     * @covers \JohnConde\Authnet\Exception\AuthnetInvalidJsonException::__construct()
     */
    public function testExceptionIsRaisedForInvalidJsonException()
    {
        $this->expectException(AuthnetInvalidJsonException::class);

        $responseJson = 'I am invalid';
        new AuthnetJsonResponse($responseJson);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::isSuccessful()
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::isError()
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

        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isError());
        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertEquals('I00004', $response->messages->message[0]->code);
        self::assertEquals('No records found.', $response->messages->message[0]->text);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::isSuccessful()
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::isError()
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

        self::assertFalse($response->isSuccessful());
        self::assertTrue($response->isError());
        self::assertEquals('Error', $response->messages->resultCode);
        self::assertEquals('E00027', $response->messages->message[0]->code);
        self::assertEquals('The transaction was unsuccessful.', $response->messages->message[0]->text);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::getTransactionResponseField()
     * @covers \JohnConde\Authnet\TransactionResponse::getTransactionResponseField()
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

        self::assertEquals('2230582306', $response->getTransactionResponseField('TransactionID'));
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::getTransactionResponseField()
     * @covers \JohnConde\Authnet\Exception\AuthnetTransactionResponseCallException::__construct()
     */
    public function testTransactionResponseException()
    {
        $this->expectException(AuthnetTransactionResponseCallException::class);

        $AuthnetJsonResponse = new AuthnetJsonResponse('{}');
        $method = new \ReflectionMethod($AuthnetJsonResponse, 'getTransactionResponseField');
        $method->invoke($AuthnetJsonResponse, 'test');
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::isApproved()
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

        self::assertEquals(AuthnetJsonResponse::STATUS_APPROVED, $response->getTransactionResponseField('ResponseCode'));
        self::assertTrue($response->isApproved());
    }


    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::isDeclined()
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

        self::assertEquals(AuthnetJsonResponse::STATUS_DECLINED, $response->getTransactionResponseField('ResponseCode'));
        self::assertTrue($response->isDeclined());
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::isPrePaidCard()
     */
    public function testIsPrePaidCard()
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

        self::assertTrue($response->isPrePaidCard());
    }


    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::__toString()
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

        self::assertTrue(stripos($string, 'validationDirectResponse":"2,2,205,This transaction has been declined,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174') !== false);
        self::assertTrue(stripos($string, '28821903') !== false);
        self::assertTrue(stripos($string, 'I00001') !== false);
        self::assertTrue(stripos($string, 'Successful') !== false);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::getRawResponse()
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
        $responseJson = json_encode(json_decode($responseJson, false));

        $response = new AuthnetJsonResponse($responseJson);
        $response = json_encode(json_decode($response->getRawResponse(), false));

        self::assertSame($responseJson, $response);
    }


    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::__get()
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

        self::assertEquals(28821903, $response->customerPaymentProfileId);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::__set()
     */
    public function testSet()
    {
        $this->expectException(AuthnetCannotSetParamsException::class);

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
        $response->test = 'test';
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::__isset()
     */
    public function testIsset()
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
        $this->assertTrue(isset($response->refId));
        $this->assertFalse(isset($response->test));
    }

    /**
     * @covers \JohnConde\Authnet\Exception\AuthnetTransactionResponseCallException::__construct()
     */
    public function testExceptionIsRaisedForTransactionResponseCall()
    {
        $this->expectException(AuthnetTransactionResponseCallException::class);

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
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::checkTransactionStatus()
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

        self::assertTrue($match);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::checkTransactionStatus()
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

        self::assertTrue($match);
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::getErrorText()
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::getErrorCode()
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

        self::assertTrue($response->isError());
        self::assertEquals('E00027', $response->getErrorCode());
        self::assertEquals('The transaction was unsuccessful.', $response->getErrorText());
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::getErrorText()
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::getErrorCode()
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::getError()
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

        self::assertTrue($response->isError());
        self::assertEquals('11', $response->getErrorCode());
        self::assertEquals('A duplicate transaction has been submitted.', $response->getErrorText());
    }

    /**
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::getErrorMessage()
     * @covers \JohnConde\Authnet\AuthnetJsonResponse::getError()
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

        self::assertEquals($response->getErrorText(), $response->getErrorMessage());
    }

    public function testGetTransactionResponses()
    {
        $http = $this->getMockBuilder(Curl::class)
            ->getMock();
        $http->error = false;

        $requestJson = array(
            'profile' => array(
                'merchantCustomerId' => '12345',
                'email' => 'user@example.com',
                'paymentProfiles' => array(
                    'billTo' => array(
                        'firstName' => 'John',
                        'lastName' => 'Smith',
                        'address' => '123 Main Street',
                        'city' => 'Townsville',
                        'state' => 'NJ',
                        'zip' => '12345',
                        'phoneNumber' => '800-555-1234'
                    ),
                    'payment' => array(
                        'creditCard' => array(
                            'cardNumber' => '4111111111111111',
                            'expirationDate' => '2016-08',
                        ),
                    ),
                ),
                'shipToList' => array(
                    'firstName' => 'John',
                    'lastName' => 'Smith',
                    'address' => '123 Main Street',
                    'city' => 'Townsville',
                    'state' => 'NJ',
                    'zip' => '12345',
                    'phoneNumber' => '800-555-1234'
                ),
            ),
            'validationMode' => 'liveMode'
        );
        $transactionInfoArray = '"1,1,1,This transaction has been approved.,1VQHEI,Y,2228580111,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,,,,,,,,,0.00,0.00,0.00,FALSE,none,317FCDBBCBABB2C7442766267D4C099C,,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,"';
        $responseJson = '{
           "customerProfileId":"31390172",
           "customerPaymentProfileIdList":[
              "28393490"
           ],
           "customerShippingAddressIdList":[
              "29366174"
           ],
           "validationDirectResponseList":[
               '. $transactionInfoArray . '
           ],
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

        $http->response = $responseJson;
        $request = AuthnetApiFactory::getJsonApiHandler('test', 'test', AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($http);
        $response = $request->createCustomerProfileRequest($requestJson);

        $transactionResponses = $response->getTransactionResponses();
        self::assertTrue(is_array($transactionResponses));
        self::assertInstanceOf(TransactionResponse::class, $transactionResponses[0]);
    }
}
