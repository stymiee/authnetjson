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

class AuthnetJsonAimTest extends \PHPUnit_Framework_TestCase
{
    private $login;
    private $transactionKey;
    private $server;
    private $http;

    protected function setUp()
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
        $this->server         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;

        $this->http = $this->getMockBuilder('\JohnConde\Authnet\CurlWrapper')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestAuthCaptureSuccess()
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
                    'userField' => array(
                        'name' => 'MerchantDefinedFieldName1',
                        'value' => 'MerchantDefinedFieldValue1',
                    ),
                    'userField' => array(
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

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertEquals('This transaction has been approved.', $response->transactionResponse->messages[0]->description);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('QWX20S', $response->transactionResponse->authCode);
        $this->assertEquals('2228446239', $response->transactionResponse->transId);
        $this->assertEquals('1', $response->transactionResponse->responseCode);
        $this->assertEquals('Y', $response->transactionResponse->avsResultCode);
        $this->assertEquals('P', $response->transactionResponse->cvvResultCode);
        $this->assertEquals('2', $response->transactionResponse->cavvResultCode);
        $this->assertEquals('56B2D50D73CAB8C6EDE7A92B9BB235BD', $response->transactionResponse->transHash);
        $this->assertEquals('0', $response->transactionResponse->testRequest);
        $this->assertEquals('XXXX1111', $response->transactionResponse->accountNumber);
        $this->assertEquals('Visa', $response->transactionResponse->accountType);
        $this->assertEquals('1', $response->transactionResponse->messages[0]->code);
        $this->assertEquals('favorite_color', $response->transactionResponse->userFields[0]->name);
        $this->assertEquals('blue', $response->transactionResponse->userFields[0]->value);
        $this->assertEquals('94564789', $response->refId);
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestAuthOnlySuccess()
    {
        $requestJson = array(
            'refId' => '65376587',
            'transactionRequest' => array(
                'transactionType' => 'authOnlyTransaction',
                'amount' => 5,
                'payment' => array(
                    'creditCard' => array(
                        'cardNumber' => '5424000000000015',
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
                        'itemId' => '1',
                        'name' => 'vase',
                        'description' => 'Cannes logo',
                        'quantity' => '18',
                        'unitPrice' => '45.00',
                    ),
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
                        'settingName' => 'allowPartialAuth',
                        'settingValue' => 'false',
                    ),
                    'setting' => array(
                        'settingName' => 'duplicateWindow',
                        'settingValue' => '0',
                    ),
                    'setting' => array(
                        'settingName' => 'emailCustomer',
                        'settingValue' => 'false',
                    ),
                    'setting' => array(
                        'settingName' => 'recurringBilling',
                        'settingValue' => 'false',
                    ),
                    'setting' => array(
                        'settingName' => 'testRequest',
                        'settingValue' => 'false',
                    ),
                ),
                'userFields' => array(
                    'userField' => array(
                        'name' => 'MerchantDefinedFieldName1',
                        'value' => 'MerchantDefinedFieldValue1',
                    ),
                    'userField' => array(
                        'name' => 'favorite_color',
                        'value' => 'blue',
                    ),
                ),
            ),
        );
        $responseJson = '{
           "transactionResponse":{
              "responseCode":"1",
              "authCode":"7M6LIT",
              "avsResultCode":"Y",
              "cvvResultCode":"P",
              "cavvResultCode":"2",
              "transId":"2228545782",
              "refTransID":"",
              "transHash":"6210B3AEC49FC269036D42F9681459A9",
              "testRequest":"0",
              "accountNumber":"XXXX0015",
              "accountType":"MasterCard",
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
           "refId":"65376587",
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

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertEquals('This transaction has been approved.', $response->transactionResponse->messages[0]->description);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('7M6LIT', $response->transactionResponse->authCode);
        $this->assertEquals('2228545782', $response->transactionResponse->transId);
        $this->assertEquals('1', $response->transactionResponse->responseCode);
        $this->assertEquals('Y', $response->transactionResponse->avsResultCode);
        $this->assertEquals('P', $response->transactionResponse->cvvResultCode);
        $this->assertEquals('2', $response->transactionResponse->cavvResultCode);
        $this->assertEquals('6210B3AEC49FC269036D42F9681459A9', $response->transactionResponse->transHash);
        $this->assertEquals('0', $response->transactionResponse->testRequest);
        $this->assertEquals('XXXX0015', $response->transactionResponse->accountNumber);
        $this->assertEquals('MasterCard', $response->transactionResponse->accountType);
        $this->assertEquals('1', $response->transactionResponse->messages[0]->code);
        $this->assertEquals('favorite_color', $response->transactionResponse->userFields[0]->name);
        $this->assertEquals('blue', $response->transactionResponse->userFields[0]->value);
        $this->assertEquals('65376587', $response->refId);
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestAuthOnlyError()
    {
        $requestJson = array(
            'refId' => '14290435',
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
                            'settingName' => 'allowPartialAuth',
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
                    'userField' => array(
                        'name' => 'MerchantDefinedFieldName1',
                        'value' => 'MerchantDefinedFieldValue1',
                    ),
                    'userField' => array(
                        'name' => 'favorite_color',
                        'value' => 'blue',
                    ),
                ),
            ),
        );
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

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('3', $response->transactionResponse->responseCode);
        $this->assertEquals('P', $response->transactionResponse->avsResultCode);
        $this->assertEquals('0', $response->transactionResponse->transId);
        $this->assertEquals('0', $response->transactionResponse->testRequest);
        $this->assertEquals('9F18DE7ABDD09076F9BADB594EFC4611', $response->transactionResponse->transHash);
        $this->assertEquals('XXXX0015', $response->transactionResponse->accountNumber);
        $this->assertEquals('MasterCard', $response->transactionResponse->accountType);
        $this->assertEmpty($response->transactionResponse->authCode);
        $this->assertEmpty($response->transactionResponse->cvvResultCode);
        $this->assertEmpty($response->transactionResponse->cavvResultCode);
        $this->assertEmpty($response->transactionResponse->refTransID);
        $this->assertEquals('11', $response->transactionResponse->errors[0]->errorCode);
        $this->assertEquals('A duplicate transaction has been submitted.', $response->transactionResponse->errors[0]->errorText);
        $this->assertEquals('favorite_color', $response->transactionResponse->userFields[0]->name);
        $this->assertEquals('blue', $response->transactionResponse->userFields[0]->value);
        $this->assertEquals('14290435', $response->refId);
        $this->assertEquals('E00027', $response->messages->message[0]->code);
        $this->assertEquals('The transaction was unsuccessful.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestCaptureOnly()
    {
        $requestJson = array(
            'refId' => '99120820',
            'transactionRequest' => array(
                'transactionType' => 'captureOnlyTransaction',
                'amount' => 5,
                'payment' => array(
                    'creditCard' => array(
                        'cardNumber' => '5424000000000015',
                        'expirationDate' => '122016'
                    )
                ),
                'authCode' => '123456'
            ),
        );
        $responseJson = '{
           "transactionResponse":{
              "responseCode":"1",
              "authCode":"123456",
              "avsResultCode":"P",
              "cvvResultCode":"",
              "cavvResultCode":"",
              "transId":"2230581248",
              "refTransID":"",
              "transHash":"6636DE0003D951E48B05DC0AAB0FD633",
              "testRequest":"0",
              "accountNumber":"XXXX0015",
              "accountType":"MasterCard",
              "messages":[
                 {
                    "code":"1",
                    "description":"This transaction has been approved."
                 }
              ]
           },
           "refId":"99120820",
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

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestPriorAuthCapture()
    {
        $requestJson = array(
            'refId' => '34913421',
            'transactionRequest' => array(
                'transactionType' => 'priorAuthCaptureTransaction',
                'refTransId' => '2230581333'
            ),
        );
        $responseJson = '{
           "transactionResponse":{
              "responseCode":"1",
              "authCode":"1VT65S",
              "avsResultCode":"P",
              "cvvResultCode":"",
              "cavvResultCode":"",
              "transId":"2230581333",
              "refTransID":"2230581333",
              "transHash":"414220CECDB539F68435A4830246BDA5",
              "testRequest":"0",
              "accountNumber":"XXXX0015",
              "accountType":"MasterCard",
              "messages":[
                 {
                    "code":"1",
                    "description":"This transaction has been approved."
                 }
              ]
           },
           "refId":"34913421",
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

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('1VT65S', $response->transactionResponse->authCode);
        $this->assertEquals('2230581333', $response->transactionResponse->transId);
        $this->assertEquals('1', $response->transactionResponse->responseCode);
        $this->assertEquals('P', $response->transactionResponse->avsResultCode);
        $this->assertEquals('', $response->transactionResponse->cvvResultCode);
        $this->assertEquals('', $response->transactionResponse->cavvResultCode);
        $this->assertEquals('414220CECDB539F68435A4830246BDA5', $response->transactionResponse->transHash);
        $this->assertEquals('0', $response->transactionResponse->testRequest);
        $this->assertEquals('XXXX0015', $response->transactionResponse->accountNumber);
        $this->assertEquals('MasterCard', $response->transactionResponse->accountType);
        $this->assertEquals('1', $response->transactionResponse->messages[0]->code);
        $this->assertEquals('34913421', $response->refId);
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestPriorAuthCaptureError()
    {
        $requestJson = array(
            'refId' => '14254181',
            'transactionRequest' => array(
                'transactionType' => 'priorAuthCaptureTransaction',
                'refTransId' => '2165665234'
            ),
        );
        $responseJson = '{
           "transactionResponse":{
              "responseCode":"3",
              "authCode":"",
              "avsResultCode":"P",
              "cvvResultCode":"",
              "cavvResultCode":"",
              "transId":"0",
              "refTransID":"2165665234",
              "transHash":"06D737C28ECD531129DC59EF0548D7FA",
              "testRequest":"0",
              "accountNumber":"",
              "accountType":"MasterCard",
              "errors":[
                 {
                    "errorCode":"16",
                    "errorText":"The transaction cannot be found."
                 }
              ]
           },
           "refId":"14254181",
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

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('', $response->transactionResponse->authCode);
        $this->assertEquals('2165665234', $response->transactionResponse->refTransID);
        $this->assertEquals('3', $response->transactionResponse->responseCode);
        $this->assertEquals('P', $response->transactionResponse->avsResultCode);
        $this->assertEquals('', $response->transactionResponse->cvvResultCode);
        $this->assertEquals('', $response->transactionResponse->cavvResultCode);
        $this->assertEquals('06D737C28ECD531129DC59EF0548D7FA', $response->transactionResponse->transHash);
        $this->assertEquals('0', $response->transactionResponse->testRequest);
        $this->assertEquals('', $response->transactionResponse->accountNumber);
        $this->assertEquals('MasterCard', $response->transactionResponse->accountType);
        $this->assertEquals('16', $response->transactionResponse->errors[0]->errorCode);
        $this->assertEquals('The transaction cannot be found.', $response->transactionResponse->errors[0]->errorText);
        $this->assertEquals('14254181', $response->refId);
        $this->assertEquals('E00027', $response->messages->message[0]->code);
        $this->assertEquals('The transaction was unsuccessful.', $response->messages->message[0]->text);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestRefund()
    {
        $requestJson = array(
            'refId' => '95063294',
            'transactionRequest' => array(
                'transactionType' => 'refundTransaction',
                'amount' => 5,
                'payment' => array(
                    'creditCard' => array(
                        'cardNumber' => '4111111111111111',
                        'expirationDate' => '122016',
                    )
                ),
                'authCode' => '2165668159'
            ),
        );
        $responseJson = '{
           "transactionResponse":{
              "responseCode":"1",
              "authCode":"",
              "avsResultCode":"P",
              "cvvResultCode":"",
              "cavvResultCode":"",
              "transId":"2230581367",
              "refTransID":"",
              "transHash":"E659A47D6DCC71D618533E17A80E818A",
              "testRequest":"0",
              "accountNumber":"XXXX1111",
              "accountType":"Visa",
              "messages":[
                 {
                    "code":"1",
                    "description":"This transaction has been approved."
                 }
              ]
           },
           "refId":"95063294",
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

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('', $response->transactionResponse->authCode);
        $this->assertEquals('2230581367', $response->transactionResponse->transId);
        $this->assertEquals('', $response->transactionResponse->refTransID);
        $this->assertEquals('1', $response->transactionResponse->responseCode);
        $this->assertEquals('P', $response->transactionResponse->avsResultCode);
        $this->assertEquals('', $response->transactionResponse->cvvResultCode);
        $this->assertEquals('', $response->transactionResponse->cavvResultCode);
        $this->assertEquals('E659A47D6DCC71D618533E17A80E818A', $response->transactionResponse->transHash);
        $this->assertEquals('0', $response->transactionResponse->testRequest);
        $this->assertEquals('XXXX1111', $response->transactionResponse->accountNumber);
        $this->assertEquals('Visa', $response->transactionResponse->accountType);
        $this->assertEquals('1', $response->transactionResponse->messages[0]->code);
        $this->assertEquals('This transaction has been approved.', $response->transactionResponse->messages[0]->description);
        $this->assertEquals('95063294', $response->refId);
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestVoid()
    {
        $requestJson = array(
            'refId' => '35481415',
            'transactionRequest' => array(
                'transactionType' => 'voidTransaction',
                'refTransId' => '2230581408'
            ),
        );
        $responseJson = '{
           "transactionResponse":{
              "responseCode":"1",
              "authCode":"QCG7TB",
              "avsResultCode":"P",
              "cvvResultCode":"",
              "cavvResultCode":"",
              "transId":"2230581408",
              "refTransID":"2230581408",
              "transHash":"39C62AE0EC82B749CC6D3324BE263CC6",
              "testRequest":"0",
              "accountNumber":"XXXX1111",
              "accountType":"Visa",
              "messages":[
                 {
                    "code":"1",
                    "description":"This transaction has been approved."
                 }
              ]
           },
           "refId":"35481415",
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

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('QCG7TB', $response->transactionResponse->authCode);
        $this->assertEquals('2230581408', $response->transactionResponse->transId);
        $this->assertEquals('2230581408', $response->transactionResponse->refTransID);
        $this->assertEquals('1', $response->transactionResponse->responseCode);
        $this->assertEquals('P', $response->transactionResponse->avsResultCode);
        $this->assertEquals('', $response->transactionResponse->cvvResultCode);
        $this->assertEquals('', $response->transactionResponse->cavvResultCode);
        $this->assertEquals('39C62AE0EC82B749CC6D3324BE263CC6', $response->transactionResponse->transHash);
        $this->assertEquals('0', $response->transactionResponse->testRequest);
        $this->assertEquals('XXXX1111', $response->transactionResponse->accountNumber);
        $this->assertEquals('Visa', $response->transactionResponse->accountType);
        $this->assertEquals('1', $response->transactionResponse->messages[0]->code);
        $this->assertEquals('This transaction has been approved.', $response->transactionResponse->messages[0]->description);
        $this->assertEquals('35481415', $response->refId);
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestVoidError()
    {
        $requestJson = array(
            'refId' => '23039947',
            'transactionRequest' => array(
                'transactionType' => 'voidTransaction',
                'refTransId' => '2165665483'
            ),
        );
        $responseJson = '{
           "transactionResponse":{
              "responseCode":"3",
              "authCode":"",
              "avsResultCode":"P",
              "cvvResultCode":"",
              "cavvResultCode":"",
              "transId":"0",
              "refTransID":"2165665483",
              "transHash":"06D737C28ECD531129DC59EF0548D7FA",
              "testRequest":"0",
              "accountNumber":"",
              "accountType":"MasterCard",
              "errors":[
                 {
                    "errorCode":"16",
                    "errorText":"The transaction cannot be found."
                 }
              ]
           },
           "refId":"23039947",
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

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('', $response->transactionResponse->authCode);
        $this->assertEquals('0', $response->transactionResponse->transId);
        $this->assertEquals('2165665483', $response->transactionResponse->refTransID);
        $this->assertEquals('3', $response->transactionResponse->responseCode);
        $this->assertEquals('P', $response->transactionResponse->avsResultCode);
        $this->assertEquals('', $response->transactionResponse->cvvResultCode);
        $this->assertEquals('', $response->transactionResponse->cavvResultCode);
        $this->assertEquals('06D737C28ECD531129DC59EF0548D7FA', $response->transactionResponse->transHash);
        $this->assertEquals('0', $response->transactionResponse->testRequest);
        $this->assertEquals('', $response->transactionResponse->accountNumber);
        $this->assertEquals('MasterCard', $response->transactionResponse->accountType);
        $this->assertEquals('16', $response->transactionResponse->errors[0]->errorCode);
        $this->assertEquals('The transaction cannot be found.', $response->transactionResponse->errors[0]->errorText);
        $this->assertEquals('23039947', $response->refId);
        $this->assertEquals('E00027', $response->messages->message[0]->code);
        $this->assertEquals('The transaction was unsuccessful.', $response->messages->message[0]->text);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testSendCustomerTransactionReceiptRequest()
    {
        $requestJson = array(
            'refId' => "2241729",
            'transId' => '2165665581',
            'customerEmail' => 'user@example.com',
            'emailSettings' => array(
                'setting' => array(
                    'settingName' => 'headerEmailReceipt',
                    'settingValue' => 'some HEADER stuff'
                ),
                'setting' => array(
                    'settingName' => 'footerEmailReceipt',
                    'settingValue' => 'some FOOTER stuff'
                ),
            ),
        );
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

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('2241729', $response->refId);
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }
}