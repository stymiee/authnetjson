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

class TransactionResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers            \JohnConde\Authnet\TransactionResponse::__construct()
     * @covers            \JohnConde\Authnet\TransactionResponse::getTransactionResponseField()
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::getTransactionResponseField()
     */
    public function testTransactionResponse()
    {
        $transactionIfo = '1,1,1,This transaction has been approved.,902R0T,Y,2230582306,INV000001,description of transaction,10.95,CC,auth_capture,12345,John,Smith,Company Name,123 Main Street,Townsville,NJ,12345,United States,800-555-1234,800-555-1235,user@example.com,John,Smith,Other Company Name,123 Main Street,Townsville,NJ,12345,United States,1.00,2.00,3.00,FALSE,PONUM000001,D3B20D6194B0E86C03A18987300E781C,P,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174';

        $response = new TransactionResponse($transactionIfo);

        $this->assertEquals('1', $response->getTransactionResponseField('ResponseCode'));
        $this->assertEquals('1', $response->getTransactionResponseField('ResponseSubcode'));
        $this->assertEquals('1', $response->getTransactionResponseField('ResponseReasonCode'));
        $this->assertEquals('This transaction has been approved.', $response->getTransactionResponseField('ResponseReasonText'));
        $this->assertEquals('902R0T', $response->getTransactionResponseField('AuthorizationCode'));
        $this->assertEquals('Y', $response->getTransactionResponseField('AVSResponse'));
        $this->assertEquals('2230582306', $response->getTransactionResponseField('TransactionID'));
        $this->assertEquals('INV000001', $response->getTransactionResponseField('InvoiceNumber'));
        $this->assertEquals('description of transaction', $response->getTransactionResponseField('Description'));
        $this->assertEquals('10.95', $response->getTransactionResponseField('Amount'));
        $this->assertEquals('CC', $response->getTransactionResponseField('Method'));
        $this->assertEquals('auth_capture', $response->getTransactionResponseField('TransactionType'));
        $this->assertEquals('12345', $response->getTransactionResponseField('CustomerID'));
        $this->assertEquals('John', $response->getTransactionResponseField('FirstName'));
        $this->assertEquals('Smith', $response->getTransactionResponseField('LastName'));
        $this->assertEquals('Company Name', $response->getTransactionResponseField('Company'));
        $this->assertEquals('123 Main Street', $response->getTransactionResponseField('Address'));
        $this->assertEquals('Townsville', $response->getTransactionResponseField('City'));
        $this->assertEquals('NJ', $response->getTransactionResponseField('State'));
        $this->assertEquals('12345', $response->getTransactionResponseField('ZipCode'));
        $this->assertEquals('United States', $response->getTransactionResponseField('Country'));
        $this->assertEquals('800-555-1234', $response->getTransactionResponseField('Phone'));
        $this->assertEquals('800-555-1235', $response->getTransactionResponseField('Fax'));
        $this->assertEquals('user@example.com', $response->getTransactionResponseField('EmailAddress'));
        $this->assertEquals('John', $response->getTransactionResponseField('ShipToFirstName'));
        $this->assertEquals('Smith', $response->getTransactionResponseField('ShipToLastName'));
        $this->assertEquals('Other Company Name', $response->getTransactionResponseField('ShipToCompany'));
        $this->assertEquals('123 Main Street', $response->getTransactionResponseField('ShipToAddress'));
        $this->assertEquals('Townsville', $response->getTransactionResponseField('ShipToCity'));
        $this->assertEquals('NJ', $response->getTransactionResponseField('ShipToState'));
        $this->assertEquals('12345', $response->getTransactionResponseField('ShipToZip'));
        $this->assertEquals('United States', $response->getTransactionResponseField('ShipToCountry'));
        $this->assertEquals('1.00', $response->getTransactionResponseField('Tax'));
        $this->assertEquals('2.00', $response->getTransactionResponseField('Duty'));
        $this->assertEquals('3.00', $response->getTransactionResponseField('Freight'));
        $this->assertEquals('FALSE', $response->getTransactionResponseField('TaxExempt'));
        $this->assertEquals('PONUM000001', $response->getTransactionResponseField('PurchaseOrderNumber'));
        $this->assertEquals('D3B20D6194B0E86C03A18987300E781C', $response->getTransactionResponseField('MD5Hash'));
        $this->assertEquals('P', $response->getTransactionResponseField('CardCodeResponse'));
        $this->assertEquals('2', $response->getTransactionResponseField('CardholderAuthenticationVerificationResponse'));
        $this->assertEquals('XXXX1111', $response->getTransactionResponseField('AccountNumber'));
        $this->assertEquals('Visa', $response->getTransactionResponseField('CardType'));

        $this->assertEquals('1', $response->getTransactionResponseField(1));
        $this->assertEquals('1', $response->getTransactionResponseField(2));
        $this->assertEquals('1', $response->getTransactionResponseField(3));
        $this->assertEquals('This transaction has been approved.', $response->getTransactionResponseField(4));
        $this->assertEquals('902R0T', $response->getTransactionResponseField(5));
        $this->assertEquals('Y', $response->getTransactionResponseField(6));
        $this->assertEquals('2230582306', $response->getTransactionResponseField(7));
        $this->assertEquals('INV000001', $response->getTransactionResponseField(8));
        $this->assertEquals('description of transaction', $response->getTransactionResponseField(9));
        $this->assertEquals('10.95', $response->getTransactionResponseField(10));
        $this->assertEquals('CC', $response->getTransactionResponseField(11));
        $this->assertEquals('auth_capture', $response->getTransactionResponseField(12));
        $this->assertEquals('12345', $response->getTransactionResponseField(13));
        $this->assertEquals('John', $response->getTransactionResponseField(14));
        $this->assertEquals('Smith', $response->getTransactionResponseField(15));
        $this->assertEquals('Company Name', $response->getTransactionResponseField(16));
        $this->assertEquals('123 Main Street', $response->getTransactionResponseField(17));
        $this->assertEquals('Townsville', $response->getTransactionResponseField(18));
        $this->assertEquals('NJ', $response->getTransactionResponseField(19));
        $this->assertEquals('12345', $response->getTransactionResponseField(20));
        $this->assertEquals('United States', $response->getTransactionResponseField(21));
        $this->assertEquals('800-555-1234', $response->getTransactionResponseField(22));
        $this->assertEquals('800-555-1235', $response->getTransactionResponseField(23));
        $this->assertEquals('user@example.com', $response->getTransactionResponseField(24));
        $this->assertEquals('John', $response->getTransactionResponseField(25));
        $this->assertEquals('Smith', $response->getTransactionResponseField(26));
        $this->assertEquals('Other Company Name', $response->getTransactionResponseField(27));
        $this->assertEquals('123 Main Street', $response->getTransactionResponseField(28));
        $this->assertEquals('Townsville', $response->getTransactionResponseField(29));
        $this->assertEquals('NJ', $response->getTransactionResponseField(30));
        $this->assertEquals('12345', $response->getTransactionResponseField(31));
        $this->assertEquals('United States', $response->getTransactionResponseField(32));
        $this->assertEquals('1.00', $response->getTransactionResponseField(33));
        $this->assertEquals('2.00', $response->getTransactionResponseField(34));
        $this->assertEquals('3.00', $response->getTransactionResponseField(35));
        $this->assertEquals('FALSE', $response->getTransactionResponseField(36));
        $this->assertEquals('PONUM000001', $response->getTransactionResponseField(37));
        $this->assertEquals('D3B20D6194B0E86C03A18987300E781C', $response->getTransactionResponseField(38));
        $this->assertEquals('P', $response->getTransactionResponseField(39));
        $this->assertEquals('2', $response->getTransactionResponseField(40));
        $this->assertEquals('XXXX1111', $response->getTransactionResponseField(51));
        $this->assertEquals('Visa', $response->getTransactionResponseField(52));
    }

    /**
    * @covers            \JohnConde\Authnet\TransactionResponse::getTransactionResponseField()
    * @covers            \JohnConde\Authnet\AuthnetJsonResponse::__construct()
    */
    public function testDirectResponse()
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

        $this->assertEquals('1', $response->getTransactionResponseField('ResponseCode'));
        $this->assertEquals('1', $response->getTransactionResponseField('ResponseSubcode'));
        $this->assertEquals('1', $response->getTransactionResponseField('ResponseReasonCode'));
        $this->assertEquals('This transaction has been approved.', $response->getTransactionResponseField('ResponseReasonText'));
        $this->assertEquals('902R0T', $response->getTransactionResponseField('AuthorizationCode'));
        $this->assertEquals('Y', $response->getTransactionResponseField('AVSResponse'));
        $this->assertEquals('2230582306', $response->getTransactionResponseField('TransactionID'));
        $this->assertEquals('INV000001', $response->getTransactionResponseField('InvoiceNumber'));
        $this->assertEquals('description of transaction', $response->getTransactionResponseField('Description'));
        $this->assertEquals('10.95', $response->getTransactionResponseField('Amount'));
        $this->assertEquals('CC', $response->getTransactionResponseField('Method'));
        $this->assertEquals('auth_capture', $response->getTransactionResponseField('TransactionType'));
        $this->assertEquals('12345', $response->getTransactionResponseField('CustomerID'));
        $this->assertEquals('John', $response->getTransactionResponseField('FirstName'));
        $this->assertEquals('Smith', $response->getTransactionResponseField('LastName'));
        $this->assertEquals('Company Name', $response->getTransactionResponseField('Company'));
        $this->assertEquals('123 Main Street', $response->getTransactionResponseField('Address'));
        $this->assertEquals('Townsville', $response->getTransactionResponseField('City'));
        $this->assertEquals('NJ', $response->getTransactionResponseField('State'));
        $this->assertEquals('12345', $response->getTransactionResponseField('ZipCode'));
        $this->assertEquals('United States', $response->getTransactionResponseField('Country'));
        $this->assertEquals('800-555-1234', $response->getTransactionResponseField('Phone'));
        $this->assertEquals('800-555-1235', $response->getTransactionResponseField('Fax'));
        $this->assertEquals('user@example.com', $response->getTransactionResponseField('EmailAddress'));
        $this->assertEquals('John', $response->getTransactionResponseField('ShipToFirstName'));
        $this->assertEquals('Smith', $response->getTransactionResponseField('ShipToLastName'));
        $this->assertEquals('Other Company Name', $response->getTransactionResponseField('ShipToCompany'));
        $this->assertEquals('123 Main Street', $response->getTransactionResponseField('ShipToAddress'));
        $this->assertEquals('Townsville', $response->getTransactionResponseField('ShipToCity'));
        $this->assertEquals('NJ', $response->getTransactionResponseField('ShipToState'));
        $this->assertEquals('12345', $response->getTransactionResponseField('ShipToZip'));
        $this->assertEquals('United States', $response->getTransactionResponseField('ShipToCountry'));
        $this->assertEquals('1.00', $response->getTransactionResponseField('Tax'));
        $this->assertEquals('2.00', $response->getTransactionResponseField('Duty'));
        $this->assertEquals('3.00', $response->getTransactionResponseField('Freight'));
        $this->assertEquals('FALSE', $response->getTransactionResponseField('TaxExempt'));
        $this->assertEquals('PONUM000001', $response->getTransactionResponseField('PurchaseOrderNumber'));
        $this->assertEquals('D3B20D6194B0E86C03A18987300E781C', $response->getTransactionResponseField('MD5Hash'));
        $this->assertEquals('P', $response->getTransactionResponseField('CardCodeResponse'));
        $this->assertEquals('2', $response->getTransactionResponseField('CardholderAuthenticationVerificationResponse'));
        $this->assertEquals('XXXX1111', $response->getTransactionResponseField('AccountNumber'));
        $this->assertEquals('Visa', $response->getTransactionResponseField('CardType'));
    }

    /**
     * @covers            \JohnConde\Authnet\TransactionResponse::getTransactionResponseField()
     * @covers            \JohnConde\Authnet\AuthnetJsonResponse::__construct()
     */
    public function testValidationDirectResponse()
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

        $this->assertEquals('1', $response->getTransactionResponseField('ResponseCode'));
        $this->assertEquals('1', $response->getTransactionResponseField('ResponseSubcode'));
        $this->assertEquals('1', $response->getTransactionResponseField('ResponseReasonCode'));
        $this->assertEquals('This transaction has been approved.', $response->getTransactionResponseField('ResponseReasonText'));
        $this->assertEquals('902R0T', $response->getTransactionResponseField('AuthorizationCode'));
        $this->assertEquals('Y', $response->getTransactionResponseField('AVSResponse'));
        $this->assertEquals('2230582306', $response->getTransactionResponseField('TransactionID'));
        $this->assertEquals('INV000001', $response->getTransactionResponseField('InvoiceNumber'));
        $this->assertEquals('description of transaction', $response->getTransactionResponseField('Description'));
        $this->assertEquals('10.95', $response->getTransactionResponseField('Amount'));
        $this->assertEquals('CC', $response->getTransactionResponseField('Method'));
        $this->assertEquals('auth_capture', $response->getTransactionResponseField('TransactionType'));
        $this->assertEquals('12345', $response->getTransactionResponseField('CustomerID'));
        $this->assertEquals('John', $response->getTransactionResponseField('FirstName'));
        $this->assertEquals('Smith', $response->getTransactionResponseField('LastName'));
        $this->assertEquals('Company Name', $response->getTransactionResponseField('Company'));
        $this->assertEquals('123 Main Street', $response->getTransactionResponseField('Address'));
        $this->assertEquals('Townsville', $response->getTransactionResponseField('City'));
        $this->assertEquals('NJ', $response->getTransactionResponseField('State'));
        $this->assertEquals('12345', $response->getTransactionResponseField('ZipCode'));
        $this->assertEquals('United States', $response->getTransactionResponseField('Country'));
        $this->assertEquals('800-555-1234', $response->getTransactionResponseField('Phone'));
        $this->assertEquals('800-555-1235', $response->getTransactionResponseField('Fax'));
        $this->assertEquals('user@example.com', $response->getTransactionResponseField('EmailAddress'));
        $this->assertEquals('John', $response->getTransactionResponseField('ShipToFirstName'));
        $this->assertEquals('Smith', $response->getTransactionResponseField('ShipToLastName'));
        $this->assertEquals('Other Company Name', $response->getTransactionResponseField('ShipToCompany'));
        $this->assertEquals('123 Main Street', $response->getTransactionResponseField('ShipToAddress'));
        $this->assertEquals('Townsville', $response->getTransactionResponseField('ShipToCity'));
        $this->assertEquals('NJ', $response->getTransactionResponseField('ShipToState'));
        $this->assertEquals('12345', $response->getTransactionResponseField('ShipToZip'));
        $this->assertEquals('United States', $response->getTransactionResponseField('ShipToCountry'));
        $this->assertEquals('1.00', $response->getTransactionResponseField('Tax'));
        $this->assertEquals('2.00', $response->getTransactionResponseField('Duty'));
        $this->assertEquals('3.00', $response->getTransactionResponseField('Freight'));
        $this->assertEquals('FALSE', $response->getTransactionResponseField('TaxExempt'));
        $this->assertEquals('PONUM000001', $response->getTransactionResponseField('PurchaseOrderNumber'));
        $this->assertEquals('D3B20D6194B0E86C03A18987300E781C', $response->getTransactionResponseField('MD5Hash'));
        $this->assertEquals('P', $response->getTransactionResponseField('CardCodeResponse'));
        $this->assertEquals('2', $response->getTransactionResponseField('CardholderAuthenticationVerificationResponse'));
        $this->assertEquals('XXXX1111', $response->getTransactionResponseField('AccountNumber'));
        $this->assertEquals('Visa', $response->getTransactionResponseField('CardType'));
    }
}