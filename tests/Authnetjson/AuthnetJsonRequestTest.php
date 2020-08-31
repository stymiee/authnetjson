<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Authnetjson\tests;

use Authnetjson\AuthnetApiFactory;
use Authnetjson\AuthnetJsonRequest;
use Authnetjson\Exception\AuthnetCannotSetParamsException;
use Authnetjson\Exception\AuthnetCurlException;
use PHPUnit\Framework\TestCase;
use Curl\Curl;

class AuthnetJsonRequestTest extends TestCase
{
    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::__construct()
     */
    public function testConstructor(): void
    {
        $apiLogin    = 'apiLogin';
        $apiTransKey = 'apiTransKey';

        $request = AuthnetApiFactory::getJsonApiHandler($apiLogin, $apiTransKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);

        $reflectionOfRequest = new \ReflectionObject($request);
        $login = $reflectionOfRequest->getProperty('login');
        $login->setAccessible(true);
        $key = $reflectionOfRequest->getProperty('transactionKey');
        $key->setAccessible(true);

        self::assertEquals($login->getValue($request), $apiLogin);
        self::assertEquals($key->getValue($request), $apiTransKey);
    }


    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::__set()
     * @covers            \Authnetjson\Exception\AuthnetCannotSetParamsException::__construct()
     */
    public function testExceptionIsRaisedForCannotSetParamsException(): void
    {
        $this->expectException(AuthnetCannotSetParamsException::class);

        $request = new AuthnetJsonRequest('', '', AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->login = 'test';
    }


    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::process()
     * @covers            \Authnetjson\Exception\AuthnetCurlException::__construct()
     * @uses              \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses              \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testExceptionIsRaisedForInvalidJsonException(): void
    {
        $this->expectException(AuthnetCurlException::class);

        $requestJson = array(
            'customerProfileId' => '123456789'
        );

        $this->http = $this->getMockBuilder(Curl::class)
            ->getMock();
        $this->http->error = false;

        $request = AuthnetApiFactory::getJsonApiHandler('asdcfvgbhn', 'asdcfvgbhn', AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($this->http);
        $request->deleteCustomerProfileRequest($requestJson);
    }


    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::setProcessHandler()
     */
    public function testProcessorIsInstanceOfCurlWrapper(): void
    {
        $request = new AuthnetJsonRequest('', '', AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler(new Curl());

        $reflectionOfRequest = new \ReflectionObject($request);
        $processor = $reflectionOfRequest->getProperty('processor');
        $processor->setAccessible(true);

        self::assertInstanceOf(Curl::class, $processor->getValue($request));
    }


    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::__toString()
     * @covers            \Authnetjson\AuthnetJsonRequest::__call()
     */
    public function testToString(): void
    {
        $requestJson = array(
            'refId' => '94564789',
            'transactionRequest' => array(
                'transactionType' => 'authCaptureTransaction',
                'amount' => 5,
                'payment' => array(
                    'creditCard' => array(
                        'cardNumber' => '4111111111111111',
                        'expirationDate' => '122016',
                        'cardCode' => '999',
                    ),
                ),
                'order' => array(
                    'invoiceNumber' => '1324567890',
                    'description' => 'this is a test transaction',
                ),
                'lineItems' => array(
                    'lineItem' => array(
                        0 => array(
                            'itemId' => '1',
                            'name' => 'vase',
                            'description' => 'Cannes logo',
                            'quantity' => '18',
                            'unitPrice' => '45.00'
                        ),
                        1 => array(
                            'itemId' => '2',
                            'name' => 'desk',
                            'description' => 'Big Desk',
                            'quantity' => '10',
                            'unitPrice' => '85.00'
                        )
                    )
                ),
                'tax' => array(
                    'amount' => '4.26',
                    'name' => 'level2 tax name',
                    'description' => 'level2 tax',
                ),
                'duty' => array(
                    'amount' => '8.55',
                    'name' => 'duty name',
                    'description' => 'duty description',
                ),
                'shipping' => array(
                    'amount' => '4.26',
                    'name' => 'level2 tax name',
                    'description' => 'level2 tax',
                ),
                'poNumber' => '456654',
                'customer' => array(
                    'id' => '18',
                    'email' => 'someone@blackhole.tv',
                ),
                'billTo' => array(
                    'firstName' => 'Ellen',
                    'lastName' => 'Johnson',
                    'company' => 'Souveniropolis',
                    'address' => '14 Main Street',
                    'city' => 'Pecan Springs',
                    'state' => 'TX',
                    'zip' => '44628',
                    'country' => 'USA',
                ),
                'shipTo' => array(
                    'firstName' => 'China',
                    'lastName' => 'Bayles',
                    'company' => 'Thyme for Tea',
                    'address' => '12 Main Street',
                    'city' => 'Pecan Springs',
                    'state' => 'TX',
                    'zip' => '44628',
                    'country' => 'USA',
                ),
                'customerIP' => '192.168.1.1',
                'transactionSettings' => array(
                    'setting' => array(
                        0 => array(
                            'settingName' =>'allowPartialAuth',
                            'settingValue' => 'false'
                        ),
                        1 => array(
                            'settingName' => 'duplicateWindow',
                            'settingValue' => '0'
                        ),
                        2 => array(
                            'settingName' => 'emailCustomer',
                            'settingValue' => 'false'
                        ),
                        3 => array(
                            'settingName' => 'recurringBilling',
                            'settingValue' => 'false'
                        ),
                        4 => array(
                            'settingName' => 'testRequest',
                            'settingValue' => 'false'
                        )
                    )
                ),
                'userFields' => array(
                    0 => array(
                        'name' => 'MerchantDefinedFieldName1',
                        'value' => 'MerchantDefinedFieldValue1',
                    ),
                    1 => array(
                        'name' => 'favorite_color',
                        'value' => 'blue',
                    ),
                ),
            ),
        );
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

        $apiLogin    = 'apiLogin';
        $apiTransKey = 'apiTransKey';

        $http = $this->getMockBuilder(Curl::class)
            ->getMock();
        $http->error = false;
        $http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($apiLogin, $apiTransKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($http);
        $request->createTransactionRequest($requestJson);

        ob_start();
        echo $request;
        $string = ob_get_clean();

        self::assertStringContainsString($apiLogin, $string);
        self::assertStringContainsString($apiTransKey, $string);
    }

    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::getRawRequest()
     */
    public function testGetRawRequest(): void
    {
        $requestJson = array(
            'refId' => '94564789',
            'transactionRequest' => array(
                'transactionType' => 'authCaptureTransaction',
                'amount' => 5,
                'payment' => array(
                    'creditCard' => array(
                        'cardNumber' => '4111111111111111',
                        'expirationDate' => '122016',
                        'cardCode' => '999',
                    ),
                ),
            ),
        );

        $apiLogin    = 'apiLogin';
        $apiTransKey = 'apiTransKey';

        $http = $this->getMockBuilder(Curl::class)
            ->getMock();
        $http->error = false;
        $http->response = '{}';

        $request = AuthnetApiFactory::getJsonApiHandler($apiLogin, $apiTransKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($http);
        $request->deleteCustomerProfileRequest($requestJson);

        $response = '{"deleteCustomerProfileRequest":{"merchantAuthentication":{"name":"apiLogin","transactionKey":"apiTransKey"},"refId":"94564789","transactionRequest":{"transactionType":"authCaptureTransaction","amount":5,"payment":{"creditCard":{"cardNumber":"4111111111111111","expirationDate":"122016","cardCode":"999"}}}}}';
        self::assertSame($response, $request->getRawRequest());
    }

    /**
     * @covers \Authnetjson\AuthnetJsonRequest::process()
     * @covers \Authnetjson\Exception\AuthnetCurlException::__construct()
     */
    public function testProcessError(): void
    {
        $this->expectException(AuthnetCurlException::class);

        $apiLogin    = 'apiLogin';
        $apiTransKey = 'apiTransKey';

        $http = $this->getMockBuilder(Curl::class)
            ->getMock();
        $http->error         = true;
        $http->error_code    = 10;
        $http->error_message = 'Test Error Message';

        $request = AuthnetApiFactory::getJsonApiHandler($apiLogin, $apiTransKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($http);
        $request->deleteCustomerProfileRequest([]);
    }

    /**
     * @covers \Authnetjson\AuthnetJsonRequest::makeRequest()
     */
    public function testMakeRequestWithError(): void
    {
        $apiLogin    = 'apiLogin';
        $apiTransKey = 'apiTransKey';

        $http = $this->getMockBuilder(Curl::class)
            ->getMock();
        $http->error         = true;
        $http->error_code    = 10;
        $http->error_message = 'Test Error Message';

        $request = AuthnetApiFactory::getJsonApiHandler($apiLogin, $apiTransKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($http);

        $method = new \ReflectionMethod(AuthnetJsonRequest::class, 'makeRequest');
        $method->setAccessible(true);
        $method->invoke($request);

        $reflectionOfRequest = new \ReflectionObject($request);
        $retries = $reflectionOfRequest->getProperty('retries');
        $retries->setAccessible(true);

        $reflectionOfMaxRetries = new \ReflectionClassConstant(AuthnetJsonRequest::class, 'MAX_RETRIES');

        self::assertEquals($reflectionOfMaxRetries->getValue(), $retries->getValue($request));
    }

    /**
     * @covers \Authnetjson\AuthnetJsonRequest::makeRequest()
     */
    public function testMakeRequestWithNoError(): void
    {
        $apiLogin    = 'apiLogin';
        $apiTransKey = 'apiTransKey';

        $http = $this->getMockBuilder(Curl::class)
            ->getMock();
        $http->error         = false;

        $request = AuthnetApiFactory::getJsonApiHandler($apiLogin, $apiTransKey, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
        $request->setProcessHandler($http);

        $method = new \ReflectionMethod(AuthnetJsonRequest::class, 'makeRequest');
        $method->setAccessible(true);
        $method->invoke($request);

        $reflectionOfRequest = new \ReflectionObject($request);
        $retries = $reflectionOfRequest->getProperty('retries');
        $retries->setAccessible(true);
        self::assertEquals(0, $retries->getValue($request));
    }
}
