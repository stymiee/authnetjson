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
      "name": "",
      "transactionKey": ""
    },
    "profile": {
      "merchantCustomerId": "12345",
      "email": "user@example.com",
      "paymentProfiles": {
        "billTo": {
          "firstName": "John",
          "lastName": "Smith",
          "address": "123 Main Street",
          "city": "Townsville",
          "state": "NJ",
          "zip": "12345",
          "phoneNumber": "800-555-1234"
        },
        "payment": {
          "creditCard": {
            "cardNumber": "4111111111111111",
            "expirationDate": "2016-08"
          }
        }
      },
      "shipToList": {
        "firstName": "John",
        "lastName": "Smith",
        "address": "123 Main Street",
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
  "customerProfileId": "1506307937",
  "customerPaymentProfileIdList": [
    "1505651943",
    "1505651944"
  ],
  "customerShippingAddressIdList": [
    "1505640104"
  ],
  "validationDirectResponseList": [
    "1,1,1,This transaction has been approved.,AMC5UR,Y,40023389473,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,5235345,John,Smith,,1234 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,,,,,,,,,0.00,0.00,0.00,FALSE,none,238BC844E4A5C2A0B8F6BB4DDCCCAC99,P,2,,,,,,,,,,,XXXX4444,MasterCard,,,,,,,,,,,,,,,,,",
    "1,1,1,This transaction has been approved.,AF0YC7,Y,40023389474,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,5235345,John,Smithberg,,4 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,,,,,,,,,0.00,0.00,0.00,FALSE,none,26A41377525AE7543ECB46AE4D060C12,P,2,,,,,,,,,,,XXXX5100,MasterCard,,,,,,,,,,,,,,,,,"
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
        'merchantCustomerId' => '5235345',
        'email' => 'user@example.com',
        'paymentProfiles' => [
            [
                'customerType'=> 'individual',
                'billTo' => [
                    'firstName' => 'John',
                    'lastName' => 'Smith',
                    'address' => '1234 Main Street',
                    'city' => 'Townsville',
                    'state' => 'NJ',
                    'zip' => '12345',
                    'phoneNumber' => '800-555-1234'
                ],
                'payment' => [
                    'creditCard' => [
                        'cardNumber' => '5555555555554444',
                        'expirationDate' => '2024-08',
                    ],
                ],
            ],
            [
                'customerType'=> 'individual',
                'billTo' => [
                    'firstName' => 'John',
                    'lastName' => 'Smithberg',
                    'address' => '4 Main Street',
                    'city' => 'Townsville',
                    'state' => 'NJ',
                    'zip' => '12345',
                    'phoneNumber' => '800-555-1234'
                ],
                'payment' => [
                    'creditCard' => [
                        'cardNumber' => '5105105105105100',
                        'expirationDate' => '2024-09',
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
