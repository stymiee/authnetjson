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

class AuthnetJsonCimTest extends \PHPUnit_Framework_TestCase
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


    public function testCreateCustomerProfileRequestSuccess()
    {
        $request = array(
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
        $responseJson = '{
           "customerProfileId":"31390172",
           "customerPaymentProfileIdList":[
              "28393490"
           ],
           "customerShippingAddressIdList":[
              "29366174"
           ],
           "validationDirectResponseList":[
              "1,1,1,This transaction has been approved.,1VQHEI,Y,2228580111,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,,,,,,,,,0.00,0.00,0.00,FALSE,none,317FCDBBCBABB2C7442766267D4C099C,,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,"
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

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->createCustomerProfileRequest($request);

        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('31390172', $authnet->customerProfileId);
        $this->assertEquals('28393490', $authnet->customerPaymentProfileIdList[0]);
        $this->assertEquals('29366174', $authnet->customerShippingAddressIdList[0]);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
    }


    public function testCreateCustomerProfileRequestDuplicateRecordError()
    {
        $request = array(
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
        $responseJson = '{
           "customerPaymentProfileIdList":[

           ],
           "customerShippingAddressIdList":[

           ],
           "validationDirectResponseList":[
              "1,1,1,This transaction has been approved.,32ZKPG,Y,2228580073,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,87657,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,,,,,,,,,0.00,0.00,0.00,FALSE,none,B1D58B7B6A29B6F989FBC6DC541F04BE,,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,"
           ],
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00039",
                    "text":"A duplicate record with ID 20382791 already exists."
                 }
              ]
           }
        }';

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->createCustomerProfileRequest($request);

        $this->assertFalse($authnet->isSuccessful());
        $this->assertTrue($authnet->isError());
        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertEquals('E00039', $authnet->messages->message[0]->code);
        $this->assertEquals('A duplicate record with ID 20382791 already exists.', $authnet->messages->message[0]->text);
    }


    public function testCreateCustomerPaymentProfileRequest()
    {
        $request = array(
            'customerProfileId' => '30582495',
            'paymentProfile' => array(
                'billTo' => array(
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'company' => '',
                    'address' => '123 Main St.',
                    'city' => 'Bellevue',
                    'state' => 'WA',
                    'zip' => '98004',
                    'country' => 'USA',
                    'phoneNumber' => '800-555-1234',
                    'faxNumber' => '800-555-1234'
                ),
                'payment' => array(
                    'creditCard' => array(
                        'cardNumber' => '4111111111111111',
                        'expirationDate' => '2016-08'
                    )
                )
            ),
            'validationMode' => 'liveMode'
        );
        $responseJson = '{
           "customerPaymentProfileId":"28821903",
           "validationDirectResponse":"1,1,1,This transaction has been approved.,4DHVNH,Y,2230582188,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,none,John,Doe,,123 Main St.,Bellevue,WA,98004,USA,800-555-1234,800-555-1234,email@example.com,,,,,,,,,0.00,0.00,0.00,FALSE,none,E440D094322A0D406E01EDF9CE871A4F,,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,",
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
        $authnet->createCustomerPaymentProfileRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('28821903', $authnet->customerPaymentProfileId);
    }


    public function testCreateCustomerProfileTransactionAuthCaptureRequest()
    {
        $request = array(
            'transaction' => array(
                'profileTransAuthCapture' => array(
                    'amount' => '10.95',
                    'tax' => array(
                        'amount' => '1.00',
                        'name' => 'WA state sales tax',
                        'description' => 'Washington state sales tax'
                    ),
                    'shipping' => array(
                        'amount' => '2.00',
                        'name' => 'ground based shipping',
                        'description' => 'Ground based 5 to 10 day shipping'
                    ),
                    'lineItems' => array(
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
                    ),
                    'customerProfileId' => '31390172',
                    'customerPaymentProfileId' => '28393490',
                    'customerShippingAddressId' => '29366174',
                    'order' => array(
                        'invoiceNumber' => 'INV000001',
                        'description' => 'description of transaction',
                        'purchaseOrderNumber' => 'PONUM000001'
                    ),
                    'taxExempt' => 'false',
                    'recurringBilling' => 'false',
                    'cardCode' => '000'
                )
            ),
            'extraOptions' => 'x_customer_ip=100.0.0.1'
        );
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

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->createCustomerProfileTransactionRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $authnet->directResponse);
    }


    public function testCreateCustomerProfileTransactionRequestAuthCaptureError()
    {
        $request = array (
            'createCustomerProfileTransactionRequest' => array (
                'merchantAuthentication' => array (
                    'name' => 'cnpdev4289',
                    'transactionKey' => 'SR2P8g4jdEn7vFLQ',
                ),
                'transaction' => array (
                    'profileTransAuthCapture' => array (
                        'amount' => '10.95',
                        'tax' => array (
                            'amount' => '1.00',
                            'name' => 'WA state sales tax',
                            'description' => 'Washington state sales tax',
                        ),
                        'shipping' => array (
                            'amount' => '2.00',
                            'name' => 'ground based shipping',
                            'description' => 'Ground based 5 to 10 day shipping',
                        ),
                        'lineItems' => array (
                            0 => array (
                                    'itemId' => '1',
                                    'name' => 'vase',
                                    'description' => 'Cannes logo',
                                    'quantity' => '18',
                                    'unitPrice' => '45.00',
                                ),
                            1 => array (
                                    'itemId' => '2',
                                    'name' => 'desk',
                                    'description' => 'Big Desk',
                                    'quantity' => '10',
                                    'unitPrice' => '85.00',
                                ),
                            ),
                        'customerProfileId' => '5427896',
                        'customerPaymentProfileId' => '4796541',
                        'customerShippingAddressId' => '4907537',
                        'order' => array (
                            'invoiceNumber' => 'INV000001',
                            'description' => 'description of transaction',
                            'purchaseOrderNumber' => 'PONUM000001',
                        ),
                        'taxExempt' => 'false',
                        'recurringBilling' => 'false',
                        'cardCode' => '000',
                        ),
                    ),
                'extraOptions' => 'x_customer_ip=100.0.0.1',
            ),
        );
        $responseJson = '{
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00040",
                    "text":"Customer Profile ID or Customer Payment Profile ID not found."
                 }
              ]
           }
        }';

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->createCustomerProfileTransactionRequest($request);

        $this->assertFalse($authnet->isSuccessful());
        $this->assertTrue($authnet->isError());
        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertEquals('E00040', $authnet->messages->message[0]->code);
        $this->assertEquals('Customer Profile ID or Customer Payment Profile ID not found.', $authnet->messages->message[0]->text);
    }


    public function testCreateCustomerProfileTransactionRequestAuthOnly()
    {
        $request = array(
            'transaction' => array(
                'profileTransAuthOnly' => array(
                    'amount' => '10.95',
                    'tax' => array(
                        'amount' => '1.00',
                        'name' => 'WA state sales tax',
                        'description' => 'Washington state sales tax'
                    ),
                    'shipping' => array(
                        'amount' => '2.00',
                        'name' => 'ground based shipping',
                        'description' => 'Ground based 5 to 10 day shipping'
                    ),
                    'lineItems' => array(
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
                    ),
                    'customerProfileId' => '31390172',
                    'customerPaymentProfileId' => '28393490',
                    'customerShippingAddressId' => '29366174',
                    'order' => array(
                        'invoiceNumber' => 'INV000001',
                        'description' => 'description of transaction',
                        'purchaseOrderNumber' => 'PONUM000001'
                    ),
                    'taxExempt' => 'false',
                    'recurringBilling' => 'false',
                    'cardCode' => '000'
                )
            ),
            'extraOptions' => 'x_customer_ip=100.0.0.1'
        );
        $responseJson = '{
           "directResponse":"1,1,1,This transaction has been approved.,KF2IM6,Y,2230582323,INV000001,description of transaction,10.95,CC,auth_only,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,FALSE,PONUM000001,15D36F54160C246186DA774FE261646B,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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
        $authnet->createCustomerProfileTransactionRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,KF2IM6,Y,2230582323,INV000001,description of transaction,10.95,CC,auth_only,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,FALSE,PONUM000001,15D36F54160C246186DA774FE261646B,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $authnet->directResponse);
    }


    public function testCreateCustomerProfileTransactionRequestCaptureOnly()
    {
        $request = array(
            'transaction' => array(
                'profileTransCaptureOnly' => array(
                    'amount' => '10.95',
                    'tax' => array(
                        'amount' => '1.00',
                        'name' => 'WA state sales tax',
                        'description' => 'Washington state sales tax'
                    ),
                    'shipping' => array(
                        'amount' => '2.00',
                        'name' => 'ground based shipping',
                        'description' => 'Ground based 5 to 10 day shipping'
                    ),
                    'lineItems' => array(
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
                    ),
                    'customerProfileId' => '31390172',
                    'customerPaymentProfileId' => '28393490',
                    'customerShippingAddressId' => '29366174',
                    'order' => array(
                        'invoiceNumber' => 'INV000001',
                        'description' => 'description of transaction',
                        'purchaseOrderNumber' => 'PONUM000001'
                    ),
                    'taxExempt' => 'false',
                    'recurringBilling' => 'false',
                    'cardCode' => '000',
                    'approvalCode' => '000000'
                )
            ),
            'extraOptions' => 'x_customer_ip=100.0.0.1'
        );
        $responseJson = '{
           "directResponse":"1,1,1,This transaction has been approved.,000000,P,2230582335,INV000001,description of transaction,10.95,CC,capture_only,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,FALSE,PONUM000001,0DAC5007786DEA5A5EB02C0C56A68F87,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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
        $authnet->createCustomerProfileTransactionRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,000000,P,2230582335,INV000001,description of transaction,10.95,CC,capture_only,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,FALSE,PONUM000001,0DAC5007786DEA5A5EB02C0C56A68F87,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $authnet->directResponse);
    }


    public function testCreateCustomerProfileTransactionRequestPriorAuthCapture()
    {
        $request = array(
            'transaction' => array(
                'profileTransPriorAuthCapture' => array(
                    'amount' => '10.95',
                    'tax' => array(
                        'amount' => '1.00',
                        'name' => 'WA state sales tax',
                        'description' => 'Washington state sales tax'
                    ),
                    'shipping' => array(
                        'amount' => '2.00',
                        'name' => 'ground based shipping',
                        'description' => 'Ground based 5 to 10 day shipping'
                    ),
                    'lineItems' => array(
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
                    ),
                    'customerProfileId' => '31390172',
                    'customerPaymentProfileId' => '28393490',
                    'customerShippingAddressId' => '29366174',
                    'transId' => '2230582347'
                )
            ),
            'extraOptions' => 'x_customer_ip=100.0.0.1'
        );
        $responseJson = '{
           "directResponse":"1,1,1,This transaction has been approved.,S9WA0V,P,2230582347,INV000001,,10.95,CC,prior_auth_capture,12345,,,,,,,12345,,,,,,,,,,,,,1.00,,2.00,,,66E86622C893D1DBBC47D1B314CB57E2,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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
        $authnet->createCustomerProfileTransactionRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,S9WA0V,P,2230582347,INV000001,,10.95,CC,prior_auth_capture,12345,,,,,,,12345,,,,,,,,,,,,,1.00,,2.00,,,66E86622C893D1DBBC47D1B314CB57E2,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $authnet->directResponse);
    }


    public function testCreateCustomerProfileTransactionRequestPriorAuthCaptureError()
    {
        $request = array (
            'createCustomerProfileTransactionRequest' => array (
                'merchantAuthentication' => array (
                    'name' => 'cnpdev4289',
                    'transactionKey' => 'SR2P8g4jdEn7vFLQ',
                ),
                'transaction' => array (
                    'profileTransPriorAuthCapture' => array (
                        'amount' => '10.95',
                        'tax' => array (
                            'amount' => '1.00',
                            'name' => 'WA state sales tax',
                            'description' => 'Washington state sales tax',
                        ),
                        'shipping' => array (
                            'amount' => '2.00',
                            'name' => 'ground based shipping',
                            'description' => 'Ground based 5 to 10 day shipping',
                        ),
                        'lineItems' => array (
                            0 => array (
                                'itemId' => '1',
                                'name' => 'vase',
                                'description' => 'Cannes logo',
                                'quantity' => '18',
                                'unitPrice' => '45.00',
                            ),
                            1 => array (
                                'itemId' => '2',
                                'name' => 'desk',
                                'description' => 'Big Desk',
                                'quantity' => '10',
                                'unitPrice' => '85.00',
                                ),
                            ),
                        'customerProfileId' => '31390172',
                        'customerPaymentProfileId' => '28393490',
                        'customerShippingAddressId' => '29366174',
                        'transId' => '2230582306',
                        ),
                    ),
                'extraOptions' => 'x_customer_ip=100.0.0.1',
            ),
        );
        $responseJson = '{
           "directResponse":"3,2,16,The transaction cannot be found.,,P,0,,,10.95,CC,prior_auth_capture,,,,,,,,,,,,,,,,,,,,,1.00,,2.00,,,4B27B1C0BEF8095AA2322B378E6B98C0,,,,,,,,,,,,,,Visa,,,,,,,,,,,,,,,,,29366174",
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00027",
                    "text":"The transaction cannot be found."
                 }
              ]
           }
        }';

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->createCustomerProfileTransactionRequest($request);

        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertFalse($authnet->isSuccessful());
        $this->assertTrue($authnet->isError());
        $this->assertEquals('E00027', $authnet->messages->message[0]->code);
        $this->assertEquals('The transaction cannot be found.', $authnet->messages->message[0]->text);
    }


    public function testCreateCustomerProfileTransactionRequestRefund()
    {
        $request = array(
            'transaction' => array(
                'profileTransRefund' => array(
                    'amount' => '10.95',
                    'tax' => array(
                        'amount' => '1.00',
                        'name' => 'WA state sales tax',
                        'description' => 'Washington state sales tax'
                    ),
                    'shipping' => array(
                        'amount' => '2.00',
                        'name' => 'ground based shipping',
                        'description' => 'Ground based 5 to 10 day shipping'
                    ),
                    'lineItems' => array(
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
                    ),
                    'customerProfileId' => '31390172',
                    'customerPaymentProfileId' => '28393490',
                    'customerShippingAddressId' => '29366174',
                    'creditCardNumberMasked' => 'XXXX1111',
                    'order' => array(
                        'invoiceNumber' => 'INV000001',
                        'description' => 'description of transaction',
                        'purchaseOrderNumber' => 'PONUM000001'
                    ),
                    'transId' => '2230582306'
                )
            ),
            'extraOptions' => 'x_customer_ip=100.0.0.1'
        );
        $responseJson = '{
           "directResponse":"1,1,1,This transaction has been approved.,,P,2230582363,INV000001,description of transaction,10.95,CC,credit,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,,PONUM000001,5E1CD1DFC373ACF8F084F5D220945BA0,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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
        $authnet->createCustomerProfileTransactionRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,,P,2230582363,INV000001,description of transaction,10.95,CC,credit,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,,PONUM000001,5E1CD1DFC373ACF8F084F5D220945BA0,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $authnet->directResponse);
    }


    public function testCreateCustomerProfileTransactionRequestRefundError()
    {
        $request = array (
            'createCustomerProfileTransactionRequest' => array (
                'merchantAuthentication' => array (
                    'name' => 'cnpdev4289',
                    'transactionKey' => 'SR2P8g4jdEn7vFLQ',
                ),
                'transaction' => array (
                    'profileTransRefund' => array (
                        'amount' => '10.95',
                        'tax' => array (
                            'amount' => '1.00',
                            'name' => 'WA state sales tax',
                            'description' => 'Washington state sales tax',
                        ),
                        'shipping' => array (
                            'amount' => '2.00',
                            'name' => 'ground based shipping',
                            'description' => 'Ground based 5 to 10 day shipping',
                        ),
                        'lineItems' => array (
                            0 => array (
                                    'itemId' => '1',
                                    'name' => 'vase',
                                    'description' => 'Cannes logo',
                                    'quantity' => '18',
                                    'unitPrice' => '45.00',
                                ),
                            1 => array (
                                    'itemId' => '2',
                                    'name' => 'desk',
                                    'description' => 'Big Desk',
                                    'quantity' => '10',
                                    'unitPrice' => '85.00',
                                ),
                            ),
                        'customerProfileId' => '31390172',
                        'customerPaymentProfileId' => '28393490',
                        'customerShippingAddressId' => '29366174',
                        'creditCardNumberMasked' => 'XXXX1111',
                        'order' => array (
                            'invoiceNumber' => 'INV000001',
                            'description' => 'description of transaction',
                            'purchaseOrderNumber' => 'PONUM000001',
                        ),
                        'transId' => '2230582347',
                        ),
                    ),
                'extraOptions' => 'x_customer_ip=100.0.0.1',
            ),
        );
        $responseJson = '{
           "directResponse":"3,2,54,The referenced transaction does not meet the criteria for issuing a credit.,,P,0,INV000001,description of transaction,10.95,CC,credit,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,,PONUM000001,4B27B1C0BEF8095AA2322B378E6B98C0,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00027",
                    "text":"The referenced transaction does not meet the criteria for issuing a credit."
                 }
              ]
           }
        }';

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->createCustomerProfileTransactionRequest($request);

        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertFalse($authnet->isSuccessful());
        $this->assertTrue($authnet->isError());
        $this->assertEquals('E00027', $authnet->messages->message[0]->code);
        $this->assertEquals('The referenced transaction does not meet the criteria for issuing a credit.', $authnet->messages->message[0]->text);
    }


    public function testCreateCustomerProfileTransactionRequestVoid()
    {
        $request = array(
            'transaction' => array(
                'profileTransVoid' => array(
                    'customerProfileId' => '31390172',
                    'customerPaymentProfileId' => '28393490',
                    'customerShippingAddressId' => '29366174',
                    'transId' => '2230582868'
                )
            ),
            'extraOptions' => 'x_customer_ip=100.0.0.1'
        );
        $responseJson = '{
           "directResponse":"1,1,1,This transaction has been approved.,OWW0UU,P,2230582868,INV000001,,0.00,CC,void,12345,,,,,,,12345,,,,,,,,,,,,,,,,,,0C7394DFC38A5BDC5737A354CE67B421,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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
        $authnet->createCustomerProfileTransactionRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,OWW0UU,P,2230582868,INV000001,,0.00,CC,void,12345,,,,,,,12345,,,,,,,,,,,,,,,,,,0C7394DFC38A5BDC5737A354CE67B421,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $authnet->directResponse);
    }


    public function testCreateCustomerShippingAddressRequest()
    {
        $request = array(
            'customerProfileId' => '31390172',
            'address' => array(
                'firstName' => 'John',
                'lastName' => 'Doe',
                'company' => '',
                'address' => '123 Main St.',
                'city' => 'Bellevue',
                'state' => 'WA',
                'zip' => '98004',
                'country' => 'USA',
                'phoneNumber' => '800-555-1234',
                'faxNumber' => '800-555-1234'
            )
        );
        $responseJson = '{
           "customerAddressId":"29870028",
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
        $authnet->createCustomerShippingAddressRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('29870028', $authnet->customerAddressId);
    }


    public function testUpdateCustomerProfileRequest()
    {
        $request = array(
            'profile' => array(
                'merchantCustomerId' => '12345',
                'description' => 'some description',
                'email' => 'newaddress@example.com',
                'customerProfileId' => '31390172'
            )
        );
        $responseJson = '{
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
        $authnet->updateCustomerProfileRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
    }


    public function testUpdateCustomerProfileRequestError()
    {
        $request = array (
            'updateCustomerPaymentProfileRequest' => array (
                'merchantAuthentication' => array (
                    'name' => 'cnpdev4289',
                    'transactionKey' => 'SR2P8g4jdEn7vFLQ',
                ),
                'customerProfileId' => '31390172',
                'paymentProfile' => array (
                    'billTo' => array (
                        'firstName' => 'John',
                        'lastName' => 'Doe',
                        'company' => '',
                        'address' => '123 Main St.',
                        'city' => 'Bellevue',
                        'state' => 'WA',
                        'zip' => '98004',
                        'country' => 'USA',
                        'phoneNumber' => '800-555-1234',
                        'faxNumber' => '800-555-1234',
                    ),
                    'payment' => array (
                        'creditCard' => array (
                            'cardNumber' => '4111111111111111',
                            'expirationDate' => '2016-08',
                        ),
                    ),
                    'customerPaymentProfileId' => '4966870',
                ),
            ),
        );
        $responseJson = '{
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00040",
                    "text":"The record cannot be found."
                 }
              ]
           }
        }';

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->updateCustomerProfileRequest($request);

        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertFalse($authnet->isSuccessful());
        $this->assertTrue($authnet->isError());
        $this->assertEquals('E00040', $authnet->messages->message[0]->code);
        $this->assertEquals('The record cannot be found.', $authnet->messages->message[0]->text);
    }


    public function testUpdateCustomerPaymentProfileRequest()
    {
        $request = array(
            'customerProfileId' => '31390172',
            'paymentProfile' => array(
                'billTo' => array(
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'company' => '',
                    'address' => '123 Main St.',
                    'city' => 'Bellevue',
                    'state' => 'WA',
                    'zip' => '98004',
                    'country' => 'USA',
                    'phoneNumber' => '800-555-1234',
                    'faxNumber' => '800-555-1234'
                ),
                'payment' => array(
                    'creditCard' => array(
                        'cardNumber' => '4111111111111111',
                        'expirationDate' => '2016-08'
                    )
                ),
                'customerPaymentProfileId' => '28393490'
            )
        );
        $responseJson = '{
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
        $authnet->updateCustomerPaymentProfileRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
    }


    public function testUpdateCustomerShippingAddressRequest()
    {
        $request = array(
            'customerProfileId' => '31390172',
            'address' => array(
                'firstName' => 'John',
                'lastName' => 'Doe',
                'company' => '',
                'address' => '123 Main St.',
                'city' => 'Bellevue',
                'state' => 'WA',
                'zip' => '98004',
                'country' => 'USA',
                'phoneNumber' => '800-555-1234',
                'faxNumber' => '800-555-1234',
                'customerAddressId' => '29366174'
            )
        );
        $responseJson = '{
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
        $authnet->updateCustomerShippingAddressRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
    }


    public function testUpdateCustomerShippingAddressRequestError()
    {
        $request = array (
            'updateCustomerShippingAddressRequest' => array (
                'merchantAuthentication' => array (
                    'name' => 'cnpdev4289',
                    'transactionKey' => 'SR2P8g4jdEn7vFLQ',
                ),
                'customerProfileId' => '31390172',
                'address' => array (
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'company' => '',
                    'address' => '123 Main St.',
                    'city' => 'Bellevue',
                    'state' => 'WA',
                    'zip' => '98004',
                    'country' => 'USA',
                    'phoneNumber' => '800-555-1234',
                    'faxNumber' => '800-555-1234',
                    'customerAddressId' => '4907537',
                ),
            ),
        );
        $responseJson = '{
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00040",
                    "text":"Cannot find the specified shipping address."
                 }
              ]
           }
        }';

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->updateCustomerShippingAddressRequest($request);

        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertFalse($authnet->isSuccessful());
        $this->assertTrue($authnet->isError());
        $this->assertEquals('E00040', $authnet->messages->message[0]->code);
        $this->assertEquals('Cannot find the specified shipping address.', $authnet->messages->message[0]->text);
    }


    public function testUpdateSplitTenderGroupRequest()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }


    public function testUpdateSplitTenderGroupRequestError()
    {
        $request = array (
            'updateSplitTenderGroupRequest' => array (
                'merchantAuthentication' => array (
                    'name' => 'cnpdev4289',
                    'transactionKey' => 'SR2P8g4jdEn7vFLQ',
                ),
                'splitTenderId' => '123456',
                'splitTenderStatus' => 'voided',
            ),
        );
        $responseJson = '{
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00027",
                    "text":"The specified SplitTenderID is invalid."
                 }
              ]
           }
        }';

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->updateSplitTenderGroupRequest($request);

        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertFalse($authnet->isSuccessful());
        $this->assertTrue($authnet->isError());
        $this->assertEquals('E00027', $authnet->messages->message[0]->code);
        $this->assertEquals('The specified SplitTenderID is invalid.', $authnet->messages->message[0]->text);
    }


    public function testDeleteCustomerProfileRequest()
    {
        $request = array(
            'customerProfileId' => '31390172'
        );
        $responseJson = '{
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
        $authnet->deleteCustomerProfileRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
    }


    public function testDeleteCustomerProfileRequestError()
    {
        $request = array (
            'deleteCustomerProfileRequest' => array (
                'merchantAuthentication' => array (
                    'name' => 'cnpdev4289',
                    'transactionKey' => 'SR2P8g4jdEn7vFLQ',
                ),
                'customerProfileId' => '5427896',
            ),
        );
        $responseJson = '{
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00040",
                    "text":"The record cannot be found."
                 }
              ]
           }
        }';

        $this->http->expects($this->once())
            ->method('process')
            ->will($this->returnValue($responseJson));

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $authnet->setProcessHandler($this->http);
        $authnet->deleteCustomerProfileRequest($request);

        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertFalse($authnet->isSuccessful());
        $this->assertTrue($authnet->isError());
        $this->assertEquals('E00040', $authnet->messages->message[0]->code);
        $this->assertEquals('The record cannot be found.', $authnet->messages->message[0]->text);
    }


    public function testDeleteCustomerPaymentProfileRequest()
    {
        $request = array(
            'customerProfileId' => '31390172',
            'customerPaymentProfileId' => '28393490'
        );
        $responseJson = '{
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
        $authnet->deleteCustomerPaymentProfileRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
    }


    public function testDeleteCustomerShippingAddressRequest()
    {
        $request = array(
            'customerProfileId' => '31390172',
            'customerAddressId' => '29366174'
        );
        $responseJson = '{
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
        $authnet->deleteCustomerShippingAddressRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
    }


    public function testValidateCustomerPaymentProfileRequest()
    {
        $request = array(
            'customerProfileId' => '31390172',
            'customerPaymentProfileId' => '28393490',
            'customerShippingAddressId' => '29366174',
            'validationMode' => 'liveMode'
        );
        $responseJson = '{
           "directResponse":"1,1,1,This transaction has been approved.,5Q8DGW,Y,2230582939,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,0.00,0.00,0.00,FALSE,none,6160655F3F4DF72144DCE15C0AEE15B1,,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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
        $authnet->validateCustomerPaymentProfileRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertTrue($authnet->isSuccessful());
        $this->assertFalse($authnet->isError());
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,5Q8DGW,Y,2230582939,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,0.00,0.00,0.00,FALSE,none,6160655F3F4DF72144DCE15C0AEE15B1,,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $authnet->directResponse);
    }
}