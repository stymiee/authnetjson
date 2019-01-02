<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*************************************************************************************************

Use the CIM JSON API to create a customer profile with multiple payment accounts

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
  "createCustomerProfileRequest": {
    "merchantAuthentication": {
      "name": "8zY2zT32",
      "transactionKey": "4WDx9a97v5DKY67a"
    },
    "profile": {
      "merchantCustomerId": "52353345",
      "email": "user@example.com",
      "paymentProfiles": [
        {
          "customerType": "individual",
          "billTo": {
            "firstName": "John",
            "lastName": "Smith",
            "address": "12345 Main Street",
            "city": "Townsville",
            "state": "NJ",
            "zip": "12345",
            "phoneNumber": "800-555-1234"
          },
          "payment": {
            "creditCard": {
              "cardNumber": "5555555555554444",
              "expirationDate": "2023-08"
            }
          }
        },
        {
          "customerType": "individual",
          "billTo": {
            "firstName": "John",
            "lastName": "Smithberg",
            "address": "42 Main Street",
            "city": "Townsville",
            "state": "NJ",
            "zip": "12345",
            "phoneNumber": "800-555-1234"
          },
          "payment": {
            "creditCard": {
              "cardNumber": "5105105105105100",
              "expirationDate": "2023-09"
            }
          }
        }
      ],
      "shipToList": {
        "firstName": "John",
        "lastName": "Smith",
        "address": "12345 Main Street",
        "city": "Townsville",
        "state": "NJ",
        "zip": "12345",
        "phoneNumber": "800-555-1234"
      }
    },
    "validationMode": "liveMode"
  }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
  "customerProfileId": "1506322353",
  "customerPaymentProfileIdList": [
    "1505667207",
    "1505667208"
  ],
  "customerShippingAddressIdList": [
    "1505655763"
  ],
  "validationDirectResponseList": [
    "1,1,1,This transaction has been approved.,A2FD5O,Y,40023515435,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,52353345,John,Smith,,12345 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,,,,,,,,,0.00,0.00,0.00,FALSE,none,32573C7D03376A9052AACA73835EDAEF,P,2,,,,,,,,,,,XXXX4444,MasterCard,,,,,,,,,,,,,,,,,",
    "1,1,1,This transaction has been approved.,AO13Y1,Y,40023515436,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,52353345,John,Smithberg,,42 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,,,,,,,,,0.00,0.00,0.00,FALSE,none,5B937D29D29F261776859B50DC1C3CF6,P,2,,,,,,,,,,,XXXX5100,MasterCard,,,,,,,,,,,,,,,,,"
  ],
  "messages": {
    "resultCode": "Ok",
    "message": [
      {
        "code": "I00001",
        "text": "Successful."
      }
    ]
  }
}

 *************************************************************************************************/

namespace JohnConde\Authnet;

require('../../config.inc.php');
require('../../src/autoload.php');

$request  = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
$response = $request->createCustomerProfileRequest([
    'profile' => [
        'merchantCustomerId' => '52353345',
        'email' => 'user@example.com',
        'paymentProfiles' => [
            [
                'customerType'=> 'individual',
                'billTo' => [
                    'firstName' => 'John',
                    'lastName' => 'Smith',
                    'address' => '12345 Main Street',
                    'city' => 'Townsville',
                    'state' => 'NJ',
                    'zip' => '12345',
                    'phoneNumber' => '800-555-1234'
                ],
                'payment' => [
                    'creditCard' => [
                        'cardNumber' => '5555555555554444',
                        'expirationDate' => '2023-08',
                    ],
                ],
            ],
            [
                'customerType'=> 'individual',
                'billTo' => [
                    'firstName' => 'John',
                    'lastName' => 'Smithberg',
                    'address' => '42 Main Street',
                    'city' => 'Townsville',
                    'state' => 'NJ',
                    'zip' => '12345',
                    'phoneNumber' => '800-555-1234'
                ],
                'payment' => [
                    'creditCard' => [
                        'cardNumber' => '5105105105105100',
                        'expirationDate' => '2023-09',
                    ],
                ],
            ],
        ],
        'shipToList' => [
            'firstName' => 'John',
            'lastName' => 'Smith',
            'address' => '12345 Main Street',
            'city' => 'Townsville',
            'state' => 'NJ',
            'zip' => '12345',
            'phoneNumber' => '800-555-1234'
        ],
    ],
    'validationMode' => 'liveMode'
]);
?>

<!DOCTYPE html>
<html>
<html lang="en">
<head>
    <title></title>
    <style type="text/css">
        table
        {
            border: 1px solid #cccccc;
            margin: auto;
            border-collapse: collapse;
            max-width: 90%;
        }

        table td
        {
            padding: 3px 5px;
            vertical-align: top;
            border-top: 1px solid #cccccc;
        }

        pre
        {
            overflow-x: auto; /* Use horizontal scroller if needed; for Firefox 2, not needed in Firefox 3 */
            white-space: pre-wrap; /* css-3 */
            white-space: -moz-pre-wrap !important; /* Mozilla, since 1999 */
            white-space: -pre-wrap; /* Opera 4-6 */
            white-space: -o-pre-wrap; /* Opera 7 */ /*
                width: 99%; */
            word-wrap: break-word; /* Internet Explorer 5.5+ */
        }

        table th
        {
            background: #e5e5e5;
            color: #666666;
        }

        h1, h2
        {
            text-align: center;
        }
    </style>
</head>
<body>
<h1>
    CIM :: Create Customer Profile
</h1>
<h2>
    Results
</h2>
<table>
    <tr>
        <th>Response</th>
        <td><?php echo $response->messages->resultCode; ?></td>
    </tr>
    <tr>
        <th>Successful?</th>
        <td><?php echo ($response->isSuccessful()) ? 'yes' : 'no'; ?></td>
    </tr>
    <tr>
        <th>Error?</th>
        <td><?php echo ($response->isError()) ? 'yes' : 'no'; ?></td>
    </tr>
    <tr>
        <th>Code</th>
        <td><?php echo $response->messages->message[0]->code; ?></td>
    </tr>
    <tr>
        <th>Message</th>
        <td><?php echo $response->messages->message[0]->text; ?></td>
    </tr>
    <?php if ($response->isSuccessful()) : ?>
        <tr>
            <th>Customer Profile ID</th>
            <td><?php echo $response->customerProfileId; ?></td>
        </tr>
        <tr>
            <th>Customer Payment Profile IDs</th>
            <td><?php echo implode(', ', $response->customerPaymentProfileIdList); ?></td>
        </tr>
        <tr>
            <th>Customer Shipping Address ID</th>
            <td><?php echo $response->customerShippingAddressIdList[0]; ?></td>
        </tr>
    <?php endif; ?>
</table>
<h2>
    Raw Input/Output
</h2>
<?php
echo $request, $response;
?>
</body>
</html>
