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

class AuthnetJsonReportingTest extends \PHPUnit_Framework_TestCase
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
    public function testGetUnsettledTransactionListRequestSuccess()
    {
        $responseJson = '{
           "transactions":[
              {
                 "transId":"2228546203",
                 "submitTimeUTC":"2015-02-16T03:34:43Z",
                 "submitTimeLocal":"2015-02-15T20:34:43",
                 "transactionStatus":"authorizedPendingCapture",
                 "invoiceNumber":"1324567890",
                 "firstName":"Ellen",
                 "lastName":"Johnson",
                 "accountType":"MasterCard",
                 "accountNumber":"XXXX0015",
                 "settleAmount":5.00,
                 "marketType":"eCommerce",
                 "product":"Card Not Present",
                 "hasReturnedItemsSpecified":false
              },
              {
                 "transId":"2228546083",
                 "submitTimeUTC":"2015-02-16T03:31:42Z",
                 "submitTimeLocal":"2015-02-15T20:31:42",
                 "transactionStatus":"authorizedPendingCapture",
                 "invoiceNumber":"1324567890",
                 "firstName":"Ellen",
                 "lastName":"Johnson",
                 "accountType":"MasterCard",
                 "accountNumber":"XXXX0015",
                 "settleAmount":5.00,
                 "marketType":"eCommerce",
                 "product":"Card Not Present",
                 "hasReturnedItemsSpecified":false
              },
              {
                 "transId":"2228545865",
                 "submitTimeUTC":"2015-02-16T03:25:00Z",
                 "submitTimeLocal":"2015-02-15T20:25:00",
                 "transactionStatus":"authorizedPendingCapture",
                 "invoiceNumber":"1324567890",
                 "firstName":"Ellen",
                 "lastName":"Johnson",
                 "accountType":"MasterCard",
                 "accountNumber":"XXXX0015",
                 "settleAmount":5.00,
                 "marketType":"eCommerce",
                 "product":"Card Not Present",
                 "hasReturnedItemsSpecified":false
              }
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
        $response = $request->getUnsettledTransactionListRequest();

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertTrue(is_array($response->transactions));
        $this->assertEquals('2228546203', $response->transactions[0]->transId);
        $this->assertEquals('2015-02-16T03:34:43Z', $response->transactions[0]->submitTimeUTC);
        $this->assertEquals('2015-02-15T20:34:43', $response->transactions[0]->submitTimeLocal);
        $this->assertEquals('authorizedPendingCapture', $response->transactions[0]->transactionStatus);
        $this->assertEquals('1324567890', $response->transactions[0]->invoiceNumber);
        $this->assertEquals('Ellen', $response->transactions[0]->firstName);
        $this->assertEquals('Johnson', $response->transactions[0]->lastName);
        $this->assertEquals('MasterCard', $response->transactions[0]->accountType);
        $this->assertEquals('XXXX0015', $response->transactions[0]->accountNumber);
        $this->assertEquals('5.00', $response->transactions[0]->settleAmount);
        $this->assertEquals('eCommerce', $response->transactions[0]->marketType);
        $this->assertEquals('Card Not Present', $response->transactions[0]->product);
        $this->assertFalse($response->transactions[0]->hasReturnedItemsSpecified);
        $this->assertEquals('2228546083', $response->transactions[1]->transId);
        $this->assertEquals('2015-02-16T03:31:42Z', $response->transactions[1]->submitTimeUTC);
        $this->assertEquals('2015-02-15T20:31:42', $response->transactions[1]->submitTimeLocal);
        $this->assertEquals('authorizedPendingCapture', $response->transactions[1]->transactionStatus);
        $this->assertEquals('1324567890', $response->transactions[1]->invoiceNumber);
        $this->assertEquals('Ellen', $response->transactions[1]->firstName);
        $this->assertEquals('Johnson', $response->transactions[1]->lastName);
        $this->assertEquals('MasterCard', $response->transactions[1]->accountType);
        $this->assertEquals('XXXX0015', $response->transactions[1]->accountNumber);
        $this->assertEquals('5.00', $response->transactions[1]->settleAmount);
        $this->assertEquals('eCommerce', $response->transactions[1]->marketType);
        $this->assertEquals('Card Not Present', $response->transactions[1]->product);
        $this->assertFalse($response->transactions[1]->hasReturnedItemsSpecified);
        $this->assertEquals('2228545865', $response->transactions[2]->transId);
        $this->assertEquals('2015-02-16T03:25:00Z', $response->transactions[2]->submitTimeUTC);
        $this->assertEquals('2015-02-15T20:25:00', $response->transactions[2]->submitTimeLocal);
        $this->assertEquals('authorizedPendingCapture', $response->transactions[2]->transactionStatus);
        $this->assertEquals('1324567890', $response->transactions[2]->invoiceNumber);
        $this->assertEquals('Ellen', $response->transactions[2]->firstName);
        $this->assertEquals('Johnson', $response->transactions[2]->lastName);
        $this->assertEquals('MasterCard', $response->transactions[2]->accountType);
        $this->assertEquals('XXXX0015', $response->transactions[2]->accountNumber);
        $this->assertEquals('5.00', $response->transactions[2]->settleAmount);
        $this->assertEquals('eCommerce', $response->transactions[2]->marketType);
        $this->assertEquals('Card Not Present', $response->transactions[2]->product);
        $this->assertFalse($response->transactions[2]->hasReturnedItemsSpecified);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetTransactionListRequestSuccess()
    {
        $requestJson = array(
            'batchId' => '1221577'
        );
        $responseJson = '{
           "transactions":[
              {
                 "transId":"2162566217",
                 "submitTimeUTC":"2011-09-01T16:30:49Z",
                 "submitTimeLocal":"2011-09-01T10:30:49",
                 "transactionStatus":"settledSuccessfully",
                 "invoiceNumber":"60",
                 "firstName":"Matteo",
                 "lastName":"Bignotti",
                 "accountType":"MasterCard",
                 "accountNumber":"XXXX4444",
                 "settleAmount":1018.88,
                 "marketType":"eCommerce",
                 "product":"Card Not Present",
                 "hasReturnedItemsSpecified":false
              }
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
        $response = $request->getTransactionListRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertTrue(is_array($response->transactions));
        $this->assertEquals('2162566217', $response->transactions[0]->transId);
        $this->assertEquals('2011-09-01T16:30:49Z', $response->transactions[0]->submitTimeUTC);
        $this->assertEquals('2011-09-01T10:30:49', $response->transactions[0]->submitTimeLocal);
        $this->assertEquals('settledSuccessfully', $response->transactions[0]->transactionStatus);
        $this->assertEquals('60', $response->transactions[0]->invoiceNumber);
        $this->assertEquals('Matteo', $response->transactions[0]->firstName);
        $this->assertEquals('Bignotti', $response->transactions[0]->lastName);
        $this->assertEquals('MasterCard', $response->transactions[0]->accountType);
        $this->assertEquals('XXXX4444', $response->transactions[0]->accountNumber);
        $this->assertEquals('1018.88', $response->transactions[0]->settleAmount);
        $this->assertEquals('eCommerce', $response->transactions[0]->marketType);
        $this->assertEquals('Card Not Present', $response->transactions[0]->product);
        $this->assertFalse($response->transactions[0]->hasReturnedItemsSpecified);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetSettledBatchListRequestSuccess()
    {
        $requestJson = array(
            'includeStatistics'   => 'true',
            'firstSettlementDate' => '2015-01-01T08:15:30',
            'lastSettlementDate'  => '2015-01-30T08:15:30'
        );
        $responseJson = '{
           "batchList":[
              {
                 "batchId":"3990061",
                 "settlementTimeUTC":"2015-01-02T08:40:29Z",
                 "settlementTimeUTCSpecified":true,
                 "settlementTimeLocal":"2015-01-02T01:40:29",
                 "settlementTimeLocalSpecified":true,
                 "settlementState":"settledSuccessfully",
                 "paymentMethod":"creditCard",
                 "marketType":"eCommerce",
                 "product":"Card Not Present",
                 "statistics":[
                    {
                       "accountType":"MasterCard",
                       "chargeAmount":98.00,
                       "chargeCount":2,
                       "refundAmount":0.00,
                       "refundCount":0,
                       "voidCount":0,
                       "declineCount":0,
                       "errorCount":0,
                       "returnedItemAmountSpecified":false,
                       "returnedItemCountSpecified":false,
                       "chargebackAmountSpecified":false,
                       "chargebackCountSpecified":false,
                       "correctionNoticeCountSpecified":false,
                       "chargeChargeBackAmountSpecified":false,
                       "chargeChargeBackCountSpecified":false,
                       "refundChargeBackAmountSpecified":false,
                       "refundChargeBackCountSpecified":false,
                       "chargeReturnedItemsAmountSpecified":false,
                       "chargeReturnedItemsCountSpecified":false,
                       "refundReturnedItemsAmountSpecified":false,
                       "refundReturnedItemsCountSpecified":false
                    },
                    {
                       "accountType":"Visa",
                       "chargeAmount":2255.50,
                       "chargeCount":4,
                       "refundAmount":0.00,
                       "refundCount":0,
                       "voidCount":0,
                       "declineCount":0,
                       "errorCount":0,
                       "returnedItemAmountSpecified":false,
                       "returnedItemCountSpecified":false,
                       "chargebackAmountSpecified":false,
                       "chargebackCountSpecified":false,
                       "correctionNoticeCountSpecified":false,
                       "chargeChargeBackAmountSpecified":false,
                       "chargeChargeBackCountSpecified":false,
                       "refundChargeBackAmountSpecified":false,
                       "refundChargeBackCountSpecified":false,
                       "chargeReturnedItemsAmountSpecified":false,
                       "chargeReturnedItemsCountSpecified":false,
                       "refundReturnedItemsAmountSpecified":false,
                       "refundReturnedItemsCountSpecified":false
                    }
                 ]
              },
              {
                 "batchId":"3990090",
                 "settlementTimeUTC":"2015-01-02T08:55:35Z",
                 "settlementTimeUTCSpecified":true,
                 "settlementTimeLocal":"2015-01-02T01:55:35",
                 "settlementTimeLocalSpecified":true,
                 "settlementState":"settledSuccessfully",
                 "paymentMethod":"creditCard",
                 "marketType":"eCommerce",
                 "product":"Card Not Present",
                 "statistics":[
                    {
                       "accountType":"MasterCard",
                       "chargeAmount":19.95,
                       "chargeCount":1,
                       "refundAmount":0.00,
                       "refundCount":0,
                       "voidCount":0,
                       "declineCount":0,
                       "errorCount":0,
                       "returnedItemAmountSpecified":false,
                       "returnedItemCountSpecified":false,
                       "chargebackAmountSpecified":false,
                       "chargebackCountSpecified":false,
                       "correctionNoticeCountSpecified":false,
                       "chargeChargeBackAmountSpecified":false,
                       "chargeChargeBackCountSpecified":false,
                       "refundChargeBackAmountSpecified":false,
                       "refundChargeBackCountSpecified":false,
                       "chargeReturnedItemsAmountSpecified":false,
                       "chargeReturnedItemsCountSpecified":false,
                       "refundReturnedItemsAmountSpecified":false,
                       "refundReturnedItemsCountSpecified":false
                    },
                    {
                       "accountType":"Visa",
                       "chargeAmount":5.00,
                       "chargeCount":1,
                       "refundAmount":0.00,
                       "refundCount":0,
                       "voidCount":0,
                       "declineCount":0,
                       "errorCount":0,
                       "returnedItemAmountSpecified":false,
                       "returnedItemCountSpecified":false,
                       "chargebackAmountSpecified":false,
                       "chargebackCountSpecified":false,
                       "correctionNoticeCountSpecified":false,
                       "chargeChargeBackAmountSpecified":false,
                       "chargeChargeBackCountSpecified":false,
                       "refundChargeBackAmountSpecified":false,
                       "refundChargeBackCountSpecified":false,
                       "chargeReturnedItemsAmountSpecified":false,
                       "chargeReturnedItemsCountSpecified":false,
                       "refundReturnedItemsAmountSpecified":false,
                       "refundReturnedItemsCountSpecified":false
                    }
                 ]
              }
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
        $response = $request->getSettledBatchListRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertTrue(is_array($response->batchList));
        $this->assertEquals('3990061', $response->batchList[0]->batchId);
        $this->assertEquals('2015-01-02T08:40:29Z', $response->batchList[0]->settlementTimeUTC);
        $this->assertTrue($response->batchList[0]->settlementTimeUTCSpecified);
        $this->assertEquals('2015-01-02T01:40:29', $response->batchList[0]->settlementTimeLocal);
        $this->assertTrue($response->batchList[0]->settlementTimeLocalSpecified);
        $this->assertEquals('settledSuccessfully', $response->batchList[0]->settlementState);
        $this->assertEquals('creditCard', $response->batchList[0]->paymentMethod);
        $this->assertEquals('eCommerce', $response->batchList[0]->marketType);
        $this->assertEquals('Card Not Present', $response->batchList[0]->product);
        $this->assertEquals('3990090', $response->batchList[1]->batchId);
        $this->assertEquals('2015-01-02T08:55:35Z', $response->batchList[1]->settlementTimeUTC);
        $this->assertTrue($response->batchList[1]->settlementTimeUTCSpecified);
        $this->assertEquals('2015-01-02T01:55:35', $response->batchList[1]->settlementTimeLocal);
        $this->assertTrue($response->batchList[1]->settlementTimeLocalSpecified);
        $this->assertEquals('settledSuccessfully', $response->batchList[1]->settlementState);
        $this->assertEquals('creditCard', $response->batchList[1]->paymentMethod);
        $this->assertEquals('eCommerce', $response->batchList[1]->marketType);
        $this->assertEquals('Card Not Present', $response->batchList[1]->product);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetSettledBatchListRequestNoRecords()
    {
        $requestJson = array(
            'includeStatistics'   => 'true',
            'firstSettlementDate' => '2020-01-01T08:15:30',
            'lastSettlementDate'  => '2020-01-30T08:15:30'
        );
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->getSettledBatchListRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertEquals('I00004', $response->messages->message[0]->code);
        $this->assertEquals('No records found.', $response->messages->message[0]->text);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetBatchStatisticsRequestSuccess()
    {
        $requestJson = array(
            'batchId' => '1221577'
        );
        $responseJson = '{
           "batch":{
              "batchId":"1221577",
              "settlementTimeUTC":"2011-09-01T16:38:54Z",
              "settlementTimeUTCSpecified":true,
              "settlementTimeLocal":"2011-09-01T10:38:54",
              "settlementTimeLocalSpecified":true,
              "settlementState":"settledSuccessfully",
              "paymentMethod":"creditCard",
              "statistics":[
                 {
                    "accountType":"MasterCard",
                    "chargeAmount":1018.88,
                    "chargeCount":1,
                    "refundAmount":0.00,
                    "refundCount":0,
                    "voidCount":0,
                    "declineCount":0,
                    "errorCount":0,
                    "returnedItemAmountSpecified":false,
                    "returnedItemCountSpecified":false,
                    "chargebackAmountSpecified":false,
                    "chargebackCountSpecified":false,
                    "correctionNoticeCountSpecified":false,
                    "chargeChargeBackAmountSpecified":false,
                    "chargeChargeBackCountSpecified":false,
                    "refundChargeBackAmountSpecified":false,
                    "refundChargeBackCountSpecified":false,
                    "chargeReturnedItemsAmountSpecified":false,
                    "chargeReturnedItemsCountSpecified":false,
                    "refundReturnedItemsAmountSpecified":false,
                    "refundReturnedItemsCountSpecified":false
                 }
              ]
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
        $response = $request->getBatchStatisticsRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('1221577', $response->batch->batchId);
        $this->assertEquals('2011-09-01T16:38:54Z', $response->batch->settlementTimeUTC);
        $this->assertTrue($response->batch->settlementTimeUTCSpecified);
        $this->assertEquals('2011-09-01T10:38:54', $response->batch->settlementTimeLocal);
        $this->assertTrue($response->batch->settlementTimeLocalSpecified);
        $this->assertEquals('settledSuccessfully', $response->batch->settlementState);
        $this->assertEquals('creditCard', $response->batch->paymentMethod);
        $this->assertTrue(is_array($response->batch->statistics));
        $this->assertEquals('MasterCard', $response->batch->statistics[0]->accountType);
        $this->assertEquals(1018.88, $response->batch->statistics[0]->chargeAmount);
        $this->assertEquals(1, $response->batch->statistics[0]->chargeCount);
        $this->assertEquals(0.00, $response->batch->statistics[0]->refundAmount);
        $this->assertEquals(0, $response->batch->statistics[0]->refundCount);
        $this->assertEquals(0, $response->batch->statistics[0]->voidCount);
        $this->assertEquals(0, $response->batch->statistics[0]->declineCount);
        $this->assertEquals(0, $response->batch->statistics[0]->errorCount);
        $this->assertFalse($response->batch->statistics[0]->returnedItemAmountSpecified);
        $this->assertFalse($response->batch->statistics[0]->returnedItemCountSpecified);
        $this->assertFalse($response->batch->statistics[0]->chargebackAmountSpecified);
        $this->assertFalse($response->batch->statistics[0]->chargebackCountSpecified);
        $this->assertFalse($response->batch->statistics[0]->correctionNoticeCountSpecified);
        $this->assertFalse($response->batch->statistics[0]->chargeChargeBackAmountSpecified);
        $this->assertFalse($response->batch->statistics[0]->chargeChargeBackCountSpecified);
        $this->assertFalse($response->batch->statistics[0]->refundChargeBackAmountSpecified);
        $this->assertFalse($response->batch->statistics[0]->refundChargeBackCountSpecified);
        $this->assertFalse($response->batch->statistics[0]->chargeReturnedItemsAmountSpecified);
        $this->assertFalse($response->batch->statistics[0]->chargeReturnedItemsCountSpecified);
        $this->assertFalse($response->batch->statistics[0]->refundReturnedItemsAmountSpecified);
        $this->assertFalse($response->batch->statistics[0]->refundReturnedItemsCountSpecified);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetBatchStatisticsRequestNoRecords()
    {
        $requestJson = array(
            'batchId' => '999999999'
        );
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

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->getBatchStatisticsRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertEquals('I00004', $response->messages->message[0]->code);
        $this->assertEquals('No records found.', $response->messages->message[0]->text);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetJsonRequest::process()

     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getJsonApiHandler
     * @uses              \JohnConde\Authnet\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetTransactionDetailsRequestSuccess()
    {
        $requestJson = array(
            'transId' => '2162566217'
        );
        $responseJson = '{
           "transaction":{
              "transId":"2162566217",
              "submitTimeUTC":"2011-09-01T16:30:49.39Z",
              "submitTimeLocal":"2011-09-01T10:30:49.39",
              "transactionType":"authCaptureTransaction",
              "transactionStatus":"settledSuccessfully",
              "responseCode":1,
              "responseReasonCode":1,
              "responseReasonDescription":"Approval",
              "authCode":"JPG9DJ",
              "AVSResponse":"Y",
              "batch":{
                 "batchId":"1221577",
                 "settlementTimeUTC":"2011-09-01T16:38:54.52Z",
                 "settlementTimeUTCSpecified":true,
                 "settlementTimeLocal":"2011-09-01T10:38:54.52",
                 "settlementTimeLocalSpecified":true,
                 "settlementState":"settledSuccessfully"
              },
              "order":{
                 "invoiceNumber":"60",
                 "description":"Auto-charge for Invoice #60"
              },
              "requestedAmountSpecified":false,
              "authAmount":1018.88,
              "settleAmount":1018.88,
              "prepaidBalanceRemainingSpecified":false,
              "taxExempt":false,
              "taxExemptSpecified":true,
              "payment":{
                 "creditCard":{
                    "cardNumber":"XXXX4444",
                    "expirationDate":"XXXX",
                    "cardType":"MasterCard"
                 }
              },
              "customer":{
                 "typeSpecified":false,
                 "id":"4"
              },
              "billTo":{
                 "phoneNumber":"(619) 274-0494",
                 "firstName":"Matteo",
                 "lastName":"Bignotti",
                 "address":"625 Broadway Suite 1025",
                 "city":"San Diego",
                 "state":"CA",
                 "zip":"92101",
                 "country":"United States"
              },
              "recurringBilling":false,
              "recurringBillingSpecified":true,
              "product":"Card Not Present",
              "marketType":"eCommerce"
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
        $response = $request->getBatchStatisticsRequest($requestJson);

        $this->assertEquals('Ok', $response->messages->resultCode);
        $this->assertEquals('I00001', $response->messages->message[0]->code);
        $this->assertEquals('Successful.', $response->messages->message[0]->text);
        $this->assertEquals('2162566217', $response->transaction->transId);
        $this->assertEquals('2011-09-01T16:30:49.39Z', $response->transaction->submitTimeUTC);
        $this->assertEquals('2011-09-01T10:30:49.39', $response->transaction->submitTimeLocal);
        $this->assertEquals('authCaptureTransaction', $response->transaction->transactionType);
        $this->assertEquals('settledSuccessfully', $response->transaction->transactionStatus);
        $this->assertEquals(1, $response->transaction->responseCode);
        $this->assertEquals(1, $response->transaction->responseReasonCode);
        $this->assertEquals('Approval', $response->transaction->responseReasonDescription);
        $this->assertEquals('JPG9DJ', $response->transaction->authCode);
        $this->assertEquals('Y', $response->transaction->AVSResponse);
        $this->assertEquals('1221577', $response->transaction->batch->batchId);
        $this->assertEquals('2011-09-01T16:38:54.52Z', $response->transaction->batch->settlementTimeUTC);
        $this->assertTrue($response->transaction->batch->settlementTimeUTCSpecified);
        $this->assertEquals('2011-09-01T10:38:54.52', $response->transaction->batch->settlementTimeLocal);
        $this->assertTrue($response->transaction->batch->settlementTimeLocalSpecified);
        $this->assertEquals('settledSuccessfully', $response->transaction->batch->settlementState);
        $this->assertEquals('60', $response->transaction->order->invoiceNumber);
        $this->assertEquals('Auto-charge for Invoice #60', $response->transaction->order->description);
        $this->assertFalse($response->transaction->requestedAmountSpecified);
        $this->assertEquals(1018.88, $response->transaction->authAmount);
        $this->assertEquals(1018.88, $response->transaction->settleAmount);
        $this->assertFalse($response->transaction->prepaidBalanceRemainingSpecified);
        $this->assertFalse($response->transaction->taxExempt);
        $this->assertTrue($response->transaction->taxExemptSpecified);
        $this->assertEquals('XXXX4444', $response->transaction->payment->creditCard->cardNumber);
        $this->assertEquals('XXXX', $response->transaction->payment->creditCard->expirationDate);
        $this->assertEquals('MasterCard', $response->transaction->payment->creditCard->cardType);
        $this->assertFalse($response->transaction->customer->typeSpecified);
        $this->assertEquals('4', $response->transaction->customer->id);
        $this->assertEquals('(619) 274-0494', $response->transaction->billTo->phoneNumber);
        $this->assertEquals('Matteo', $response->transaction->billTo->firstName);
        $this->assertEquals('Bignotti', $response->transaction->billTo->lastName);
        $this->assertEquals('625 Broadway Suite 1025', $response->transaction->billTo->address);
        $this->assertEquals('San Diego', $response->transaction->billTo->city);
        $this->assertEquals('CA', $response->transaction->billTo->state);
        $this->assertEquals('92101', $response->transaction->billTo->zip);
        $this->assertEquals('United States', $response->transaction->billTo->country);
        $this->assertFalse($response->transaction->recurringBilling);
        $this->assertTrue($response->transaction->recurringBillingSpecified);
        $this->assertEquals('Card Not Present', $response->transaction->product);
        $this->assertEquals('eCommerce', $response->transaction->marketType);
    }
}