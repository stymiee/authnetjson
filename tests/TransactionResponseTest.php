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

use PHPUnit\Framework\TestCase;

class TransactionResponseTest extends TestCase
{
    /**
     * @covers            \JohnConde\Authnet\TransactionResponse::__construct()
     * @covers            \JohnConde\Authnet\TransactionResponse::getTransactionResponseField()
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::getTransactionResponseField()
     */
    public function testTransactionResponse() : void
    {
        $transactionIfo = '1,1,1,This transaction has been approved.,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174';

        $response = new TransactionResponse($transactionIfo);

        self::assertEquals('1', $response->getTransactionResponseField('ResponseCode'));
        self::assertEquals('1', $response->getTransactionResponseField('ResponseSubcode'));
        self::assertEquals('1', $response->getTransactionResponseField('ResponseReasonCode'));
        self::assertEquals('This transaction has been approved.', $response->getTransactionResponseField('ResponseReasonText'));
        self::assertEquals('902R0T', $response->getTransactionResponseField('AuthorizationCode'));
        self::assertEquals('Y', $response->getTransactionResponseField('AVSResponse'));
        self::assertEquals('2230582306', $response->getTransactionResponseField('TransactionID'));
        self::assertEquals('INV000001', $response->getTransactionResponseField('InvoiceNumber'));
        self::assertEquals('description of transaction', $response->getTransactionResponseField('Description'));
        self::assertEquals('10.95', $response->getTransactionResponseField('Amount'));
        self::assertEquals('CC', $response->getTransactionResponseField('Method'));
        self::assertEquals('auth_capture', $response->getTransactionResponseField('TransactionType'));
        self::assertEquals('12345', $response->getTransactionResponseField('CustomerID'));
        self::assertEquals('John', $response->getTransactionResponseField('FirstName'));
        self::assertEquals('Smith', $response->getTransactionResponseField('LastName'));
        self::assertEquals('Company Name', $response->getTransactionResponseField('Company'));
        self::assertEquals('123 Main Street', $response->getTransactionResponseField('Address'));
        self::assertEquals('Townsville', $response->getTransactionResponseField('City'));
        self::assertEquals('NJ', $response->getTransactionResponseField('State'));
        self::assertEquals('12345', $response->getTransactionResponseField('ZipCode'));
        self::assertEquals('United States', $response->getTransactionResponseField('Country'));
        self::assertEquals('800-555-1234', $response->getTransactionResponseField('Phone'));
        self::assertEquals('800-555-1235', $response->getTransactionResponseField('Fax'));
        self::assertEquals('user@example.com', $response->getTransactionResponseField('EmailAddress'));
        self::assertEquals('John', $response->getTransactionResponseField('ShipToFirstName'));
        self::assertEquals('Smith', $response->getTransactionResponseField('ShipToLastName'));
        self::assertEquals('Other Company Name', $response->getTransactionResponseField('ShipToCompany'));
        self::assertEquals('123 Main Street', $response->getTransactionResponseField('ShipToAddress'));
        self::assertEquals('Townsville', $response->getTransactionResponseField('ShipToCity'));
        self::assertEquals('NJ', $response->getTransactionResponseField('ShipToState'));
        self::assertEquals('12345', $response->getTransactionResponseField('ShipToZip'));
        self::assertEquals('United States', $response->getTransactionResponseField('ShipToCountry'));
        self::assertEquals('1.00', $response->getTransactionResponseField('Tax'));
        self::assertEquals('2.00', $response->getTransactionResponseField('Duty'));
        self::assertEquals('3.00', $response->getTransactionResponseField('Freight'));
        self::assertEquals('FALSE', $response->getTransactionResponseField('TaxExempt'));
        self::assertEquals('PONUM000001', $response->getTransactionResponseField('PurchaseOrderNumber'));
        self::assertEquals('D3B20D6194B0E86C03A18987300E781C', $response->getTransactionResponseField('MD5Hash'));
        self::assertEquals('P', $response->getTransactionResponseField('CardCodeResponse'));
        self::assertEquals('2', $response->getTransactionResponseField('CardholderAuthenticationVerificationResponse'));
        self::assertEquals('XXXX1111', $response->getTransactionResponseField('AccountNumber'));
        self::assertEquals('Visa', $response->getTransactionResponseField('CardType'));

        self::assertEquals('1', $response->getTransactionResponseField(1));
        self::assertEquals('1', $response->getTransactionResponseField(2));
        self::assertEquals('1', $response->getTransactionResponseField(3));
        self::assertEquals('This transaction has been approved.', $response->getTransactionResponseField(4));
        self::assertEquals('902R0T', $response->getTransactionResponseField(5));
        self::assertEquals('Y', $response->getTransactionResponseField(6));
        self::assertEquals('2230582306', $response->getTransactionResponseField(7));
        self::assertEquals('INV000001', $response->getTransactionResponseField(8));
        self::assertEquals('description of transaction', $response->getTransactionResponseField(9));
        self::assertEquals('10.95', $response->getTransactionResponseField(10));
        self::assertEquals('CC', $response->getTransactionResponseField(11));
        self::assertEquals('auth_capture', $response->getTransactionResponseField(12));
        self::assertEquals('12345', $response->getTransactionResponseField(13));
        self::assertEquals('John', $response->getTransactionResponseField(14));
        self::assertEquals('Smith', $response->getTransactionResponseField(15));
        self::assertEquals('Company Name', $response->getTransactionResponseField(16));
        self::assertEquals('123 Main Street', $response->getTransactionResponseField(17));
        self::assertEquals('Townsville', $response->getTransactionResponseField(18));
        self::assertEquals('NJ', $response->getTransactionResponseField(19));
        self::assertEquals('12345', $response->getTransactionResponseField(20));
        self::assertEquals('United States', $response->getTransactionResponseField(21));
        self::assertEquals('800-555-1234', $response->getTransactionResponseField(22));
        self::assertEquals('800-555-1235', $response->getTransactionResponseField(23));
        self::assertEquals('user@example.com', $response->getTransactionResponseField(24));
        self::assertEquals('John', $response->getTransactionResponseField(25));
        self::assertEquals('Smith', $response->getTransactionResponseField(26));
        self::assertEquals('Other Company Name', $response->getTransactionResponseField(27));
        self::assertEquals('123 Main Street', $response->getTransactionResponseField(28));
        self::assertEquals('Townsville', $response->getTransactionResponseField(29));
        self::assertEquals('NJ', $response->getTransactionResponseField(30));
        self::assertEquals('12345', $response->getTransactionResponseField(31));
        self::assertEquals('United States', $response->getTransactionResponseField(32));
        self::assertEquals('1.00', $response->getTransactionResponseField(33));
        self::assertEquals('2.00', $response->getTransactionResponseField(34));
        self::assertEquals('3.00', $response->getTransactionResponseField(35));
        self::assertEquals('FALSE', $response->getTransactionResponseField(36));
        self::assertEquals('PONUM000001', $response->getTransactionResponseField(37));
        self::assertEquals('D3B20D6194B0E86C03A18987300E781C', $response->getTransactionResponseField(38));
        self::assertEquals('P', $response->getTransactionResponseField(39));
        self::assertEquals('2', $response->getTransactionResponseField(40));
        self::assertEquals('XXXX1111', $response->getTransactionResponseField(51));
        self::assertEquals('Visa', $response->getTransactionResponseField(52));
    }

