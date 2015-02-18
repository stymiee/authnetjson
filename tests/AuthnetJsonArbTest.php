<?php

class AuthnetJsonArbTest extends PHPUnit_Framework_TestCase
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

    public function testARBCreateSubscriptionRequestSuccess()
    {
        $request = array(
            'refId' => 'Sample',
            'subscription' => array(
                'name' => 'Sample subscription',
                'paymentSchedule' => array(
                    'interval' => array(
                        'length' => '1',
                        'unit' => 'months'
                    ),
                    'startDate' => '2015-04-18',
                    'totalOccurrences' => '12',
                    'trialOccurrences' => '1'
                ),
                'amount' => '10.29',
                'trialAmount' => '0.00',
                'payment' => array(
                    'creditCard' => array(
                        'cardNumber' => '4111111111111111',
                        'expirationDate' => '2016-08'
                    )
                ),
                'billTo' => array(
                    'firstName' => 'John',
                    'lastName' => 'Smith'
                )
            )
        );
        $responseJson = '{
           "subscriptionId":"2341621",
           "refId":"Sample",
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
        $authnet->createTransactionRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('2341621', $authnet->subscriptionId);
        $this->assertEquals('Sample', $authnet->refId);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
    }

    public function testARBCreateSubscriptionRequestDuplicateRequestError()
    {
        $request = array(
            'refId' => 'Sample',
            'subscription' => array(
                'name' => 'Sample subscription',
                'paymentSchedule' => array(
                    'interval' => array(
                        'length' => '1',
                        'unit' => 'months'
                    ),
                    'startDate' => '2015-04-18',
                    'totalOccurrences' => '12',
                    'trialOccurrences' => '1'
                ),
                'amount' => '10.29',
                'trialAmount' => '0.00',
                'payment' => array(
                    'creditCard' => array(
                        'cardNumber' => '4111111111111111',
                        'expirationDate' => '2016-08'
                    )
                ),
                'billTo' => array(
                    'firstName' => 'John',
                    'lastName' => 'Smith'
                )
            )
        );
        $responseJson = '{
           "refId":"Sample",
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00012",
                    "text":"You have submitted a duplicate of Subscription 2341621. A duplicate subscription will not be created."
                 }
              ]
           }
        }';

        $authnet = \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->createTransactionRequest($request);

        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertEquals('Sample', $authnet->refId);
        $this->assertEquals('E00012', $authnet->messages->message[0]->code);
        $this->assertEquals('You have submitted a duplicate of Subscription 2341621. A duplicate subscription will not be created.', $authnet->messages->message[0]->text);
    }

    public function testARBCreateSubscriptionRequestInvalidStartDateError()
    {
        $request = array(
            'refId' => 'Sample',
            'subscription' => array(
                'name' => 'Sample subscription',
                'paymentSchedule' => array(
                    'interval' => array(
                        'length' => '1',
                        'unit' => 'months'
                    ),
                    'startDate' => '2015-04-18',
                    'totalOccurrences' => '12',
                    'trialOccurrences' => '1'
                ),
                'amount' => '10.29',
                'trialAmount' => '0.00',
                'payment' => array(
                    'creditCard' => array(
                        'cardNumber' => '4111111111111111',
                        'expirationDate' => '2016-08'
                    )
                ),
                'billTo' => array(
                    'firstName' => 'John',
                    'lastName' => 'Smith'
                )
            )
        );
        $responseJson = '{
           "refId":"Sample",
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00017",
                    "text":"Start Date must not occur before the submission date."
                 }
              ]
           }
        }';

        $authnet = \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->createTransactionRequest($request);

        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertEquals('Sample', $authnet->refId);
        $this->assertEquals('E00017', $authnet->messages->message[0]->code);
        $this->assertEquals('Start Date must not occur before the submission date.', $authnet->messages->message[0]->text);
    }

    public function testARBGetSubscriptionStatusRequestActive()
    {
        $request = array(
            'refId' => 'Sample',
            'subscriptionId' => '1207505'
        );
        $responseJson = '{
           "note":"Status with a capital \'S\' is obsolete.",
           "status":"active",
           "Status":"active",
           "statusSpecified":true,
           "StatusSpecified":true,
           "refId":"Sample",
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
        $authnet->ARBGetSubscriptionStatusRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('Sample', $authnet->refId);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('active', $authnet->status);
        $this->assertTrue($authnet->statusSpecified);
    }

    public function testARBGetSubscriptionStatusRequestCancelled()
    {
        $request = array(
            'refId' => 'Sample',
            'subscriptionId' => '1207505'
        );
        $responseJson = '{
           "note":"Status with a capital \'S\' is obsolete.",
           "status":"canceled",
           "Status":"canceled",
           "statusSpecified":true,
           "StatusSpecified":true,
           "refId":"Sample",
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
        $authnet->ARBGetSubscriptionStatusRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('Sample', $authnet->refId);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('canceled', $authnet->status);
        $this->assertTrue($authnet->statusSpecified);
    }

    public function testARBCancelSubscriptionRequestSuccess()
    {
        $request = array(
            'refId' => 'Sample',
            'subscriptionId' => '1207505'
        );
        $responseJson = '{
           "refId":"Sample",
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
        $authnet->ARBGetSubscriptionStatusRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('Sample', $authnet->refId);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
    }

    public function testARBCancelSubscriptionRequestAlreadyCancelled()
    {
        $request = array(
            'refId' => 'Sample',
            'subscriptionId' => '1207505'
        );
        $responseJson = '{
           "refId":"Sample",
           "messages":{
              "resultCode":"Ok",
              "message":[
                 {
                    "code":"I00002",
                    "text":"The subscription has already been canceled."
                 }
              ]
           }
        }';

        $authnet = \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->ARBGetSubscriptionStatusRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('Sample', $authnet->refId);
        $this->assertEquals('I00002', $authnet->messages->message[0]->code);
        $this->assertEquals('The subscription has already been canceled.', $authnet->messages->message[0]->text);
    }

    public function testARBUpdateSubscriptionRequestSuccess()
    {
        $request = array(
            'refId' => 'Sample',
            'subscriptionId' => '2342682',
            'subscription' => array(
                'payment' => array(
                    'creditCard' => array(
                        'cardNumber' => '6011000000000012',
                        'expirationDate' => '2016-08'
                    ),
                ),
            ),
        );
        $responseJson = '{
           "refId":"Sample",
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
        $authnet->ARBGetSubscriptionStatusRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('Sample', $authnet->refId);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
    }

    public function testARBUpdateSubscriptionRequestError()
    {
        $request = array(
            'refId' => 'Sample',
            'subscriptionId' => '2342682',
            'subscription' => array(
                'payment' => array(
                    'creditCard' => array(
                        'cardNumber' => '6011000000000012',
                        'expirationDate' => '2016-08'
                    ),
                ),
            ),
        );
        $responseJson = '{
           "refId":"Sample",
           "messages":{
              "resultCode":"Error",
              "message":[
                 {
                    "code":"E00037",
                    "text":"Subscriptions that are canceled cannot be updated."
                 }
              ]
           }
        }';

        $authnet = \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->ARBGetSubscriptionStatusRequest($request);

        $this->assertEquals('Error', $authnet->messages->resultCode);
        $this->assertEquals('Sample', $authnet->refId);
        $this->assertEquals('E00037', $authnet->messages->message[0]->code);
        $this->assertEquals('Subscriptions that are canceled cannot be updated.', $authnet->messages->message[0]->text);
    }
}