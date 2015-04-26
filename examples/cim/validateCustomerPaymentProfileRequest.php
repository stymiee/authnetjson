<?php
/*************************************************************************************************

Use the CIM JSON API to validate a payment profile

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "validateCustomerPaymentProfileRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "customerProfileId":"31390172",
      "customerPaymentProfileId":"28393490",
      "customerShippingAddressId":"29366174",
      "validationMode":"liveMode"
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "directResponse":"1,1,1,This transaction has been approved.,5Q8DGW,Y,2230582939,none,Test transaction for ValidateCustomerPaymentProfile.,0.00,CC,auth_only,12345,John,Smith,,123 Main Street,Townsville,NJ,12345,,800-555-1234,,user@example.com,John,Smith,,123 Main Street,Townsville,NJ,12345,,0.00,0.00,0.00,FALSE,none,6160655F3F4DF72144DCE15C0AEE15B1,,2,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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
    $response = $request->validateCustomerPaymentProfileRequest(array(
        'customerProfileId' => '31390172',
        'customerPaymentProfileId' => '28393490',
        'customerShippingAddressId' => '29366174',
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
            CIM :: Validate Payment Profile
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
