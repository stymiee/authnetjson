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
    public function testCreateCustomerProfileRequestSuccess()
    {
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerProfileRequest($requestJson);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertEquals('31390172', $response->customerProfileId);
        $this->assertEquals('28393490', $response->customerPaymentProfileIdList[0]);
        $this->assertEquals('29366174', $response->customerShippingAddressIdList[0]);
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateCustomerProfileRequestDuplicateRecordError()
    {
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerProfileRequest($requestJson);

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertEquals('E00039', $response->messages->message[0]->code);
        $this->assertEquals('A duplicate record with ID 20382791 already exists.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateCustomerPaymentProfileRequest()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerPaymentProfileRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('28821903', $response->customerPaymentProfileId);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateCustomerProfileTransactionAuthCaptureRequest()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerProfileTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $response->directResponse);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateCustomerProfileTransactionRequestAuthCaptureError()
    {
        $requestJson = array (
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerProfileTransactionRequest($requestJson);

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertEquals('E00040', $response->messages->message[0]->code);
        $this->assertEquals('Customer Profile ID or Customer Payment Profile ID not found.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateCustomerProfileTransactionRequestAuthOnly()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerProfileTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,KF2IM6,Y,2230582323,INV000001,description of transaction,10.95,CC,auth_only,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,FALSE,PONUM000001,15D36F54160C246186DA774FE261646B,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $response->directResponse);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateCustomerProfileTransactionRequestCaptureOnly()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerProfileTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,000000,P,2230582335,INV000001,description of transaction,10.95,CC,capture_only,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,FALSE,PONUM000001,0DAC5007786DEA5A5EB02C0C56A68F87,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $response->directResponse);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateCustomerProfileTransactionRequestPriorAuthCapture()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerProfileTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,S9WA0V,P,2230582347,INV000001,,10.95,CC,prior_auth_capture,12345,,,,,,,12345,,,,,,,,,,,,,1.00,,2.00,,,66E86622C893D1DBBC47D1B314CB57E2,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $response->directResponse);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateCustomerProfileTransactionRequestPriorAuthCaptureError()
    {
        $requestJson = array (
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerProfileTransactionRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('E00027', $response->messages->message[0]->code);
        $this->assertEquals('The transaction cannot be found.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateCustomerProfileTransactionRequestRefund()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerProfileTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,,P,2230582363,INV000001,description of transaction,10.95,CC,credit,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,1.00,,2.00,,PONUM000001,5E1CD1DFC373ACF8F084F5D220945BA0,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $response->directResponse);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateCustomerProfileTransactionRequestRefundError()
    {
        $requestJson = array (
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerProfileTransactionRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('E00027', $response->messages->message[0]->code);
        $this->assertEquals('The referenced transaction does not meet the criteria for issuing a credit.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateCustomerProfileTransactionRequestVoid()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerProfileTransactionRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,OWW0UU,P,2230582868,INV000001,,0.00,CC,void,12345,,,,,,,12345,,,,,,,,,,,,,,,,,,0C7394DFC38A5BDC5737A354CE67B421,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $response->directResponse);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateCustomerShippingAddressRequest()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createCustomerShippingAddressRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('29870028', $response->customerAddressId);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testUpdateCustomerProfileRequest()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->updateCustomerProfileRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testUpdateCustomerProfileRequestError()
    {
        $requestJson = array (
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->updateCustomerProfileRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('E00040', $response->messages->message[0]->code);
        $this->assertEquals('The record cannot be found.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testUpdateCustomerPaymentProfileRequest()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->updateCustomerPaymentProfileRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testUpdateCustomerShippingAddressRequest()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->updateCustomerShippingAddressRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testUpdateCustomerShippingAddressRequestError()
    {
        $requestJson = array (
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->updateCustomerShippingAddressRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('E00040', $response->messages->message[0]->code);
        $this->assertEquals('Cannot find the specified shipping address.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testUpdateSplitTenderGroupRequest()
    {
        $requestJson = array (
            'splitTenderId' => '123456',
            'splitTenderStatus' => 'voided'
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->updateSplitTenderGroupRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testUpdateSplitTenderGroupRequestError()
    {
        $requestJson = array (
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->updateSplitTenderGroupRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('E00027', $response->messages->message[0]->code);
        $this->assertEquals('The specified SplitTenderID is invalid.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testDeleteCustomerProfileRequest()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->deleteCustomerProfileRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testDeleteCustomerProfileRequestError()
    {
        $requestJson = array (
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->deleteCustomerProfileRequest($requestJson);

        $this->assertEquals('Error', $response->messages->resultCode);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
        $this->assertEquals('E00040', $response->messages->message[0]->code);
        $this->assertEquals('The record cannot be found.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testDeleteCustomerPaymentProfileRequest()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->deleteCustomerPaymentProfileRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testDeleteCustomerShippingAddressRequest()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->deleteCustomerShippingAddressRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testValidateCustomerPaymentProfileRequest()
    {
        $requestJson = array(
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->validateCustomerPaymentProfileRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('1,1,1,This transaction has been approved.,5Q8DGW,Y,2230582939,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,0.00,0.00,0.00,FALSE,none,6160655F3F4DF72144DCE15C0AEE15B1,,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174', $response->directResponse);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetCustomerPaymentProfileRequest()
    {
        $requestJson = array(
            'customerProfileId' => '31390172',
            'customerPaymentProfileId' => '28393490'
        );
        $responseJson = '{
           "paymentProfile":{
              "customerPaymentProfileId":"28393490",
              "payment":{
                 "creditCard":{
                    "cardNumber":"XXXX1111",
                    "expirationDate":"XXXX"
                 }
              },
              "customerTypeSpecified":false,
              "billTo":{
                 "phoneNumber":"800-555-1234",
                 "firstName":"John",
                 "lastName":"Smith",
                 "address":"123 Main Street",
                 "city":"Townsville",
                 "state":"NJ",
                 "zip":"12345"
              }
           },
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
        $response = $request->getCustomerPaymentProfileRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('28393490', $response->paymentProfile->customerPaymentProfileId);
        $this->assertEquals('XXXX1111', $response->paymentProfile->payment->creditCard->cardNumber);
        $this->assertEquals('XXXX', $response->paymentProfile->payment->creditCard->expirationDate);
        $this->assertFalse($response->paymentProfile->customerTypeSpecified);
        $this->assertEquals('800-555-1234', $response->paymentProfile->billTo->phoneNumber);
        $this->assertEquals('John', $response->paymentProfile->billTo->firstName);
        $this->assertEquals('Smith', $response->paymentProfile->billTo->lastName);
        $this->assertEquals('123 Main Street', $response->paymentProfile->billTo->address);
        $this->assertEquals('Townsville', $response->paymentProfile->billTo->city);
        $this->assertEquals('NJ', $response->paymentProfile->billTo->state);
        $this->assertEquals('12345', $response->paymentProfile->billTo->zip);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetCustomerProfileIdsRequest()
    {
        $responseJson = '{
           "ids":[
              "20320494",
              "20320495",
              "20320496",
              "20320497",
              "20320499",
              "20382791",
              "20522161",
              "20522247",
              "20522344",
              "20522529",
              "20529466",
              "20532466",
              "20533743",
              "20533892",
              "20631798",
              "21176851",
              "21267776",
              "21267786",
              "21268552",
              "21268866",
              "21323330",
              "21387453",
              "21452273",
              "21503525",
              "21507048",
              "21520223",
              "21533869",
              "21630064",
              "21631076",
              "21644324",
              "21755205",
              "21783775",
              "22820980",
              "22853636",
              "22912790",
              "22913090",
              "23896146",
              "23942782",
              "24353242",
              "24415694",
              "24431080",
              "24873651",
              "24874921",
              "24875645",
              "25139838",
              "25286149",
              "25287624",
              "25697926",
              "25933750",
              "26564070",
              "26564773",
              "26583007",
              "26585538",
              "26585555",
              "26585578",
              "26586275",
              "26648484",
              "26648913",
              "27389717",
              "27667711",
              "27713150",
              "27984649",
              "27984720",
              "27984879",
              "28012856",
              "28023333",
              "28023355",
              "28023366",
              "28023374",
              "28421203",
              "28421294",
              "28440218",
              "28440239",
              "28440287",
              "28440368",
              "28473202",
              "28473492",
              "28474805",
              "28596453",
              "28705962",
              "28722134",
              "28774792",
              "28907593",
              "30582495",
              "30582501",
              "31390172"
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->getCustomerProfileIdsRequest();

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertTrue(is_array($response->ids));
        $this->assertEquals('20320494', $response->ids[0]);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetCustomerProfileRequest()
    {
        $requestJson = array(
            'customerProfileId' => '31390172'
        );
        $responseJson = '{
           "profile":{
              "paymentProfiles":[
                 {
                    "customerPaymentProfileId":"28393490",
                    "payment":{
                       "creditCard":{
                          "cardNumber":"XXXX1111",
                          "expirationDate":"XXXX"
                       }
                    },
                    "customerTypeSpecified":false,
                    "billTo":{
                       "phoneNumber":"800-555-1234",
                       "firstName":"John",
                       "lastName":"Smith",
                       "address":"123 Main Street",
                       "city":"Townsville",
                       "state":"NJ",
                       "zip":"12345"
                    }
                 }
              ],
              "shipToList":[
                 {
                    "customerAddressId":"29366174",
                    "phoneNumber":"800-555-1234",
                    "firstName":"John",
                    "lastName":"Smith",
                    "address":"123 Main Street",
                    "city":"Townsville",
                    "state":"NJ",
                    "zip":"12345"
                 },
                 {
                    "customerAddressId":"29870028",
                    "phoneNumber":"800-555-1234",
                    "faxNumber":"800-555-1234",
                    "firstName":"John",
                    "lastName":"Doe",
                    "company":"",
                    "address":"123 Main St.",
                    "city":"Bellevue",
                    "state":"WA",
                    "zip":"98004",
                    "country":"USA"
                 }
              ],
              "customerProfileId":"31390172",
              "merchantCustomerId":"12345",
              "email":"user@example.com"
           },
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
        $response = $request->getCustomerProfileRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('28393490', $response->profile->paymentProfiles[0]->customerPaymentProfileId);
        $this->assertEquals('XXXX1111', $response->profile->paymentProfiles[0]->payment->creditCard->cardNumber);
        $this->assertEquals('XXXX', $response->profile->paymentProfiles[0]->payment->creditCard->expirationDate);
        $this->assertFalse($response->profile->paymentProfiles[0]->customerTypeSpecified);
        $this->assertEquals('800-555-1234', $response->profile->paymentProfiles[0]->billTo->phoneNumber);
        $this->assertEquals('John', $response->profile->paymentProfiles[0]->billTo->firstName);
        $this->assertEquals('Smith', $response->profile->paymentProfiles[0]->billTo->lastName);
        $this->assertEquals('123 Main Street', $response->profile->paymentProfiles[0]->billTo->address);
        $this->assertEquals('Townsville', $response->profile->paymentProfiles[0]->billTo->city);
        $this->assertEquals('NJ', $response->profile->paymentProfiles[0]->billTo->state);
        $this->assertEquals('12345', $response->profile->paymentProfiles[0]->billTo->zip);
        $this->assertEquals('29366174', $response->profile->shipToList[0]->customerAddressId);
        $this->assertEquals('800-555-1234', $response->profile->shipToList[0]->phoneNumber);
        $this->assertEquals('John', $response->profile->shipToList[0]->firstName);
        $this->assertEquals('Smith', $response->profile->shipToList[0]->lastName);
        $this->assertEquals('123 Main Street', $response->profile->shipToList[0]->address);
        $this->assertEquals('Townsville', $response->profile->shipToList[0]->city);
        $this->assertEquals('NJ', $response->profile->shipToList[0]->state);
        $this->assertEquals('12345', $response->profile->shipToList[0]->zip);
        $this->assertEquals('29870028', $response->profile->shipToList[1]->customerAddressId);
        $this->assertEquals('800-555-1234', $response->profile->shipToList[1]->phoneNumber);
        $this->assertEquals('800-555-1234', $response->profile->shipToList[1]->faxNumber);
        $this->assertEquals('John', $response->profile->shipToList[1]->firstName);
        $this->assertEquals('Doe', $response->profile->shipToList[1]->lastName);
        $this->assertEquals('', $response->profile->shipToList[1]->company);
        $this->assertEquals('123 Main St.', $response->profile->shipToList[1]->address);
        $this->assertEquals('Bellevue', $response->profile->shipToList[1]->city);
        $this->assertEquals('WA', $response->profile->shipToList[1]->state);
        $this->assertEquals('98004', $response->profile->shipToList[1]->zip);
        $this->assertEquals('USA', $response->profile->shipToList[1]->country);
        $this->assertEquals('31390172', $response->profile->customerProfileId);
        $this->assertEquals('12345', $response->profile->merchantCustomerId);
        $this->assertEquals('user@example.com', $response->profile->email);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetCustomerShippingAddressRequest()
    {
        $requestJson = array(
            'customerProfileId' => '31390172',
            'customerAddressId' => '29366174'
        );
        $responseJson = '{
           "address":{
              "customerAddressId":"29366174",
              "phoneNumber":"800-555-1234",
              "firstName":"John",
              "lastName":"Smith",
              "address":"123 Main Street",
              "city":"Townsville",
              "state":"NJ",
              "zip":"12345"
           },
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
        $response = $request->getCustomerShippingAddressRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('29366174', $response->address->customerAddressId);
        $this->assertEquals('800-555-1234', $response->address->phoneNumber);
        $this->assertEquals('John', $response->address->firstName);
        $this->assertEquals('Smith', $response->address->lastName);
        $this->assertEquals('123 Main Street', $response->address->address);
        $this->assertEquals('Townsville', $response->address->city);
        $this->assertEquals('NJ', $response->address->state);
        $this->assertEquals('12345', $response->address->zip);
    }


    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetHostedProfilePageRequest()
    {
        $requestJson = array(
            'customerProfileId' => '31390172',
            'hostedProfileSettings' => array(
                'setting' => array(
                    'settingName' => 'hostedProfileReturnUrl',
                    'settingValue' => 'https://blah.com/blah/',
                ),
                'setting' => array(
                    'settingName' => 'hostedProfileReturnUrlText',
                    'settingValue' => 'Continue to blah.',
                ),
                'setting' => array(
                    'settingName' => 'hostedProfilePageBorderVisible',
                    'settingValue' => 'true',
                )
            )
        );
        $responseJson = '{
           "token":"Mvwo9mTx2vS332eCFY3rFzh/x1x64henm7rppLYQxd2cOzNpw+bfp1ZTVKvu98XSIvL9VIEB65mCFtzchN/pFKBdBA0daBukS27pWYxZuo6QpBUpz2p6zLENX8qH9wCcAw6EJr0MZkNttPW6b+Iw9eKfcBtJayq6kdNm9m1ywANHsg9xME4qUccBXnY2cCf3kLaaLNJhhiNxJmcboKNlDn5HtIQ/wcRnxB4YbqddTN8=",
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
        $response = $request->getHostedProfilePageRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('Mvwo9mTx2vS332eCFY3rFzh/x1x64henm7rppLYQxd2cOzNpw+bfp1ZTVKvu98XSIvL9VIEB65mCFtzchN/pFKBdBA0daBukS27pWYxZuo6QpBUpz2p6zLENX8qH9wCcAw6EJr0MZkNttPW6b+Iw9eKfcBtJayq6kdNm9m1ywANHsg9xME4qUccBXnY2cCf3kLaaLNJhhiNxJmcboKNlDn5HtIQ/wcRnxB4YbqddTN8=', $response->token);
    }
}