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

use Exception;

require '../../config.inc.php';

try {
    $request = AuthnetApiFactory::getJsonApiHandler(
        AUTHNET_LOGIN,
        AUTHNET_TRANSKEY,
        AuthnetApiFactory::USE_DEVELOPMENT_SERVER
    );
    $response = $request->getCustomerProfileRequest([
        'customerProfileId' => '31390172'
    ]);
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>CIM :: Get Customer Profile ID</title>
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
        CIM :: Get Customer Profile ID
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
        <?php if ($response->isSuccessful()) : ?>
        <tr>
            <th>Customer Profile Id</th>
            <td><?= $response->profile->customerProfileId ?></td>
        </tr>
        <tr>
            <th>Merchant Customer Id</th>
            <td><?= $response->profile->merchantCustomerId ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= $response->profile->email ?></td>
        </tr>
        <tr>
            <th>Payment Profiles</th>
            <td>
                <table>
                    <?php foreach ($response->paymentProfiles as $paymentProfile) : ?>
                    <tr>
                        <td>Customer Payment Profile Id</td><td><?= $paymentProfile->customerPaymentProfileId ?></td>
                        <td>Card Number</td><td><?= $paymentProfile->payment->creditCard->cardNumber ?></td>
                        <td>Expiration Date</td><td><?= $paymentProfile->payment->creditCard->expirationDate ?></td>
                        <td>Phone Number</td><td><?= $paymentProfile->billTo->phoneNumber ?></td>
                        <td>First Name</td><td><?= $paymentProfile->billTo->firstName ?></td>
                        <td>Last Name</td><td><?= $paymentProfile->billTo->lastName ?></td>
                        <td>Address</td><td><?= $paymentProfile->billTo->address ?></td>
                        <td>City</td><td><?= $paymentProfile->billTo->city ?></td>
                        <td>State</td><td><?= $paymentProfile->billTo->state ?></td>
                        <td>Zip</td><td><?= $paymentProfile->billTo->zip ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </td>
        </tr>
        <tr>
            <th>Ship To List</th>
            <td>
                <?php foreach ($response->shipToList as $shipTo) : ?>
                    Customer Address Id: <?= $shipTo->customerAddressId ?? '' ?><br>
                    Phone Number: <?= $shipTo->phoneNumber ?? '' ?> <br>
                    Fax Number: <?= $shipTo->faxNumber ?? '' ?> <br>
                    First Name: <?= $shipTo->firstName ?? '' ?> <br>
                    Last Name: <?= $shipTo->lastName ?? '' ?> <br>
                    Company: <?= $shipTo->company ?? '' ?> <br>
                    Address: <?= $shipTo->address ?? '' ?> <br>
                    City: <?= $shipTo->city ?? '' ?> <br>
                    State: <?= $shipTo->state ?? '' ?> <br>
                    Zip: <?= $shipTo->zip ?? '' ?> <br>
                    Country: <?= $shipTo->country ?? '' ?> <br>
                <?php endforeach; ?>
            </td>
        </tr>
        <?php endif; ?>
    </table>
    <h2>
        Raw Input/Output
    </h2>
<?= $request, $response ?>
</body>
</html>
