<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Authnetjson\tests;

use Authnetjson\AuthnetApiFactory;
use Authnetjson\AuthnetJsonResponse;
use PHPUnit\Framework\TestCase;
use Curl\Curl;

class AuthnetJsonAimPaypalTest extends TestCase
{
    private $login;
    private $transactionKey;
    private $server;
    private $http;

    protected function setUp(): void
    {
        $this->login          = 'test';
        $this->transactionKey = 'test';
        $this->server         = AuthnetApiFactory::USE_DEVELOPMENT_SERVER;

        $this->http = $this->getMockBuilder(Curl::class)
            ->getMock();
        $this->http->error = false;
    }

    /**
    * @covers \Authnetjson\AuthnetJsonRequest::process()
    *
    * @uses \Authnetjson\AuthnetApiFactory::getJsonApiHandler
    * @uses \Authnetjson\AuthnetApiFactory::getWebServiceURL
    */
    public function testCreateTransactionRequestAuthCapture(): void
    {
        $requestJson = array(
            "transactionRequest" => array(
                "transactionType" => "authCaptureTransaction",
                "amount" => "80.93",
                "payment" => array(
                    "payPal" => array(
                        "successUrl" => "https://my.server.com/success.html",
                        "cancelUrl" => "https://my.server.com/cancel.html",
                        "paypalLc" => "",
                        "paypalHdrImg" => "",
                        "paypalPayflowcolor" => "FFFF00"
                    )
                ),
                "lineItems" => array(
                    "lineItem" => array(
                        "itemId" => "item1",
                        "name" => "golf balls",
                        "quantity" => "1",
                        "unitPrice" => "18.95"
                    )
                )
            )
        );
        $responseJson = '{
            "transactionResponse": {
                "responseCode": "1",
                "transId": "2149186848",
                "refTransID": "2149186775",
                "transHash": "D6C9036F443BADE785D57DA2B44CD190",
                "testRequest": "0",
                "accountType": "PayPal",
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isError());

        self::assertEquals('PayPal', $response->transactionResponse->accountType);
    }