    /**
    * @covers            \JohnConde\Authnet\TransactionResponse::getTransactionResponseField()
    * @covers            \JohnConde\Authnet\AuthnetJsonResponse::__construct()
    */
    public function testDirectResponse() : void
    {
        $responseJson = '{
           "directResponse":"1,1,1,This transaction has been approved.,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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

        $response = new AuthnetJsonResponse($responseJson);

        self::assertEquals('1', $response->getTransactionResponseField('ResponseCode'));
        self::assertEquals('1', $response->getTransactionResponseField('ResponseSubcode'));
        self::assertEquals('1', $response->getTransactionResponseField('ResponseReasonCode'));
        self::assertEquals('This transaction has been approved.', $response->getTransactionResponseField('ResponseReasonText'));
        self::assertEquals('902R0T', $response->getTransactionResponseField('AuthorizationCode'));
        self::assertEquals('Y', $response->getTransactionResponseField('AVSResponse'));
        self::assertEquals('2230582306', $response->getTransactionResponseField('TransactionID'));
        self::assertEquals('INV000001', $response->getTransactionResponseField('InvoiceNumber'));
        self::assertEquals('description of transaction', $response->getTransactionResponseField('Description'));
        self::assertEquals('10.95', $response->getTransactionResponseField('Amount'));
        self::assertEquals('CC', $response->getTransactionResponseField('Method'));
        self::assertEquals('auth_capture', $response->getTransactionResponseField('TransactionType'));
        self::assertEquals('12345', $response->getTransactionResponseField('CustomerID'));
        self::assertEquals('John', $response->getTransactionResponseField('FirstName'));
        self::assertEquals('Smith', $response->getTransactionResponseField('LastName'));
        self::assertEquals('Company Name', $response->getTransactionResponseField('Company'));
        self::assertEquals('123 Main Street', $response->getTransactionResponseField('Address'));
        self::assertEquals('Townsville', $response->getTransactionResponseField('City'));
        self::assertEquals('NJ', $response->getTransactionResponseField('State'));
        self::assertEquals('12345', $response->getTransactionResponseField('ZipCode'));
        self::assertEquals('United States', $response->getTransactionResponseField('Country'));
        self::assertEquals('800-555-1234', $response->getTransactionResponseField('Phone'));
        self::assertEquals('800-555-1235', $response->getTransactionResponseField('Fax'));
        self::assertEquals('user@example.com', $response->getTransactionResponseField('EmailAddress'));
        self::assertEquals('John', $response->getTransactionResponseField('ShipToFirstName'));
        self::assertEquals('Smith', $response->getTransactionResponseField('ShipToLastName'));
        self::assertEquals('Other Company Name', $response->getTransactionResponseField('ShipToCompany'));
        self::assertEquals('123 Main Street', $response->getTransactionResponseField('ShipToAddress'));
        self::assertEquals('Townsville', $response->getTransactionResponseField('ShipToCity'));
        self::assertEquals('NJ', $response->getTransactionResponseField('ShipToState'));
        self::assertEquals('12345', $response->getTransactionResponseField('ShipToZip'));
        self::assertEquals('United States', $response->getTransactionResponseField('ShipToCountry'));
        self::assertEquals('1.00', $response->getTransactionResponseField('Tax'));
        self::assertEquals('2.00', $response->getTransactionResponseField('Duty'));
        self::assertEquals('3.00', $response->getTransactionResponseField('Freight'));
        self::assertEquals('FALSE', $response->getTransactionResponseField('TaxExempt'));
        self::assertEquals('PONUM000001', $response->getTransactionResponseField('PurchaseOrderNumber'));
        self::assertEquals('D3B20D6194B0E86C03A18987300E781C', $response->getTransactionResponseField('MD5Hash'));
        self::assertEquals('P', $response->getTransactionResponseField('CardCodeResponse'));
        self::assertEquals('2', $response->getTransactionResponseField('CardholderAuthenticationVerificationResponse'));
        self::assertEquals('XXXX1111', $response->getTransactionResponseField('AccountNumber'));
        self::assertEquals('Visa', $response->getTransactionResponseField('CardType'));
    }

