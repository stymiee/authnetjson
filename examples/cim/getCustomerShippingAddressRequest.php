<?php
/*************************************************************************************************

Use the CIM JSON API to retrieve a shipping address

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "getCustomerShippingAddressRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      },
      "customerProfileId":"31390172",
      "customerAddressId":"29366174"
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "address":{
      "customerAddressId":"29366174",
      "phoneNumber":"800-555-1234",
      "firstName":"John",
      "lastName":"Smith",
      "address":"123 Main Street",
      "city":"Townsville",
      "state":"NJ",
      "zip":"12345"
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

use Exception;

require '../../config.inc.php';

try {
    $request = AuthnetApiFactory::getJsonApiHandler(
        AUTHNET_LOGIN,
        AUTHNET_TRANSKEY,
        AuthnetApiFactory::USE_DEVELOPMENT_SERVER
    );
    $response = $request->getCustomerShippingAddressRequest([
        'customerProfileId' => '31390172',
        'customerAddressId' => '29366174'
    ]);
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>CIM :: Get Shipping Address</title>
    <style>
        table { border: 1px solid #cccccc; margin: auto; border-collapse: collapse; max-width: 90%; }
        table td { padding: 3px 5px; vertical-align: top; border-top: 1px solid #cccccc; }
        pre { white-space: pre-wrap; }
        table th { background: #e5e5e5; color: #666666; }
        h1, h2 { text-align: center; }
    </style>
</head>
<body>
    <h1>
        CIM :: Get Shipping Address
    </h1>
    <h2>
        Results
    </h2>
    <table>
        <tr>
            <th>Successful?</th>
            <td><?= $response->isSuccessful() ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <th>Error?</th>
            <td><?= $response->isError() ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <th>Result Code</th>
            <td><?= $response->messages->resultCode ?></td>
        </tr>
        <tr>
            <th>Message Code</th>
            <td><?= $response->messages->message[0]->code ?></td>
        </tr>
        <tr>
            <th>Message</th>
            <td><?= $response->messages->message[0]->text ?></td>
        </tr>
        <tr>
            <th>First Name</th>
            <td><?= $response->address->firstName ?></td>
        </tr>
        <tr>
            <th>Last Name</th>
            <td><?= $response->address->lastName ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?= $response->address->address ?></td>
        </tr>
        <tr>
            <th>City</th>
            <td><?= $response->address->city ?></td>
        </tr>
        <tr>
            <th>State</th>
            <td><?= $response->address->state ?></td>
        </tr>
        <tr>
            <th>Zip</th>
            <td><?= $response->address->zip ?></td>
        </tr>
        <tr>
            <th>Phone Number</th>
            <td><?= $response->address->phoneNumber ?></td>
        </tr>
        <tr>
            <th>Customer Address Id</th>
            <td><?= $response->address->customerAddressId ?></td>
        </tr>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