    /**
     * @covers \Authnetjson\AuthnetJsonRequest::process()
     *
     * @uses \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestAuthCaptureContinue(): void
    {
        $requestJson = array(
            "transactionRequest" => array(
                "transactionType" => "authCaptureContinueTransaction",
                "payment" => array(
                    "payPal" => array(
                        "payerID" =>  "S6D5ETGSVYX94"
                    )
                ),
                "refTransId" => "139"
            )
        );
        $responseJson = '{
            "transactionResponse": {
                "responseCode": "1",
                "authCode": "HH5414",
                "avsResultCode": "P",
                "cvvResultCode": "",
                "cavvResultCode": "",
                "transId": "2149186848",
                "refTransID": "2149186848",
                "transHash": "D3A855F0934EB404DE3B13508D0E3826",
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isError());

        self::assertEquals('MasterCard', $response->transactionResponse->accountType);
    }

    /**
     * @covers \Authnetjson\AuthnetJsonRequest::process()
     *
     * @uses \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestAuthOnly(): void
    {
        $requestJson = array(
            "transactionRequest" => array(
                "transactionType" => "authOnlyTransaction",
                "amount" => "5",
                "payment" => array(
                    "payPal" => array(
                        "successUrl" => "https://my.server.com/success.html",
                        "cancelUrl" => "https://my.server.com/cancel.html"
                    )
                )
            )
        );
        $responseJson = '{
            "transactionResponse": {
                "responseCode": "5",
                "rawResponseCode": "0",
                "transId": "2149186954",
                "refTransID": "",
                "transHash": "A719785EE9752530FDCE67695E9A56EE",
                "testRequest": "0",
                "accountType": "PayPal",
                "messages": [
                    {
                        "code": "2000",
                        "description": "Need payer consent."
                    }
                ],
                "secureAcceptance": {
                    "SecureAcceptanceUrl": "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-C506B0LGTG2J800OK"
                }
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isError());

        self::assertEquals(AuthnetJsonResponse::STATUS_PAYPAL_NEED_CONSENT, $response->transactionResponse->responseCode);
        self::assertEquals('PayPal', $response->transactionResponse->accountType);
        self::assertEquals('2000', $response->transactionResponse->messages[0]->code);
        self::assertEquals('Need payer consent.', $response->transactionResponse->messages[0]->description);
        self::assertEquals('https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=EC-C506B0LGTG2J800OK', $response->transactionResponse->secureAcceptance->SecureAcceptanceUrl);
    }


    /**
     * @covers \Authnetjson\AuthnetJsonRequest::process()
     *
     * @uses \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestAuthOnlyContinue(): void
    {
        $requestJson = array(
            "transactionRequest" => array(
                "transactionType" => "authOnlyContinueTransaction",
                "payment" => array(
                    "payPal" => array(
                        "payerID" => "S6D5ETGSVYX94"
                    )
                ),
                "refTransId" => "128"
            )
        );
        $responseJson = '{
            "transactionResponse": {
                "responseCode": "1",
                "authCode": "HH5414",
                "avsResultCode": "P",
                "cvvResultCode": "",
                "cavvResultCode": "",
                "transId": "2149186848",
                "refTransID": "2149186848",
                "transHash": "D3A855F0934EB404DE3B13508D0E3826",
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isError());

        self::assertEquals('MasterCard', $response->transactionResponse->accountType);
    }

    /**
     * @covers \Authnetjson\AuthnetJsonRequest::process()
     *
     * @uses \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestGetDetails(): void
    {
        $requestJson = array(
            "transactionRequest" => array(
                "transactionType" => "getDetailsTransaction",
                "refTransId" => "128"
            )
        );
        $responseJson = '{
            "transactionResponse": {
                "responseCode": "1",
                "authCode": "HH5414",
                "avsResultCode": "P",
                "cvvResultCode": "",
                "cavvResultCode": "",
                "transId": "2149186848",
                "refTransID": "2149186848",
                "transHash": "D3A855F0934EB404DE3B13508D0E3826",
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isError());

        self::assertEquals('MasterCard', $response->transactionResponse->accountType);
    }

    /**
     * @covers \Authnetjson\AuthnetJsonRequest::process()
     *
     * @uses \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestPriorAuthCapture(): void
    {
        $requestJson = array(
            "transactionRequest" => array(
                "transactionType" => "priorAuthCaptureTransaction",
                "refTransId" => "128"
            )
        );
        $responseJson = '{
            "transactionResponse": {
                "responseCode": "1",
                "authCode": "HH5414",
                "avsResultCode": "P",
                "cvvResultCode": "",
                "cavvResultCode": "",
                "transId": "2149186848",
                "refTransID": "2149186848",
                "transHash": "D3A855F0934EB404DE3B13508D0E3826",
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isError());

        self::assertEquals('MasterCard', $response->transactionResponse->accountType);
    }

    /**
     * @covers \Authnetjson\AuthnetJsonRequest::process()
     *
     * @uses \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestRefund(): void
    {
        $requestJson = array(
            "transactionRequest" => array(
                "transactionType" => "refundTransaction",
                "refTransId" => "138"
            )
        );
        $responseJson = '{
            "transactionResponse": {
                "responseCode": "1",
                "transId": "2149186848",
                "refTransID": "2149186775",
                "transHash": "D6C9036F443BADE785D57DA2B44CD190",
                "testRequest": "0",
                "accountType": "PayPal",
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isError());

        self::assertEquals('PayPal', $response->transactionResponse->accountType);
    }

    /**
     * @covers \Authnetjson\AuthnetJsonRequest::process()
     *
     * @uses \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestVoid(): void
    {
        $requestJson = array(
            "transactionRequest" => array(
                "transactionType" => "voidTransaction",
                "refTransId" => "138"
            )
        );
        $responseJson = '{
            "transactionResponse": {
                "responseCode": "1",
                "authCode": "HH5414",
                "avsResultCode": "P",
                "cvvResultCode": "",
                "cavvResultCode": "",
                "transId": "2149186848",
                "refTransID": "2149186848",
                "transHash": "D3A855F0934EB404DE3B13508D0E3826",
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

        self::assertEquals('Ok', $response->messages->resultCode);
        self::assertTrue($response->isSuccessful());
        self::assertFalse($response->isError());

        self::assertEquals('MasterCard', $response->transactionResponse->accountType);
    }


    /**
     * @covers \Authnetjson\AuthnetJsonRequest::process()

     * @uses \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestAuthCaptureError(): void
    {
        $requestJson = array(
            "transactionRequest" => array(
                "transactionType" => "authCaptureTransaction",
                "amount" => "80.93",
                "payment" => array(
                    "payPal" => array(
                        "successUrl" => "https://my.server.com/success.html",
                        "cancelUrl" => "https://my.server.com/cancel.html",
                        "paypalLc" => "",
                        "paypalHdrImg" => "",
                        "paypalPayflowcolor" => "FFFF00"
                    )
                ),
                "lineItems" => array(
                    "lineItem" => array(
                        "itemId" => "item1",
                        "name" => "golf balls",
                        "quantity" => "1",
                        "unitPrice" => "18.95"
                    )
                )
            )
        );
        $responseJson = '{
            "transactionResponse": {
                "responseCode": "3",
                "rawResponseCode": "0",
                "transId": "0",
                "refTransID": "",
                "transHash": "2AF9B654FE7745AF78EBF7A8DD8A18D2",
                "testRequest": "0",
                "accountType": "PayPal",
                "errors": [
                    {
                        "errorCode": "2001",
                        "errorText": "PayPal transactions are not accepted by this merchant."
                    }
                ]
            },
            "messages": {
                "resultCode": "Error",
                "message": [
                    {
                        "code": "E00027",
                        "text": "The transaction was unsuccessful."
                    }
                ]
            }
        }';

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        self::assertEquals('Error', $response->messages->resultCode);
        self::assertFalse($response->isSuccessful());
        self::assertTrue($response->isError());

        self::assertEquals(AuthnetJsonResponse::STATUS_ERROR, $response->transactionResponse->responseCode);
        self::assertEquals('PayPal', $response->transactionResponse->accountType);
        self::assertEquals('2001', $response->transactionResponse->errors[0]->errorCode);
        self::assertEquals('PayPal transactions are not accepted by this merchant.', $response->transactionResponse->errors[0]->errorText);
    }



    /**
     * @covers \Authnetjson\AuthnetJsonRequest::process()

     * @uses \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestGetDetailsError(): void
    {
        $requestJson = array(
            "transactionRequest" => array(
                "transactionType" => "getDetailsTransaction",
                "refTransId" => "128"
            )
        );
        $responseJson = '{
            "transactionResponse": {
                "responseCode": "3",
                "authCode": "",
                "avsResultCode": "P",
                "cvvResultCode": "",
                "cavvResultCode": "",
                "transId": "0",
                "refTransID": "128",
                "transHash": "B349AC0DCCCF601C6DB09403341CD18F",
                "testRequest": "0",
                "accountNumber": "",
                "accountType": "",
                "errors": [
                    {
                        "errorCode": "16",
                        "errorText": "The transaction cannot be found."
                    }
                ],
                "shipTo": {}
            },
            "messages": {
                "resultCode": "Error",
                "message": [
                    {
                        "code": "E00027",
                        "text": "The transaction was unsuccessful."
                    }
                ]
            }
        }';

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        self::assertEquals('Error', $response->messages->resultCode);
        self::assertFalse($response->isSuccessful());
        self::assertTrue($response->isError());

        self::assertEquals(AuthnetJsonResponse::STATUS_ERROR, $response->transactionResponse->responseCode);
        self::assertEquals('', $response->transactionResponse->accountType);
        self::assertEquals('16', $response->transactionResponse->errors[0]->errorCode);
        self::assertEquals('The transaction cannot be found.', $response->transactionResponse->errors[0]->errorText);
    }


    /**
     * @covers \Authnetjson\AuthnetJsonRequest::process()

     * @uses \Authnetjson\AuthnetApiFactory::getJsonApiHandler
     * @uses \Authnetjson\AuthnetApiFactory::getWebServiceURL
     */
    public function testCreateTransactionRequestRefundError(): void
    {
        $requestJson = array(
            "transactionRequest" => array(
                "transactionType" => "refundTransaction",
                "refTransId" => "138"
            )
        );
        $responseJson = '{
            "transactionResponse": {
                "responseCode": "3",
                "transId": "0",
                "refTransID": "2149186775",
                "transHash": "D6C9036F443BADE785D57DA2B44CD190",
                "testRequest": "0",
                "accountType": "PayPal",
                "errors": [
                    {
                        "errorCode": "54",
                        "errorText": "The referenced transaction does not meet the criteria for issuing a credit."
                    }
                ]
            },
            "refId": "123456",
            "messages": {
                "resultCode": "Error",
                "message": [
                    {
                        "code": "E00027",
                        "text": "The transaction was unsuccessful."
                    }
                ]
            }
        }';

        $this->http->response = $responseJson;

        $request = AuthnetApiFactory::getJsonApiHandler($this->login, $this->transactionKey, $this->server);
        $request->setProcessHandler($this->http);
        $response = $request->createTransactionRequest($requestJson);

        self::assertEquals('Error', $response->messages->resultCode);
        self::assertFalse($response->isSuccessful());
        self::assertTrue($response->isError());

        self::assertEquals(AuthnetJsonResponse::STATUS_ERROR, $response->transactionResponse->responseCode);
        self::assertEquals('PayPal', $response->transactionResponse->accountType);
        self::assertEquals('54', $response->transactionResponse->errors[0]->errorCode);
        self::assertEquals('The referenced transaction does not meet the criteria for issuing a credit.', $response->transactionResponse->errors[0]->errorText);
    }
}