    /**
     * @covers            \JohnConde\Authnet\TransactionResponse::getTransactionResponseField()
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::__construct()
     */
    public function testValidationDirectResponse() : void
    {
        $responseJson = '{
           "customerPaymentProfileId":"28821903",
           "validationDirectResponse":"1,1,1,This transaction has been approved.,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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

        $response = new AuthnetJsonResponse($responseJson);

        self::assertEquals('1', $response->getTransactionResponseField('ResponseCode'));
        self::assertEquals('1', $response->getTransactionResponseField('ResponseSubcode'));
        self::assertEquals('1', $response->getTransactionResponseField('ResponseReasonCode'));
        self::assertEquals('This transaction has been approved.', $response->getTransactionResponseField('ResponseReasonText'));
        self::assertEquals('902R0T', $response->getTransactionResponseField('AuthorizationCode'));
        self::assertEquals('Y', $response->getTransactionResponseField('AVSResponse'));
        self::assertEquals('2230582306', $response->getTransactionResponseField('TransactionID'));
        self::assertEquals('INV000001', $response->getTransactionResponseField('InvoiceNumber'));
        self::assertEquals('description of transaction', $response->getTransactionResponseField('Description'));
        self::assertEquals('10.95', $response->getTransactionResponseField('Amount'));
        self::assertEquals('CC', $response->getTransactionResponseField('Method'));
        self::assertEquals('auth_capture', $response->getTransactionResponseField('TransactionType'));
        self::assertEquals('12345', $response->getTransactionResponseField('CustomerID'));
        self::assertEquals('John', $response->getTransactionResponseField('FirstName'));
        self::assertEquals('Smith', $response->getTransactionResponseField('LastName'));
        self::assertEquals('Company Name', $response->getTransactionResponseField('Company'));
        self::assertEquals('123 Main Street', $response->getTransactionResponseField('Address'));
        self::assertEquals('Townsville', $response->getTransactionResponseField('City'));
        self::assertEquals('NJ', $response->getTransactionResponseField('State'));
        self::assertEquals('12345', $response->getTransactionResponseField('ZipCode'));
        self::assertEquals('United States', $response->getTransactionResponseField('Country'));
        self::assertEquals('800-555-1234', $response->getTransactionResponseField('Phone'));
        self::assertEquals('800-555-1235', $response->getTransactionResponseField('Fax'));
        self::assertEquals('user@example.com', $response->getTransactionResponseField('EmailAddress'));
        self::assertEquals('John', $response->getTransactionResponseField('ShipToFirstName'));
        self::assertEquals('Smith', $response->getTransactionResponseField('ShipToLastName'));
        self::assertEquals('Other Company Name', $response->getTransactionResponseField('ShipToCompany'));
        self::assertEquals('123 Main Street', $response->getTransactionResponseField('ShipToAddress'));
        self::assertEquals('Townsville', $response->getTransactionResponseField('ShipToCity'));
        self::assertEquals('NJ', $response->getTransactionResponseField('ShipToState'));
        self::assertEquals('12345', $response->getTransactionResponseField('ShipToZip'));
        self::assertEquals('United States', $response->getTransactionResponseField('ShipToCountry'));
        self::assertEquals('1.00', $response->getTransactionResponseField('Tax'));
        self::assertEquals('2.00', $response->getTransactionResponseField('Duty'));
        self::assertEquals('3.00', $response->getTransactionResponseField('Freight'));
        self::assertEquals('FALSE', $response->getTransactionResponseField('TaxExempt'));
        self::assertEquals('PONUM000001', $response->getTransactionResponseField('PurchaseOrderNumber'));
        self::assertEquals('D3B20D6194B0E86C03A18987300E781C', $response->getTransactionResponseField('MD5Hash'));
        self::assertEquals('P', $response->getTransactionResponseField('CardCodeResponse'));
        self::assertEquals('2', $response->getTransactionResponseField('CardholderAuthenticationVerificationResponse'));
        self::assertEquals('XXXX1111', $response->getTransactionResponseField('AccountNumber'));
        self::assertEquals('Visa', $response->getTransactionResponseField('CardType'));
    }
}
