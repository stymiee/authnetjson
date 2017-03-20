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

require(__DIR__ . '/../config.inc.php');

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

        $this->http = $this->getMockBuilder('\Curl\Curl')
            ->setMethods(['post'])
            ->getMock();
        $this->http->error = false;
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

        $this->http->response = $responseJson;

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

        $this->http->response = $responseJson;

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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals(AuthnetJsonResponse::STATUS_ERROR, $response->transactionResponse->responseCode);
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

        $this->http->response = $responseJson;

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

        $this->http->response = $responseJson;

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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('', $response->transactionResponse->authCode);
        $this->assertEquals('2165665234', $response->transactionResponse->refTransID);
        $this->assertEquals(AuthnetJsonResponse::STATUS_ERROR, $response->transactionResponse->responseCode);
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

        $this->http->response = $responseJson;

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

        $this->http->response = $responseJson;

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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('', $response->transactionResponse->authCode);
        $this->assertEquals('0', $response->transactionResponse->transId);
        $this->assertEquals('2165665483', $response->transactionResponse->refTransID);
        $this->assertEquals(AuthnetJsonResponse::STATUS_ERROR, $response->transactionResponse->responseCode);
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

        $this->http->response = $responseJson;

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


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestVisaCheckout()
    {
        $requestJson = array(
            "refId" => rand(1000000, 100000000),
            "transactionRequest" => array(
                "transactionType" => "authCaptureTransaction",
                "amount" => "5",
                "payment" => array(
                    "opaqueData" => array(
                        "dataDescriptor" => "COMMON.VCO.ONLINE.PAYMENT",
                        "dataValue" => "2BceaAHSHwTc0oA8RTqXfqpqDQk+e0thusXG/SAy3EuyksOis8aEMkxuVZDRUpbZVOFh6JnmUl/LS3s+GuoPFJbR8+OBfwJRBNGSuFIgYFhZooXYQbH0pkO6jY3WCMTwiYymGz359T9M6WEdCA2oIMRKOw8PhRpZLvaoqlxZ+oILMq6+NCckdBd2LJeFNOHzUlBvYdVmUX1K1IhJVB0+nxCjFijYQQQVlxx5sacg+9A0YWWhxNnEk8HzeCghnU9twR1P/JGI5LlT+AaPmP0wj6LupH0mNFDIYzLIoA6eReIrTqn63OwJdCwYZ7qQ2hWVhPyLZty22NycWjVCdl2hdrNhWyOySXJqKDFGIq0yrnD3Hh1Y71hbg4GjsObhxtq3IsGT1JgoL9t6Q393Yw2K4sdzjgSJgetxrh2aElgLs9mEtQfWYUo71KpesMmDPxZYlf+NToIXpgz5yrQ/FT29YCSbGUICO9ems9buhb0iwcuhhamUcg336bLjL2K54+s1cpOGNEuqUi0cSMvng9T9IKZgWO+jMvQmJzRsIB5KD3FooCnDwxadp61eWfc+Bl+e0b0oQXSxJt12vyghZBgHvhEQcXU+YGBihbyYuI1JOPtt+A3smz7Emfd2+ktGF4lp82RVQNXlG9ENtKr26Utntc/xfj+y1UX2NUtsn22rW+Kahb20/4hHXA8DLcmfeHMDvrupePIcCKj02+Feofc2RMnVQLS9bsXXzbxzYGm6kHtmaXteNTnpB67U0z3igD17bYFOZurNQUBEHLXntCAMqCL7kkBT8Qx8yaSA3uhzJVhLSWU9ovu2uM1AGIauJki5TxdjSyrcj56Hi8sVB7FNxWcsZhGb67wwNRVwwyurU+YFVXB8fJna3FW686qPfo+UnyVUxwOXP4g+wC661VcrkMPPwZySzVaM7vH2n2VENkECCpm3bBYBitRifHwhRlYFF6z0GNhJQ2JLy2+YJZrlNWUMtzvYjcKojgA6YsCkSr1tFCv2c+oLAfkC720G9IvfCNQyv4o2dH554taGPvMynzbtCjWbhCj3sdX80AO2o/fQjeowa45/lYkY4CDgthzjwQwDaEK9qEKSIWjt3C/gb/VgxPRikdwmVCfI5XltVSX/bIoSyGiHvIo8DoTYebridtw4dHNx55y/a12Y/dWjpDNqV+8enZqNEfSrGSAC3E+nv9oM8ttB1JQ1GVtuN7vIv+tjHuYLEV9/U2WkUlA5ia3JjWRp3mOiYmluyof7pZbFPi5UpbKs9Nqp58oH5nI4f9JhUg2iQUz5cBiRJpntD8/KgA9JngTuQsoGWSt397hDmM99q848cqJf8UJ0OXqdgMf5mjh/atatfqgxglXfuPD2lJlJAdFDLEs0a/EEGrhwf2t14Gaw7XwBl6CSy2HzdD+HwmkNSdicb8an+JbH0WPqH8DXR8eaT0P11rjVtoGTPwQD1G+OllgfmzL5kxtRZFqbfFVCRqLjHGsygNX9Ts8nxGz301NT294HnkZSfre7hadDQUqTpZo0Em/DkwY1ruuba3zfLwzv0C4Hil3FllEhZbPNYIcb4C5FHQ2NlqVCSt2YfofGYkDWI2g46UJG1rlLQ4OQ7jcdjybvasMqcCvqO+jkQK07DZtb5gZEnzWqGSNcRdzz6cJbnbJqDDhxyci+FRBE0an9m7iHX7lyhEbfnjatP606DRikKQkmPnjaDlhsAJA6Yx82zu3z88/wJG75W8TkKrbyEAMiB5CGDSg/bvEUN60VN9PRbYiD3XTm8ZpTg0k2hQfh/xwRktbRKJ/zqp5l5Jchif0vrtMJdk6omzMMy0LBCSzu9aNAELwz4CQoSpeKA90piGy0T/IjiAvq2r6hOWfAUvZITckN9PA1NPaqEkACG1jyK+LgXew/CCplywL3Tz76fKkkYYApJAuTgzja6O2a9xC3ULlMjfVzeDHbV+R4mDno35mDOz7q7BB2Qoj3TBr6yLgz9mzZssY48U93Nwd1g663NKk1mn/i2a0fLeaOOr46d/tS0oXCEIB+NIOlYYQqKfuZAh0GSzVZMYZsQ4NfueSx5VY80MibBYrVk26u3/sco5wvaz0C3PY27pBj89VhM5kAhGv1CXJcbIFBJ/B9Xw9VFsTf39PfJUhB7b0+7+zFwtriJn02WcW1Z9pX78wSs0AxwYCMbNtxzK5fFZZcdOt2HOsIHw==",
                        "dataKey" => "k5qdgh6t5BnvuBz3Xs0f+iraXB0tXO+6WewMUVTBsqYN3G16HHYwlz8To602G/pZ+Sng0mjTAy3mwhpOb/5nJktiCTpKL4wvn0exJxGt9GIAODBYz4PFbxuenmuQE9O5"
                    ),
                ),
                "callId" => "4859677641513545101"
            ),
        );
        $responseJson = '{
            "transactionResponse": {
                "responseCode": "1",
                "authCode": "C1E3I6",
                "avsResultCode": "Y",
                "cvvResultCode": "S",
                "cavvResultCode": "9",
                "transId": "2149186775",
                "refTransID": "",
                "transHash": "C85B15CED28462974F1114DB07A16C39",
                "testRequest": "0",
                "accountNumber": "XXXX0015",
                "accountType": "MasterCard",
                "messages": [
                    {
                        "code": "1",
                        "description": "This transaction has been approved."
                    }
                ]
            },
            "refId": "123456",
            "messages": {
                "resultCode": "Ok",
                "message": [
                    {
                        "code": "I00001",
                        "text": "Successful."
                    }
                ]
            }
        }';

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('1', $response->transactionResponse->responseCode);
        $this->assertEquals('C1E3I6', $response->transactionResponse->authCode);
        $this->assertEquals('Y', $response->transactionResponse->avsResultCode);
        $this->assertEquals('S', $response->transactionResponse->cvvResultCode);
        $this->assertEquals('9', $response->transactionResponse->cavvResultCode);
        $this->assertEquals('2149186775', $response->transactionResponse->transId);
        $this->assertEquals('', $response->transactionResponse->refTransID);
        $this->assertEquals('C85B15CED28462974F1114DB07A16C39', $response->transactionResponse->transHash);
        $this->assertEquals('0', $response->transactionResponse->testRequest);
        $this->assertEquals('XXXX0015', $response->transactionResponse->accountNumber);
        $this->assertEquals('MasterCard', $response->transactionResponse->accountType);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testDecryptPaymentDataRequest()
    {
        $requestJson = array(
            "opaqueData" => array(
                "dataDescriptor" => "COMMON.VCO.ONLINE.PAYMENT",
                "dataValue" => "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",
                "dataKey" => "k5qdgh6t5BnvuBz3Xs0f+iraXB0tXO+6WewMUVTBsqYN3G16HHYwlz8To602G/pZ+Sng0mjTAy3mwhpOb/5nJktiCTpKL4wvn0exJxGt9GIAODBYz4PFbxuenmuQE9O5"
            ),
            "callId" => "4859677641513545101"
        );
        $responseJson = '{
            "shippingInfo": {
                "firstName": "John",
                "lastName": "Doe",
                "address": "5100 Main St",
                "city": "Bellevue",
                "state": "CA",
                "zip": "98104",
                "country": "US"
            },
            "billingInfo": {
                "email": "bmcmanus@visa.com",
                "firstName": "John",
                "lastName": "Doe",
                "address": "5100 Main St",
                "city": "Bellevue",
                "state": "CA",
                "zip": "98104",
                "country": "US"
            },
            "cardInfo": {
                "cardNumber": "XXXX4242",
                "expirationDate": "12/2018",
                "cardArt": {
                    "cardBrand": "VISA",
                    "cardImageHeight": "50",
                    "cardImageUrl": "https://sandbox.secure.checkout.visa.com/VmeCardArts/wv87HR3X3jqlNXNJu_6YtLQyyO7mpu2aU6Yo3VWGKKM.png",
                    "cardImageWidth": "77",
                    "cardType": "DEBIT"
                }
            },
            "paymentDetails": {
                "currency": "USD",
                "amount": "16.00"
            },
            "messages": {
                "resultCode": "Ok",
                "message": [
                    {
                        "code": "I00001",
                        "text": "Successful."
                    }
                ]
            }
        }';

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('John', $response->shippingInfo->firstName);
        $this->assertEquals('Doe', $response->shippingInfo->lastName);
        $this->assertEquals('5100 Main St', $response->shippingInfo->address);
        $this->assertEquals('Bellevue', $response->shippingInfo->city);
        $this->assertEquals('CA', $response->shippingInfo->state);
        $this->assertEquals('98104', $response->shippingInfo->zip);
        $this->assertEquals('US', $response->shippingInfo->country);
        $this->assertEquals('bmcmanus@visa.com', $response->billingInfo->email);
        $this->assertEquals('John', $response->billingInfo->firstName);
        $this->assertEquals('Doe', $response->billingInfo->lastName);
        $this->assertEquals('5100 Main St', $response->billingInfo->address);
        $this->assertEquals('Bellevue', $response->billingInfo->city);
        $this->assertEquals('CA', $response->billingInfo->state);
        $this->assertEquals('98104', $response->billingInfo->zip);
        $this->assertEquals('US', $response->billingInfo->country);
        $this->assertEquals('XXXX4242', $response->cardInfo->cardNumber);
        $this->assertEquals('12/2018', $response->cardInfo->expirationDate);
        $this->assertEquals('VISA', $response->cardInfo->cardArt->cardBrand);
        $this->assertEquals('50', $response->cardInfo->cardArt->cardImageHeight);
        $this->assertEquals('https://sandbox.secure.checkout.visa.com/VmeCardArts/wv87HR3X3jqlNXNJu_6YtLQyyO7mpu2aU6Yo3VWGKKM.png', $response->cardInfo->cardArt->cardImageUrl);
        $this->assertEquals('77', $response->cardInfo->cardArt->cardImageWidth);
        $this->assertEquals('DEBIT', $response->cardInfo->cardArt->cardType);
        $this->assertEquals('USD', $response->paymentDetails->currency);
        $this->assertEquals('16.00', $response->paymentDetails->amount);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testDecryptPaymentDataRequestError()
    {
        $requestJson = array(
            "opaqueData" => array(
                "dataDescriptor" => "COMMON.VCO.ONLINE.PAYMENT",
                "dataValue" => "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",
                "dataKey" => "k5qdgh6t5BnvuBz3Xs0f+iraXB0tXO+6WewMUVTBsqYN3G16HHYwlz8To602G/pZ+Sng0mjTAy3mwhpOb/5nJktiCTpKL4wvn0exJxGt9GIAODBYz4PFbxuenmuQE9O5"
            ),
            "callId" => "4859677641513545101"
        );
        $responseJson = '{
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00001",
                    "text":"Unable to process decryption"
                 }
              ]
           }
        }';

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('Unable to process decryption', $response->messages->message[0]->text);
    }
}