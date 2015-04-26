<?php
/*************************************************************************************************

Use the AIM JSON API to send a customer a transaction receipt

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "sendCustomerTransactionReceiptRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "refId":2241729,
      "transId":"2165665581",
      "customerEmail":"user@example.com",
      "emailSettings":{
         "setting":{
            "settingName":"footerEmailReceipt",
            "settingValue":"some FOOTER stuff"
         }
      }
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "refId":"2241729",
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

**************************************************************************************************/

    namespace JohnConde\Authnet;

    require('../../config.inc.php');
    require('../../src/autoload.php');

    $request  = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $response = $request->sendCustomerTransactionReceiptRequest(array(
        'refId' => rand(1000000, 100000000),
        'transId' => '2165665581',
        'customerEmail' => 'user@example.com',
        'emailSettings' => array(
            'setting' => array(
                'settingName' => 'headerEmailReceipt',
                'settingValue' => 'some HEADER stuff'
            ),
            'setting' => array(
                'settingName' => 'footerEmailReceipt',
                'settingValue' => 'some FOOTER stuff'
            ),
        ),
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
            AIM :: Receipt Request
        </h1>
        <h2>
            Results
        </h2>
        <table>
            <tr>
                <th>Response</th>
                <td><?php echo $response->messages->resultCode; ?></td>
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
