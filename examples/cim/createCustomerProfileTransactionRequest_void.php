<?php
/*************************************************************************************************

Use the CIM JSON API to void a transaction

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "createCustomerProfileTransactionRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "transaction":{
         "profileTransVoid":{
            "customerProfileId":"31390172",
            "customerPaymentProfileId":"28393490",
            "customerShippingAddressId":"29366174",
            "transId":"2230582868"
         }
      },
      "extraOptions":"x_customer_ip=100.0.0.1"
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "directResponse":"1,1,1,This transaction has been approved.,OWW0UU,P,2230582868,INV000001,,0.00,CC,void,12345,,,,,,,12345,,,,,,,,,,,,,,,,,,0C7394DFC38A5BDC5737A354CE67B421,,,,,,,,,,,,,XXXX1111,Visa,,,,,,,,,,,,,,,,,29366174",
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
    $response = $request->createCustomerProfileTransactionRequest(array(
        'transaction' => array(
            'profileTransVoid' => array(
                'customerProfileId' => '31390172',
                'customerPaymentProfileId' => '28393490',
                'customerShippingAddressId' => '29366174',
                'transId' => '2230582868'
            )
        ),
        'extraOptions' => 'x_customer_ip=100.0.0.1'
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
            CIM :: Void
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
