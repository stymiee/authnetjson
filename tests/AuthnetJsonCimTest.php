<?php

class AuthnetJsonCimTest extends PHPUnit_Framework_TestCase
{
    private $login;
    private $transactionKey;
    private $server;

    protected function setUp()
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
        $this->server         = \JohnConde\Authnet\AuthnetApiFactory::USE_UNIT_TEST_SERVER;
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

        $authnet = \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->createCustomerProfileRequest($request);

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

        $authnet = \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->createCustomerProfileRequest($request);

        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertEquals('E00039', $authnet->messages->message[0]->code);
        $this->assertEquals('A duplicate record with ID 20382791 already exists.', $authnet->messages->message[0]->text);
    }
}