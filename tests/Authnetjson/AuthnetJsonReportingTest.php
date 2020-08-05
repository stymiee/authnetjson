<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Authnetjson;

use PHPUnit\Framework\TestCase;
use Curl\Curl;

class AuthnetJsonReportingTest extends TestCase
{
    private $login;
    private $transactionKey;
    private $server;
    private $http;

    protected function setUp() : void
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
        $this->server         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;

        $this->http = $this->getMockBuilder(Curl::class)
            ->setMethods(['post'])
            ->getMock();
        $this->http->error = false;
    }

    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::process()

     * @uses              \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses              \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetUnsettledTransactionListRequestSuccess() : void
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertEquals('I00001', $response->messages->message[0]->code);
        self::assertEquals('Successful.', $response->messages->message[0]->text);
        self::assertIsArray($response->transactions);
        self::assertEquals('2228546203', $response->transactions[0]->transId);
        self::assertEquals('2015-02-16T03:34:43Z', $response->transactions[0]->submitTimeUTC);
        self::assertEquals('2015-02-15T20:34:43', $response->transactions[0]->submitTimeLocal);
        self::assertEquals('authorizedPendingCapture', $response->transactions[0]->transactionStatus);
        self::assertEquals('1324567890', $response->transactions[0]->invoiceNumber);
        self::assertEquals('Ellen', $response->transactions[0]->firstName);
        self::assertEquals('Johnson', $response->transactions[0]->lastName);
        self::assertEquals('MasterCard', $response->transactions[0]->accountType);
        self::assertEquals('XXXX0015', $response->transactions[0]->accountNumber);
        self::assertEquals('5.00', $response->transactions[0]->settleAmount);
        self::assertEquals('eCommerce', $response->transactions[0]->marketType);
        self::assertEquals('Card Not Present', $response->transactions[0]->product);
        self::assertFalse($response->transactions[0]->hasReturnedItemsSpecified);
        self::assertEquals('2228546083', $response->transactions[1]->transId);
        self::assertEquals('2015-02-16T03:31:42Z', $response->transactions[1]->submitTimeUTC);
        self::assertEquals('2015-02-15T20:31:42', $response->transactions[1]->submitTimeLocal);
        self::assertEquals('authorizedPendingCapture', $response->transactions[1]->transactionStatus);
        self::assertEquals('1324567890', $response->transactions[1]->invoiceNumber);
        self::assertEquals('Ellen', $response->transactions[1]->firstName);
        self::assertEquals('Johnson', $response->transactions[1]->lastName);
        self::assertEquals('MasterCard', $response->transactions[1]->accountType);
        self::assertEquals('XXXX0015', $response->transactions[1]->accountNumber);
        self::assertEquals('5.00', $response->transactions[1]->settleAmount);
        self::assertEquals('eCommerce', $response->transactions[1]->marketType);
        self::assertEquals('Card Not Present', $response->transactions[1]->product);
        self::assertFalse($response->transactions[1]->hasReturnedItemsSpecified);
        self::assertEquals('2228545865', $response->transactions[2]->transId);
        self::assertEquals('2015-02-16T03:25:00Z', $response->transactions[2]->submitTimeUTC);
        self::assertEquals('2015-02-15T20:25:00', $response->transactions[2]->submitTimeLocal);
        self::assertEquals('authorizedPendingCapture', $response->transactions[2]->transactionStatus);
        self::assertEquals('1324567890', $response->transactions[2]->invoiceNumber);
        self::assertEquals('Ellen', $response->transactions[2]->firstName);
        self::assertEquals('Johnson', $response->transactions[2]->lastName);
        self::assertEquals('MasterCard', $response->transactions[2]->accountType);
        self::assertEquals('XXXX0015', $response->transactions[2]->accountNumber);
        self::assertEquals('5.00', $response->transactions[2]->settleAmount);
        self::assertEquals('eCommerce', $response->transactions[2]->marketType);
        self::assertEquals('Card Not Present', $response->transactions[2]->product);
        self::assertFalse($response->transactions[2]->hasReturnedItemsSpecified);
    }

    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::process()

     * @uses              \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses              \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetTransactionListRequestSuccess() : void
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertEquals('I00001', $response->messages->message[0]->code);
        self::assertEquals('Successful.', $response->messages->message[0]->text);
        self::assertIsArray($response->transactions);
        self::assertEquals('2162566217', $response->transactions[0]->transId);
        self::assertEquals('2011-09-01T16:30:49Z', $response->transactions[0]->submitTimeUTC);
        self::assertEquals('2011-09-01T10:30:49', $response->transactions[0]->submitTimeLocal);
        self::assertEquals('settledSuccessfully', $response->transactions[0]->transactionStatus);
        self::assertEquals('60', $response->transactions[0]->invoiceNumber);
        self::assertEquals('Matteo', $response->transactions[0]->firstName);
        self::assertEquals('Bignotti', $response->transactions[0]->lastName);
        self::assertEquals('MasterCard', $response->transactions[0]->accountType);
        self::assertEquals('XXXX4444', $response->transactions[0]->accountNumber);
        self::assertEquals('1018.88', $response->transactions[0]->settleAmount);
        self::assertEquals('eCommerce', $response->transactions[0]->marketType);
        self::assertEquals('Card Not Present', $response->transactions[0]->product);
        self::assertFalse($response->transactions[0]->hasReturnedItemsSpecified);
    }

    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::process()

     * @uses              \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses              \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetSettledBatchListRequestSuccess() : void
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertEquals('I00001', $response->messages->message[0]->code);
        self::assertEquals('Successful.', $response->messages->message[0]->text);
        self::assertIsArray($response->batchList);
        self::assertEquals('3990061', $response->batchList[0]->batchId);
        self::assertEquals('2015-01-02T08:40:29Z', $response->batchList[0]->settlementTimeUTC);
        self::assertTrue($response->batchList[0]->settlementTimeUTCSpecified);
        self::assertEquals('2015-01-02T01:40:29', $response->batchList[0]->settlementTimeLocal);
        self::assertTrue($response->batchList[0]->settlementTimeLocalSpecified);
        self::assertEquals('settledSuccessfully', $response->batchList[0]->settlementState);
        self::assertEquals('creditCard', $response->batchList[0]->paymentMethod);
        self::assertEquals('eCommerce', $response->batchList[0]->marketType);
        self::assertEquals('Card Not Present', $response->batchList[0]->product);
        self::assertEquals('3990090', $response->batchList[1]->batchId);
        self::assertEquals('2015-01-02T08:55:35Z', $response->batchList[1]->settlementTimeUTC);
        self::assertTrue($response->batchList[1]->settlementTimeUTCSpecified);
        self::assertEquals('2015-01-02T01:55:35', $response->batchList[1]->settlementTimeLocal);
        self::assertTrue($response->batchList[1]->settlementTimeLocalSpecified);
        self::assertEquals('settledSuccessfully', $response->batchList[1]->settlementState);
        self::assertEquals('creditCard', $response->batchList[1]->paymentMethod);
        self::assertEquals('eCommerce', $response->batchList[1]->marketType);
        self::assertEquals('Card Not Present', $response->batchList[1]->product);
    }

    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::process()

     * @uses              \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses              \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetSettledBatchListRequestNoRecords() : void
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertEquals('I00004', $response->messages->message[0]->code);
        self::assertEquals('No records found.', $response->messages->message[0]->text);
    }

    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::process()

     * @uses              \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses              \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetBatchStatisticsRequestSuccess() : void
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertEquals('I00001', $response->messages->message[0]->code);
        self::assertEquals('Successful.', $response->messages->message[0]->text);
        self::assertEquals('1221577', $response->batch->batchId);
        self::assertEquals('2011-09-01T16:38:54Z', $response->batch->settlementTimeUTC);
        self::assertTrue($response->batch->settlementTimeUTCSpecified);
        self::assertEquals('2011-09-01T10:38:54', $response->batch->settlementTimeLocal);
        self::assertTrue($response->batch->settlementTimeLocalSpecified);
        self::assertEquals('settledSuccessfully', $response->batch->settlementState);
        self::assertEquals('creditCard', $response->batch->paymentMethod);
        self::assertIsArray($response->batch->statistics);
        self::assertEquals('MasterCard', $response->batch->statistics[0]->accountType);
        self::assertEquals(1018.88, $response->batch->statistics[0]->chargeAmount);
        self::assertEquals(1, $response->batch->statistics[0]->chargeCount);
        self::assertEquals(0.00, $response->batch->statistics[0]->refundAmount);
        self::assertEquals(0, $response->batch->statistics[0]->refundCount);
        self::assertEquals(0, $response->batch->statistics[0]->voidCount);
        self::assertEquals(0, $response->batch->statistics[0]->declineCount);
        self::assertEquals(0, $response->batch->statistics[0]->errorCount);
        self::assertFalse($response->batch->statistics[0]->returnedItemAmountSpecified);
        self::assertFalse($response->batch->statistics[0]->returnedItemCountSpecified);
        self::assertFalse($response->batch->statistics[0]->chargebackAmountSpecified);
        self::assertFalse($response->batch->statistics[0]->chargebackCountSpecified);
        self::assertFalse($response->batch->statistics[0]->correctionNoticeCountSpecified);
        self::assertFalse($response->batch->statistics[0]->chargeChargeBackAmountSpecified);
        self::assertFalse($response->batch->statistics[0]->chargeChargeBackCountSpecified);
        self::assertFalse($response->batch->statistics[0]->refundChargeBackAmountSpecified);
        self::assertFalse($response->batch->statistics[0]->refundChargeBackCountSpecified);
        self::assertFalse($response->batch->statistics[0]->chargeReturnedItemsAmountSpecified);
        self::assertFalse($response->batch->statistics[0]->chargeReturnedItemsCountSpecified);
        self::assertFalse($response->batch->statistics[0]->refundReturnedItemsAmountSpecified);
        self::assertFalse($response->batch->statistics[0]->refundReturnedItemsCountSpecified);
    }

    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::process()

     * @uses              \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses              \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetBatchStatisticsRequestNoRecords() : void
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertEquals('I00004', $response->messages->message[0]->code);
        self::assertEquals('No records found.', $response->messages->message[0]->text);
    }

    /**
     * @covers            \Authnetjson\AuthnetJsonRequest::process()

     * @uses              \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses              \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testGetTransactionDetailsRequestSuccess() : void
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertEquals('I00001', $response->messages->message[0]->code);
        self::assertEquals('Successful.', $response->messages->message[0]->text);
        self::assertEquals('2162566217', $response->transaction->transId);
        self::assertEquals('2011-09-01T16:30:49.39Z', $response->transaction->submitTimeUTC);
        self::assertEquals('2011-09-01T10:30:49.39', $response->transaction->submitTimeLocal);
        self::assertEquals('authCaptureTransaction', $response->transaction->transactionType);
        self::assertEquals('settledSuccessfully', $response->transaction->transactionStatus);
        self::assertEquals(1, $response->transaction->responseCode);
        self::assertEquals(1, $response->transaction->responseReasonCode);
        self::assertEquals('Approval', $response->transaction->responseReasonDescription);
        self::assertEquals('JPG9DJ', $response->transaction->authCode);
        self::assertEquals('Y', $response->transaction->AVSResponse);
        self::assertEquals('1221577', $response->transaction->batch->batchId);
        self::assertEquals('2011-09-01T16:38:54.52Z', $response->transaction->batch->settlementTimeUTC);
        self::assertTrue($response->transaction->batch->settlementTimeUTCSpecified);
        self::assertEquals('2011-09-01T10:38:54.52', $response->transaction->batch->settlementTimeLocal);
        self::assertTrue($response->transaction->batch->settlementTimeLocalSpecified);
        self::assertEquals('settledSuccessfully', $response->transaction->batch->settlementState);
        self::assertEquals('60', $response->transaction->order->invoiceNumber);
        self::assertEquals('Auto-charge for Invoice #60', $response->transaction->order->description);
        self::assertFalse($response->transaction->requestedAmountSpecified);
        self::assertEquals(1018.88, $response->transaction->authAmount);
        self::assertEquals(1018.88, $response->transaction->settleAmount);
        self::assertFalse($response->transaction->prepaidBalanceRemainingSpecified);
        self::assertFalse($response->transaction->taxExempt);
        self::assertTrue($response->transaction->taxExemptSpecified);
        self::assertEquals('XXXX4444', $response->transaction->payment->creditCard->cardNumber);
        self::assertEquals('XXXX', $response->transaction->payment->creditCard->expirationDate);
        self::assertEquals('MasterCard', $response->transaction->payment->creditCard->cardType);
        self::assertFalse($response->transaction->customer->typeSpecified);
        self::assertEquals('4', $response->transaction->customer->id);
        self::assertEquals('(619) 274-0494', $response->transaction->billTo->phoneNumber);
        self::assertEquals('Matteo', $response->transaction->billTo->firstName);
        self::assertEquals('Bignotti', $response->transaction->billTo->lastName);
        self::assertEquals('625 Broadway Suite 1025', $response->transaction->billTo->address);
        self::assertEquals('San Diego', $response->transaction->billTo->city);
        self::assertEquals('CA', $response->transaction->billTo->state);
        self::assertEquals('92101', $response->transaction->billTo->zip);
        self::assertEquals('United States', $response->transaction->billTo->country);
        self::assertFalse($response->transaction->recurringBilling);
        self::assertTrue($response->transaction->recurringBillingSpecified);
        self::assertEquals('Card Not Present', $response->transaction->product);
        self::assertEquals('eCommerce', $response->transaction->marketType);
    }
}
