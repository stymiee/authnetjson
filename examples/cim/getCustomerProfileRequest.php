<?php
/*************************************************************************************************

Use the CIM JSON API to retrieve a customer profile

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "getCustomerProfileRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "customerProfileId":"31390172"
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "profile":{
      "paymentProfiles":[
         {
            "customerPaymentProfileId":"28393490",
            "payment":{
               "creditCard":{
                  "cardNumber":"XXXX1111",
                  "expirationDate":"XXXX"
               }
            },
            "customerTypeSpecified":false,
            "billTo":{
               "phoneNumber":"800-555-1234",
               "firstName":"John",
               "lastName":"Smith",
               "address":"123 Main Street",
               "city":"Townsville",
               "state":"NJ",
               "zip":"12345"
            }
         }
      ],
      "shipToList":[
         {
            "customerAddressId":"29366174",
            "phoneNumber":"800-555-1234",
            "firstName":"John",
            "lastName":"Smith",
            "address":"123 Main Street",
            "city":"Townsville",
            "state":"NJ",
            "zip":"12345"
         },
         {
            "customerAddressId":"29870028",
            "phoneNumber":"800-555-1234",
            "faxNumber":"800-555-1234",
            "firstName":"John",
            "lastName":"Doe",
            "company":"",
            "address":"123 Main St.",
            "city":"Bellevue",
            "state":"WA",
            "zip":"98004",
            "country":"USA"
         }
      ],
      "customerProfileId":"31390172",
      "merchantCustomerId":"12345",
      "email":"user@example.com"
   },
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
    $response = $request->getCustomerProfileRequest(array(
        'customerProfileId' => '31390172'
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
            CIM :: Get Customer Profile ID
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
                <th>merchantCustomerId</th>
                <td><?php echo $response->profile->merchantCustomerId; ?></td>
            </tr>
            <tr>
                <th>email</th>
                <td><?php echo $response->profile->email; ?></td>
            </tr>
            <tr>
                <th>Payment Profile IDs</th>
                <td>
<?php
    foreach ($json->profile->paymentProfiles as $profile)
    {
        echo $profile->customerPaymentProfileId . ', ';
    }
?>
                </td>
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
