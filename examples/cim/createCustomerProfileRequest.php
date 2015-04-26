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

Use the CIM JSON API to create a customer profile

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{  
   "createCustomerProfileRequest":{  
      "merchantAuthentication":{  
         "name":"",
         "transactionKey":""
      },
      "profile":{  
         "merchantCustomerId":"12345",
         "email":"user@example.com",
         "paymentProfiles":{  
            "billTo":{  
               "firstName":"John",
               "lastName":"Smith",
               "address":"123 Main Street",
               "city":"Townsville",
               "state":"NJ",
               "zip":"12345",
               "phoneNumber":"800-555-1234"
            },
            "payment":{  
               "creditCard":{  
                  "cardNumber":"4111111111111111",
                  "expirationDate":"2016-08"
               }
            }
         },
         "shipToList":{  
            "firstName":"John",
            "lastName":"Smith",
            "address":"123 Main Street",
            "city":"Townsville",
            "state":"NJ",
            "zip":"12345",
            "phoneNumber":"800-555-1234"
         }
      },
      "validationMode":"liveMode"
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{  
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
}

*************************************************************************************************/

    namespace JohnConde\Authnet;

    require('../../config.inc.php');
    require('../../src/autoload.php');

    $request  = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $response = $request->createCustomerProfileRequest(array(
            'profile' => array(
			'merchantCustomerId' => '123456',
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
	));
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
                <th>Customer Payment Profile ID</th>
                <td><?php echo $response->customerPaymentProfileIdList[0]; ?></td>
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
