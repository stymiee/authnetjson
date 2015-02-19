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

class AuthnetJsonReportingTest extends \PHPUnit_Framework_TestCase
{
    private $login;
    private $transactionKey;
    private $server;

    protected function setUp()
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
        $this->server         = AuthnetApiFactory::USE_UNIT_TEST_SERVER;
    }

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

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->getUnsettledTransactionListRequest();

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertTrue(is_array($authnet->transactions));
        $this->assertEquals('2228546203', $authnet->transactions[0]->transId);
        $this->assertEquals('2015-02-16T03:34:43Z', $authnet->transactions[0]->submitTimeUTC);
        $this->assertEquals('2015-02-15T20:34:43', $authnet->transactions[0]->submitTimeLocal);
        $this->assertEquals('authorizedPendingCapture', $authnet->transactions[0]->transactionStatus);
        $this->assertEquals('1324567890', $authnet->transactions[0]->invoiceNumber);
        $this->assertEquals('Ellen', $authnet->transactions[0]->firstName);
        $this->assertEquals('Johnson', $authnet->transactions[0]->lastName);
        $this->assertEquals('MasterCard', $authnet->transactions[0]->accountType);
        $this->assertEquals('XXXX0015', $authnet->transactions[0]->accountNumber);
        $this->assertEquals('5.00', $authnet->transactions[0]->settleAmount);
        $this->assertEquals('eCommerce', $authnet->transactions[0]->marketType);
        $this->assertEquals('Card Not Present', $authnet->transactions[0]->product);
        $this->assertFalse($authnet->transactions[0]->hasReturnedItemsSpecified);
        $this->assertEquals('2228546083', $authnet->transactions[1]->transId);
        $this->assertEquals('2015-02-16T03:31:42Z', $authnet->transactions[1]->submitTimeUTC);
        $this->assertEquals('2015-02-15T20:31:42', $authnet->transactions[1]->submitTimeLocal);
        $this->assertEquals('authorizedPendingCapture', $authnet->transactions[1]->transactionStatus);
        $this->assertEquals('1324567890', $authnet->transactions[1]->invoiceNumber);
        $this->assertEquals('Ellen', $authnet->transactions[1]->firstName);
        $this->assertEquals('Johnson', $authnet->transactions[1]->lastName);
        $this->assertEquals('MasterCard', $authnet->transactions[1]->accountType);
        $this->assertEquals('XXXX0015', $authnet->transactions[1]->accountNumber);
        $this->assertEquals('5.00', $authnet->transactions[1]->settleAmount);
        $this->assertEquals('eCommerce', $authnet->transactions[1]->marketType);
        $this->assertEquals('Card Not Present', $authnet->transactions[1]->product);
        $this->assertFalse($authnet->transactions[1]->hasReturnedItemsSpecified);
        $this->assertEquals('2228545865', $authnet->transactions[2]->transId);
        $this->assertEquals('2015-02-16T03:25:00Z', $authnet->transactions[2]->submitTimeUTC);
        $this->assertEquals('2015-02-15T20:25:00', $authnet->transactions[2]->submitTimeLocal);
        $this->assertEquals('authorizedPendingCapture', $authnet->transactions[2]->transactionStatus);
        $this->assertEquals('1324567890', $authnet->transactions[2]->invoiceNumber);
        $this->assertEquals('Ellen', $authnet->transactions[2]->firstName);
        $this->assertEquals('Johnson', $authnet->transactions[2]->lastName);
        $this->assertEquals('MasterCard', $authnet->transactions[2]->accountType);
        $this->assertEquals('XXXX0015', $authnet->transactions[2]->accountNumber);
        $this->assertEquals('5.00', $authnet->transactions[2]->settleAmount);
        $this->assertEquals('eCommerce', $authnet->transactions[2]->marketType);
        $this->assertEquals('Card Not Present', $authnet->transactions[2]->product);
        $this->assertFalse($authnet->transactions[2]->hasReturnedItemsSpecified);
    }

    public function testGetTransactionListRequestSuccess()
    {
        $request = array(
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

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->getTransactionListRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertTrue(is_array($authnet->transactions));
        $this->assertEquals('2162566217', $authnet->transactions[0]->transId);
        $this->assertEquals('2011-09-01T16:30:49Z', $authnet->transactions[0]->submitTimeUTC);
        $this->assertEquals('2011-09-01T10:30:49', $authnet->transactions[0]->submitTimeLocal);
        $this->assertEquals('settledSuccessfully', $authnet->transactions[0]->transactionStatus);
        $this->assertEquals('60', $authnet->transactions[0]->invoiceNumber);
        $this->assertEquals('Matteo', $authnet->transactions[0]->firstName);
        $this->assertEquals('Bignotti', $authnet->transactions[0]->lastName);
        $this->assertEquals('MasterCard', $authnet->transactions[0]->accountType);
        $this->assertEquals('XXXX4444', $authnet->transactions[0]->accountNumber);
        $this->assertEquals('1018.88', $authnet->transactions[0]->settleAmount);
        $this->assertEquals('eCommerce', $authnet->transactions[0]->marketType);
        $this->assertEquals('Card Not Present', $authnet->transactions[0]->product);
        $this->assertFalse($authnet->transactions[0]->hasReturnedItemsSpecified);
    }

    public function testGetSettledBatchListRequestSuccess()
    {
        $request = array(
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

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->getSettledBatchListRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertTrue(is_array($authnet->batchList));
        $this->assertEquals('3990061', $authnet->batchList[0]->batchId);
        $this->assertEquals('2015-01-02T08:40:29Z', $authnet->batchList[0]->settlementTimeUTC);
        $this->assertTrue($authnet->batchList[0]->settlementTimeUTCSpecified);
        $this->assertEquals('2015-01-02T01:40:29', $authnet->batchList[0]->settlementTimeLocal);
        $this->assertTrue($authnet->batchList[0]->settlementTimeLocalSpecified);
        $this->assertEquals('settledSuccessfully', $authnet->batchList[0]->settlementState);
        $this->assertEquals('creditCard', $authnet->batchList[0]->paymentMethod);
        $this->assertEquals('eCommerce', $authnet->batchList[0]->marketType);
        $this->assertEquals('Card Not Present', $authnet->batchList[0]->product);
        $this->assertEquals('3990090', $authnet->batchList[1]->batchId);
        $this->assertEquals('2015-01-02T08:55:35Z', $authnet->batchList[1]->settlementTimeUTC);
        $this->assertTrue($authnet->batchList[1]->settlementTimeUTCSpecified);
        $this->assertEquals('2015-01-02T01:55:35', $authnet->batchList[1]->settlementTimeLocal);
        $this->assertTrue($authnet->batchList[1]->settlementTimeLocalSpecified);
        $this->assertEquals('settledSuccessfully', $authnet->batchList[1]->settlementState);
        $this->assertEquals('creditCard', $authnet->batchList[1]->paymentMethod);
        $this->assertEquals('eCommerce', $authnet->batchList[1]->marketType);
        $this->assertEquals('Card Not Present', $authnet->batchList[1]->product);
    }

    public function testGetSettledBatchListRequestNoRecords()
    {
        $request = array(
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

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->getSettledBatchListRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('I00004', $authnet->messages->message[0]->code);
        $this->assertEquals('No records found.', $authnet->messages->message[0]->text);
    }

    public function testGetBatchStatisticsRequestSuccess()
    {
        $request = array(
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

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->getBatchStatisticsRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('1221577', $authnet->batch->batchId);
        $this->assertEquals('2011-09-01T16:38:54Z', $authnet->batch->settlementTimeUTC);
        $this->assertTrue($authnet->batch->settlementTimeUTCSpecified);
        $this->assertEquals('2011-09-01T10:38:54', $authnet->batch->settlementTimeLocal);
        $this->assertTrue($authnet->batch->settlementTimeLocalSpecified);
        $this->assertEquals('settledSuccessfully', $authnet->batch->settlementState);
        $this->assertEquals('creditCard', $authnet->batch->paymentMethod);
        $this->assertTrue(is_array($authnet->batch->statistics));
        $this->assertEquals('MasterCard', $authnet->batch->statistics[0]->accountType);
        $this->assertEquals(1018.88, $authnet->batch->statistics[0]->chargeAmount);
        $this->assertEquals(1, $authnet->batch->statistics[0]->chargeCount);
        $this->assertEquals(0.00, $authnet->batch->statistics[0]->refundAmount);
        $this->assertEquals(0, $authnet->batch->statistics[0]->refundCount);
        $this->assertEquals(0, $authnet->batch->statistics[0]->voidCount);
        $this->assertEquals(0, $authnet->batch->statistics[0]->declineCount);
        $this->assertEquals(0, $authnet->batch->statistics[0]->errorCount);
        $this->assertFalse($authnet->batch->statistics[0]->returnedItemAmountSpecified);
        $this->assertFalse($authnet->batch->statistics[0]->returnedItemCountSpecified);
        $this->assertFalse($authnet->batch->statistics[0]->chargebackAmountSpecified);
        $this->assertFalse($authnet->batch->statistics[0]->chargebackCountSpecified);
        $this->assertFalse($authnet->batch->statistics[0]->correctionNoticeCountSpecified);
        $this->assertFalse($authnet->batch->statistics[0]->chargeChargeBackAmountSpecified);
        $this->assertFalse($authnet->batch->statistics[0]->chargeChargeBackCountSpecified);
        $this->assertFalse($authnet->batch->statistics[0]->refundChargeBackAmountSpecified);
        $this->assertFalse($authnet->batch->statistics[0]->refundChargeBackCountSpecified);
        $this->assertFalse($authnet->batch->statistics[0]->chargeReturnedItemsAmountSpecified);
        $this->assertFalse($authnet->batch->statistics[0]->chargeReturnedItemsCountSpecified);
        $this->assertFalse($authnet->batch->statistics[0]->refundReturnedItemsAmountSpecified);
        $this->assertFalse($authnet->batch->statistics[0]->refundReturnedItemsCountSpecified);
    }

    public function testGetBatchStatisticsRequestNoRecords()
    {
        $request = array(
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

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->getBatchStatisticsRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('I00004', $authnet->messages->message[0]->code);
        $this->assertEquals('No records found.', $authnet->messages->message[0]->text);
    }

    public function testGetTransactionDetailsRequestSuccess()
    {
        $request = array(
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

        $authnet = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server, $responseJson);
        $authnet->getBatchStatisticsRequest($request);

        $this->assertEquals('Ok', $authnet->messages->resultCode);
        $this->assertEquals('I00001', $authnet->messages->message[0]->code);
        $this->assertEquals('Successful.', $authnet->messages->message[0]->text);
        $this->assertEquals('2162566217', $authnet->transaction->transId);
        $this->assertEquals('2011-09-01T16:30:49.39Z', $authnet->transaction->submitTimeUTC);
        $this->assertEquals('2011-09-01T10:30:49.39', $authnet->transaction->submitTimeLocal);
        $this->assertEquals('authCaptureTransaction', $authnet->transaction->transactionType);
        $this->assertEquals('settledSuccessfully', $authnet->transaction->transactionStatus);
        $this->assertEquals(1, $authnet->transaction->responseCode);
        $this->assertEquals(1, $authnet->transaction->responseReasonCode);
        $this->assertEquals('Approval', $authnet->transaction->responseReasonDescription);
        $this->assertEquals('JPG9DJ', $authnet->transaction->authCode);
        $this->assertEquals('Y', $authnet->transaction->AVSResponse);
        $this->assertEquals('1221577', $authnet->transaction->batch->batchId);
        $this->assertEquals('2011-09-01T16:38:54.52Z', $authnet->transaction->batch->settlementTimeUTC);
        $this->assertTrue($authnet->transaction->batch->settlementTimeUTCSpecified);
        $this->assertEquals('2011-09-01T10:38:54.52', $authnet->transaction->batch->settlementTimeLocal);
        $this->assertTrue($authnet->transaction->batch->settlementTimeLocalSpecified);
        $this->assertEquals('settledSuccessfully', $authnet->transaction->batch->settlementState);
        $this->assertEquals('60', $authnet->transaction->order->invoiceNumber);
        $this->assertEquals('Auto-charge for Invoice #60', $authnet->transaction->order->description);
        $this->assertFalse($authnet->transaction->requestedAmountSpecified);
        $this->assertEquals(1018.88, $authnet->transaction->authAmount);
        $this->assertEquals(1018.88, $authnet->transaction->settleAmount);
        $this->assertFalse($authnet->transaction->prepaidBalanceRemainingSpecified);
        $this->assertFalse($authnet->transaction->taxExempt);
        $this->assertTrue($authnet->transaction->taxExemptSpecified);
        $this->assertEquals('XXXX4444', $authnet->transaction->payment->creditCard->cardNumber);
        $this->assertEquals('XXXX', $authnet->transaction->payment->creditCard->expirationDate);
        $this->assertEquals('MasterCard', $authnet->transaction->payment->creditCard->cardType);
        $this->assertFalse($authnet->transaction->customer->typeSpecified);
        $this->assertEquals('4', $authnet->transaction->customer->id);
        $this->assertEquals('(619) 274-0494', $authnet->transaction->billTo->phoneNumber);
        $this->assertEquals('Matteo', $authnet->transaction->billTo->firstName);
        $this->assertEquals('Bignotti', $authnet->transaction->billTo->lastName);
        $this->assertEquals('625 Broadway Suite 1025', $authnet->transaction->billTo->address);
        $this->assertEquals('San Diego', $authnet->transaction->billTo->city);
        $this->assertEquals('CA', $authnet->transaction->billTo->state);
        $this->assertEquals('92101', $authnet->transaction->billTo->zip);
        $this->assertEquals('United States', $authnet->transaction->billTo->country);
        $this->assertFalse($authnet->transaction->recurringBilling);
        $this->assertTrue($authnet->transaction->recurringBillingSpecified);
        $this->assertEquals('Card Not Present', $authnet->transaction->product);
        $this->assertEquals('eCommerce', $authnet->transaction->marketType);
    }
}