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

    public function testCreateTransactionRequestAuthCaptureSuccess()
    {
        $request = array(
            'refId' => rand(1000000, 100000000),
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

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->createTransactionRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('This transaction has been approved.', $authnet->transactionResponse->messages[0]->description);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('QWX20S', $authnet->transactionResponse->authCode);
        $this->assertEquals('2228446239', $authnet->transactionResponse->transId);
        $this->assertEquals('1', $authnet->transactionResponse->responseCode);
        $this->assertEquals('Y', $authnet->transactionResponse->avsResultCode);
        $this->assertEquals('P', $authnet->transactionResponse->cvvResultCode);
        $this->assertEquals('2', $authnet->transactionResponse->cavvResultCode);
        $this->assertEquals('56B2D50D73CAB8C6EDE7A92B9BB235BD', $authnet->transactionResponse->transHash);
        $this->assertEquals('0', $authnet->transactionResponse->testRequest);
        $this->assertEquals('XXXX1111', $authnet->transactionResponse->accountNumber);
        $this->assertEquals('Visa', $authnet->transactionResponse->accountType);
        $this->assertEquals('1', $authnet->transactionResponse->messages[0]->code);
        $this->assertEquals('favorite_color', $authnet->transactionResponse->userFields[0]->name);
        $this->assertEquals('blue', $authnet->transactionResponse->userFields[0]->value);
        $this->assertEquals('94564789', $authnet->refId);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
    }

    public function testCreateTransactionRequestAuthOnlySuccess()
    {
        $request = array(
            'refId' => rand(1000000, 100000000),
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

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->createTransactionRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('This transaction has been approved.', $authnet->transactionResponse->messages[0]->description);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('7M6LIT', $authnet->transactionResponse->authCode);
        $this->assertEquals('2228545782', $authnet->transactionResponse->transId);
        $this->assertEquals('1', $authnet->transactionResponse->responseCode);
        $this->assertEquals('Y', $authnet->transactionResponse->avsResultCode);
        $this->assertEquals('P', $authnet->transactionResponse->cvvResultCode);
        $this->assertEquals('2', $authnet->transactionResponse->cavvResultCode);
        $this->assertEquals('6210B3AEC49FC269036D42F9681459A9', $authnet->transactionResponse->transHash);
        $this->assertEquals('0', $authnet->transactionResponse->testRequest);
        $this->assertEquals('XXXX0015', $authnet->transactionResponse->accountNumber);
        $this->assertEquals('MasterCard', $authnet->transactionResponse->accountType);
        $this->assertEquals('1', $authnet->transactionResponse->messages[0]->code);
        $this->assertEquals('favorite_color', $authnet->transactionResponse->userFields[0]->name);
        $this->assertEquals('blue', $authnet->transactionResponse->userFields[0]->value);
        $this->assertEquals('65376587', $authnet->refId);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
    }

    public function testCreateTransactionRequestAuthOnlyError()
    {
        $request = array(
            'refId' => rand(1000000, 100000000),
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

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->createTransactionRequest($request);

        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertFalse($authnet->isSuccessful());
        $this->assertTrue($authnet->isError());
        $this->assertEquals('3', $authnet->transactionResponse->responseCode);
        $this->assertEquals('P', $authnet->transactionResponse->avsResultCode);
        $this->assertEquals('0', $authnet->transactionResponse->transId);
        $this->assertEquals('0', $authnet->transactionResponse->testRequest);
        $this->assertEquals('9F18DE7ABDD09076F9BADB594EFC4611', $authnet->transactionResponse->transHash);
        $this->assertEquals('XXXX0015', $authnet->transactionResponse->accountNumber);
        $this->assertEquals('MasterCard', $authnet->transactionResponse->accountType);
        $this->assertEmpty($authnet->transactionResponse->authCode);
        $this->assertEmpty($authnet->transactionResponse->cvvResultCode);
        $this->assertEmpty($authnet->transactionResponse->cavvResultCode);
        $this->assertEmpty($authnet->transactionResponse->refTransID);
        $this->assertEquals('11', $authnet->transactionResponse->errors[0]->errorCode);
        $this->assertEquals('A duplicate transaction has been submitted.', $authnet->transactionResponse->errors[0]->errorText);
        $this->assertEquals('favorite_color', $authnet->transactionResponse->userFields[0]->name);
        $this->assertEquals('blue', $authnet->transactionResponse->userFields[0]->value);
        $this->assertEquals('14290435', $authnet->refId);
        $this->assertEquals('E00027', $authnet->messages->message[0]->code);
        $this->assertEquals('The transaction was unsuccessful.', $authnet->messages->message[0]->text);
    }
}