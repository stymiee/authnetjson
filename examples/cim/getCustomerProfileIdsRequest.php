<?php
/*************************************************************************************************

Use the CIM JSON API to retrieve a list of profile IDs

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
   "getCustomerProfileIdsRequest":{
      "merchantAuthentication":{
         "name":"",
         "transactionKey":""
      }
   }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
   "ids":[
      "20320494",
      "20320495",
      "20320496",
      "20320497",
      "20320499",
      "20382791",
      "20522161",
      "20522247",
      "20522344",
      "20522529",
      "20529466",
      "20532466",
      "20533743",
      "20533892",
      "20631798",
      "21176851",
      "21267776",
      "21267786",
      "21268552",
      "21268866",
      "21323330",
      "21387453",
      "21452273",
      "21503525",
      "21507048",
      "21520223",
      "21533869",
      "21630064",
      "21631076",
      "21644324",
      "21755205",
      "21783775",
      "22820980",
      "22853636",
      "22912790",
      "22913090",
      "23896146",
      "23942782",
      "24353242",
      "24415694",
      "24431080",
      "24873651",
      "24874921",
      "24875645",
      "25139838",
      "25286149",
      "25287624",
      "25697926",
      "25933750",
      "26564070",
      "26564773",
      "26583007",
      "26585538",
      "26585555",
      "26585578",
      "26586275",
      "26648484",
      "26648913",
      "27389717",
      "27667711",
      "27713150",
      "27984649",
      "27984720",
      "27984879",
      "28012856",
      "28023333",
      "28023355",
      "28023366",
      "28023374",
      "28421203",
      "28421294",
      "28440218",
      "28440239",
      "28440287",
      "28440368",
      "28473202",
      "28473492",
      "28474805",
      "28596453",
      "28705962",
      "28722134",
      "28774792",
      "28907593",
      "30582495",
      "30582501",
      "31390172"
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

namespace Authnetjson;

use Exception;
use Symfony\Component\Config\Definition\Builder\EnumNodeDefinition;

require '../../config.inc.php';

try {
    $request = AuthnetApiFactory::getJsonApiHandler(
        AUTHNET_LOGIN,
        AUTHNET_TRANSKEY,
        AuthnetApiFactory::USE_DEVELOPMENT_SERVER
    );
    $response = $request->getCustomerProfileIdsRequest();
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>CIM :: Get Customer Profile IDs</title>
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
        CIM :: Get Customer Profile IDs
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
            <th>Profile IDs</th>
            <td>
                <?= implode(', ', $response->ids) ?>
            </td>
        </tr>
    </table>
    <h2>
        Raw Input/Output
    </h2>
    <?= $request, $response ?>
</body>
</html>
