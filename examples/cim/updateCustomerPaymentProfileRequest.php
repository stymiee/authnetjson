<?php
/*************************************************************************************************

Use the CIM JSON API to update a customer profile

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "updateCustomerPaymentProfileRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "customerProfileId":"31390172",
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
         },
         "customerPaymentProfileId":"28393490"
      }
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
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
    $response = $request->updateCustomerPaymentProfileRequest(array(
        'customerProfileId' => '31390172',
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
            ),
            'customerPaymentProfileId' => '28393490'
        )
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
            CIM :: Update Customer Profile
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
        </table>
        <h2>
            Raw Input/Output
        </h2>
<?php
    echo $request, $response;
?>
    </body>
</html>
