<?php
/*************************************************************************************************

Use the CIM JSON API to create a payment profile

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "createCustomerPaymentProfileRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "customerProfileId":"30582495",
      "paymentProfile":{
         "billTo":{
            "firstName":"John",
            "lastName":"Doe",
            "company":"",
            "address":"123 Main St.",
            "city":"Bellevue",
            "state":"WA",
            "zip":"98004",
            "country":"USA",
            "phoneNumber":"800-555-1234",
            "faxNumber":"800-555-1234"
         },
         "payment":{
            "creditCard":{
               "cardNumber":"4111111111111111",
               "expirationDate":"2016-08"
            }
         }
      },
      "validationMode":"liveMode"
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "customerPaymentProfileId":"28821903",
   "validationDirectResponse":"1,1,1,This transaction has been approved.,4DHVNH,Y,2230582188,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,none,John,Doe,,123 Main St.,Bellevue,WA,98004,USA,800-555-1234,800-555-1234,email@example.com,,,,,,,,,0.00,0.00,0.00,FALSE,none,E440D094322A0D406E01EDF9CE871A4F,,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,",
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
    $response = $request->createCustomerPaymentProfileRequest(array(
        'customerProfileId' => '30582495',
        'paymentProfile' => array(
            'billTo' => array(
                'firstName' => 'John',
                'lastName' => 'Doe',
                'company' => '',
                'address' => '123 Main St.',
                'city' => 'Bellevue',
                'state' => 'WA',
                'zip' => '98004',
                'country' => 'USA',
                'phoneNumber' => '800-555-1234',
                'faxNumber' => '800-555-1234'
            ),
            'payment' => array(
                'creditCard' => array(
                    'cardNumber' => '4111111111111111',
                    'expirationDate' => '2016-08'
                )
            )
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
            CIM :: Create Payment Profile
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
                <th>code</th>
                <td><?php echo $response->messages->message[0]->code; ?></td>
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
                <th>customerProfileId</th>
                <td><?php echo $response->customerPaymentProfileId; ?></td>
            </tr>
            <tr>
                <th>Transaction Approved?</th>
                <td><?php echo ($response->isApproved()) ? 'yes' : 'no'; ?></td>
            </tr>
        </table>
        <h2>
            Raw Input/Output
        </h2>
<?php
    echo $request, $response;
?>
    </body>
</html>
