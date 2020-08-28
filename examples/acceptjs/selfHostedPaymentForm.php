<?php
namespace Authnetjson;

use Exception;

require '../../config.inc.php';

try {
    $request = AuthnetApiFactory::getJsonApiHandler(
        AUTHNET_LOGIN,
        AUTHNET_TRANSKEY,
        AuthnetApiFactory::USE_DEVELOPMENT_SERVER
    );
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $response = $request->createTransactionRequest([
            'refId' => 123456,
            'transactionRequest' => [
                'transactionType' => 'authCaptureTransaction',
                'amount' => 5,
                'payment' => [
                    'opaqueData' => [
                        'dataDescriptor' => 'COMMON.ACCEPT.INAPP.PAYMENT',
                        'dataValue' => $_POST['dataValue'],
                    ]
                ]
            ]
        ]);
    } else {
        $response = $request->getMerchantDetailsRequest();
    }
} catch (Exception $e) {
    echo $e;
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Self Hosted Accept.js Payment Form</title>
    <style type="text/css">
        table { border: 1px solid #cccccc; margin: auto; border-collapse: collapse; max-width: 90%; }
        table td { padding: 3px 5px; vertical-align: top; border-top: 1px solid #cccccc; }
        pre { white-space: pre-wrap; }
        table th { background: #e5e5e5; color: #666666; }
        h1, h2 { text-align: center; }
    </style>
</head>
<body>
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
    <h2>
        Results
    </h2>
    <table>
        <tr>
            <th>Response</th>
            <td><?= $response->messages->resultCode ?></td>
        </tr>
        <tr>
            <th>Successful?</th>
            <td><?= $response->isSuccessful() ? 'yes' : 'no' ?></td>
        </tr>
        <tr>
            <th>Error?</th>
            <td><?= $response->isError() ? 'yes' : 'no' ?></td>
        </tr>
        <?php if ($response->isSuccessful()) : ?>
            <tr>
                <th>Description</th>
                <td><?= $response->transactionResponse->messages[0]->description ?></td>
            </tr>
            <tr>
                <th>authCode</th>
                <td><?= $response->transactionResponse->authCode ?></td>
            </tr>
            <tr>
                <th>transId</th>
                <td><?= $response->transactionResponse->transId ?></td>
            </tr>
        <?php elseif ($response->isError()) : ?>
            <tr>
                <th>Error Code</th>
                <td><?= $response->getErrorCode() ?></td>
            </tr>
            <tr>
                <th>Error Message</th>
                <td><?php echo  $response->getErrorText() ?></td>
            </tr>
        <?php endif; ?>
    </table>
<?php else : ?>
    <form id="paymentForm" method="POST" action="">
        <input type="text" name="cardNumber" id="cardNumber" placeholder="cardNumber"/> <br><br>
        <input type="text" name="expMonth" id="expMonth" placeholder="expMonth"/> <br><br>
        <input type="text" name="expYear" id="expYear" placeholder="expYear"/> <br><br>
        <input type="text" name="cardCode" id="cardCode" placeholder="cardCode"/> <br><br>
        <input type="hidden" name="dataValue" id="dataValue" />
        <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
        <button onclick="sendPaymentDataToAnet()">Pay</button>
    </form>
    <script type="text/javascript">
        function sendPaymentDataToAnet() {
            let authData = {};
            authData.clientKey = "<?= $response->publicClientKey ?>";
            authData.apiLoginID = "<?= AUTHNET_LOGIN ?>";

            let cardData = {};
            cardData.cardNumber = document.getElementById("cardNumber").value;
            cardData.month = document.getElementById("expMonth").value;
            cardData.year = document.getElementById("expYear").value;
            cardData.cardCode = document.getElementById("cardCode").value;

            let secureData = {};
            secureData.authData = authData;
            secureData.cardData = cardData;

            Accept.dispatchData(secureData, responseHandler);
        }

        function responseHandler(response) {
            if (response.messages.resultCode === "Error") {
                let i = 0;
                while (i < response.messages.message.length) {
                    console.log(
                        response.messages.message[i].code + ": " +
                        response.messages.message[i].text
                    );
                    i = i + 1;
                }
            } else {
                paymentFormUpdate(response.opaqueData);
            }
        }

        function paymentFormUpdate(opaqueData) {
            console.log('dataDescriptor: ' + opaqueData.dataDescriptor)
            console.log('dataValue: ' + opaqueData.dataValue)

            document.getElementById("dataDescriptor").value = opaqueData.dataDescriptor;
            document.getElementById("dataValue").value = opaqueData.dataValue;

            document.getElementById("cardNumber").value = "";
            document.getElementById("expMonth").value = "";
            document.getElementById("expYear").value = "";
            document.getElementById("cardCode").value = "";

            document.getElementById("paymentForm").submit();
        }
    </script>
    <script type="text/javascript" src="https://jstest.authorize.net/v1/Accept.js" charset="utf-8"></script>
<?php endif; ?>
</body>
</html>
