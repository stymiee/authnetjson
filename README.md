[![Latest Stable Version](https://poser.pugx.org/stymiee/authnetjson/v/stable.svg)](https://packagist.org/packages/stymiee/authnetjson)
[![Total Downloads](https://poser.pugx.org/stymiee/authnetjson/downloads)](https://packagist.org/packages/stymiee/authnetjson)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/stymiee/authnetjson/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/stymiee/authnetjson/?branch=php72)
[![Build Status](https://scrutinizer-ci.com/g/stymiee/authnetjson/badges/build.png?b=master)](https://scrutinizer-ci.com/g/stymiee/authnetjson/build-status/php72)
[![Code Coverage](https://scrutinizer-ci.com/g/stymiee/authnetjson/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/stymiee/authnetjson/?branch=php72)
[![Maintainability](https://api.codeclimate.com/v1/badges/5847da924af47933e25f/maintainability)](https://codeclimate.com/github/stymiee/authnetjson/maintainability)
[![License](https://poser.pugx.org/stymiee/authnetjson/license)](https://packagist.org/packages/stymiee/authnetjson)
# AuthnetJSON

Library that abstracts [Authorize.Net](http://www.authorize.net/)'s [JSON APIs](http://developer.authorize.net/api/reference/).

## Requirements

- PHP 7.2+ (Support for PHP 7.2.0 - 8.*)
- cURL PHP Extension
- JSON PHP Extension
- An Authorize.Net account

Support for PHP versions less than 7.2 has been removed from the master branch. There is a [PHP 5.6 compatible branch](https://github.com/stymiee/authnetjson/tree/php56) 
available for development and releases for 5.6 may continue to be made as long as it is feasible to do so.

## Installation

Simply add a dependency on `stymiee/authnetjson` to your project's `composer.json` file if you use [Composer](http://getcomposer.org/)
to manage the dependencies of your project.

Here is a minimal example of a `composer.json` file that just defines a dependency on AuthnetJSON:

    {
        "require": {
            "stymiee/authnetjson": "~4.1"
        }
    }

## Basic Usage
Using this library usually consists of three steps:

1. Initiate the library with the login credentials for your Authorize.Net account
2. Make the API call passing any required parameters as an array
3. Check for the results and use them appropriately

*NOTE: If you are viewing any of the examples in a browser you will need to fill your Authorize.Net credentials in `config.inc.php` before usage*

Simple usage:
```php
$request = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
$response = $request->getTransactionDetailsRequest([
    'transId' => '2162566217'
]);
if ($response->isSuccessful()) {
    echo $response->transaction->transactionStatus;
}
```
The format of the array to be passed during the API call follows the structure outlined in [Authorize.Net's Integration Guide](http://developer.authorize.net/api/reference/).

## Using the Authorize.Net Development Server

Authorize.Net provides a development environment for developers to test their integration against. To use this endpoint
(as opposed to their production endpoint) set the optional third parameter of `AuthnetApiFactory::getJsonApiHandler()` to be `1` or use the built in class constant `AuthnetApiFactory::USE_DEVELOPMENT_SERVER`:

    $json = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, 
                                                    AuthnetApiFactory::USE_DEVELOPMENT_SERVER);

## Usage Examples

To help make how this library is used easier to understand example API calls are provided in the `example` directory.
Examples for all of the current APIs calls are represented. You *may* need to make adjustments to get some to work as
they may be dependent on valid values created from other API calls (i.e. a void will not work without a valid
transaction ID).

#### Authorize and Capture (Basic)
```php
$request = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
$response = $request->createTransactionRequest([
    'refId' => rand(1000000, 100000000),
    'transactionRequest' => [
        'transactionType' => 'authCaptureTransaction',
        'amount' => 5,
        'payment' => [
            'creditCard' => [
                'cardNumber' => '4111111111111111',
                'expirationDate' => '122026',
                'cardCode' => '999',
            ]
        ]
    ]
]);

if ($response->isSuccessful()) {
    echo $response->transactionResponse->authCode;
}
```
#### Authorize and Capture (Full)
```php
$request = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
$response = $request->createTransactionRequest([
    'refId' => rand(1000000, 100000000),
    'transactionRequest' => [
        'transactionType' => 'authCaptureTransaction',
        'amount' => 5,
        'payment' => [
            'creditCard' => [
                'cardNumber' => '4111111111111111',
                'expirationDate' => '122026',
                'cardCode' => '999',
            ],
        ],
        'order' => [
            'invoiceNumber' => '1324567890',
            'description' => 'this is a test transaction',
        ],
        'lineItems' => [
            'lineItem' => [
                0 => [
                    'itemId' => '1',
                    'name' => 'vase',
                    'description' => 'Cannes logo',
                    'quantity' => '18',
                    'unitPrice' => '45.00'
                ],
                1 => [
                    'itemId' => '2',
                    'name' => 'desk',
                    'description' => 'Big Desk',
                    'quantity' => '10',
                    'unitPrice' => '85.00'
                ]
            ]
        ],
        'tax' => [
           'amount' => '4.26',
           'name' => 'level2 tax name',
           'description' => 'level2 tax',
        ],
        'duty' => [
           'amount' => '8.55',
           'name' => 'duty name',
           'description' => 'duty description',
        ],
        'shipping' => [
           'amount' => '4.26',
           'name' => 'level2 tax name',
           'description' => 'level2 tax',
        ],
        'poNumber' => '456654',
        'customer' => [
           'id' => '18',
           'email' => 'someone@blackhole.tv',
        ],
        'billTo' => [
           'firstName' => 'Ellen',
           'lastName' => 'Johnson',
           'company' => 'Souveniropolis',
           'address' => '14 Main Street',
           'city' => 'Pecan Springs',
           'state' => 'TX',
           'zip' => '44628',
           'country' => 'USA',
        ],
        'shipTo' => [
           'firstName' => 'China',
           'lastName' => 'Bayles',
           'company' => 'Thyme for Tea',
           'address' => '12 Main Street',
           'city' => 'Pecan Springs',
           'state' => 'TX',
           'zip' => '44628',
           'country' => 'USA',
        ],
        'customerIP' => '192.168.1.1',
        'transactionSettings' => [
            'setting' => [
                0 => [
                    'settingName' =>'allowPartialAuth',
                    'settingValue' => 'false'
                ],
                1 => [
                    'settingName' => 'duplicateWindow',
                    'settingValue' => '0'
                ],
                2 => [
                    'settingName' => 'emailCustomer',
                    'settingValue' => 'false'
                ],
                3 => [
                    'settingName' => 'recurringBilling',
                    'settingValue' => 'false'
                ],
                4 => [
                    'settingName' => 'testRequest',
                    'settingValue' => 'false'
                ]
            ]
        ],
        'userFields' => [
            'userField' => [
                0 => [
                    'name' => 'MerchantDefinedFieldName1',
                    'value' => 'MerchantDefinedFieldValue1',
                ],
                1 => [
                    'name' => 'favorite_color',
                    'value' => 'blue',
                ],
            ],
        ],
    ],
]);

if ($response->isSuccessful()) {
    echo $response->transactionResponse->authCode;
}
```
#### Create a Customer Profile
```php
$request = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
$response = $request->createCustomerProfileRequest([
        'profile' => [
        'merchantCustomerId' => '12345',
        'email' => 'user@example.com',
        'paymentProfiles' => [
            'billTo' => [
                'firstName' => 'John',
                'lastName' => 'Smith',
                'address' => '123 Main Street',
                'city' => 'Townsville',
                'state' => 'NJ',
                'zip' => '12345',
                'phoneNumber' => '800-555-1234'
            ],
            'payment' => [
                'creditCard' => [
                'cardNumber' => '4111111111111111',
                'expirationDate' => '2026-08',
                ],
            ],
        ],
        'shipToList' => [
            'firstName' => 'John',
            'lastName' => 'Smith',
            'address' => '123 Main Street',
            'city' => 'Townsville',
            'state' => 'NJ',
            'zip' => '12345',
            'phoneNumber' => '800-555-1234'
        ],
    ],
    'validationMode' => 'liveMode'
]);

if ($response->isSuccessful()) {
    echo $response->customerProfileId;
}
```
#### Create a Recurring Subscription
```php
$request = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
$response = $request->ARBCreateSubscriptionRequest([
    'refId' => 'Sample',
    'subscription' => [
        'name' => 'Sample subscription',
        'paymentSchedule' => [
            'interval' => [
                'length' => '1',
                'unit' => 'months'
            ],
            'startDate' => '2020-04-18',
            'totalOccurrences' => '12',
            'trialOccurrences' => '1'
        ],
        'amount' => '10.29',
        'trialAmount' => '0.00',
        'payment' => [
            'creditCard' => [
                'cardNumber' => '4111111111111111',
                'expirationDate' => '2016-08'
            ]
        ],
        'billTo' => [
            'firstName' => 'John',
            'lastName' => 'Smith'
        ]
    ]
]);

if ($response->isSuccessful()) {
    echo $response->subscriptionId;
}
```
#### Get a List of Settled Batches
```php
$request = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
$response = $request->getSettledBatchListRequest([
    'includeStatistics'   => 'true',
    'firstSettlementDate' => '2020-01-01T08:15:30',
    'lastSettlementDate'  => '2020-01-30T08:15:30',
]);

if ($response->isSuccessful()) {
    foreach ($response->batchList as $batch) {
        echo $batch->batchId;
    }
}
```
#### Get Transaction Detail From CIM API Calls

Some CIM API calls process an AUTH_CAPTURE transaction and return data similar to AIM AUTH_CAPTURE transactions. To access
this information you can call `AuthnetJsonResponse::getTransactionResponseField()` using the field name or field number.
For example, if you are looking for the transaction ID you can use:
```php
$response->getTransactionResponseField('TransactionID');
```
or
```php
$response->getTransactionResponseField(7);
```
Field name and number can be found in the [Authorize.Net AIM Guide](http://www.authorize.net/support/AIM_guide.pdf). Note
that the field name has all spaces removed so `TransactionID` becomes `TransactionID`.

#### Create a Webhook
```php
$response = $request->createWebhooks([
    "net.authorize.customer.subscription.expiring",
    "net.authorize.customer.subscription.suspended",
    "net.authorize.payment.authcapture.created",
    "net.authorize.payment.authorization.created",
    "net.authorize.payment.capture.created",
    "net.authorize.payment.fraud.approved",
    "net.authorize.payment.fraud.declined",
    "net.authorize.payment.fraud.held",
    "net.authorize.payment.priorAuthCapture.created",
    "net.authorize.payment.refund.created",
    "net.authorize.payment.void.created"
], 'http://www.example.com:55950/api/webhooks', 'active');
```
#### Validate and access a Webhook
```php
$payload = file_get_contents("php://input");
$webhook = new AuthnetWebhook(AUTHNET_SIGNATURE, $payload);
if ($webhook->isValid()) {
    // Access notifcation values
    // echo $webhook->eventType;
}
```
If `apache_request_headers()`/`getallheaders()` are not available to you, you can will need to get the HTTP request
    headers and pass them as the third parameter to `AuthnetWebhook()`.
```php
$headers = yourGetHeadersFunction();
$payload = file_get_contents("php://input");
$webhook = new AuthnetWebhook(AUTHNET_SIGNATURE, $payload, $headers);
if ($webhook->isValid()) {
    // Access notifcation values
    // echo $webhook->eventType;
}
```
#### Accept.js

To see examples of an Accept.js powered 
[self hosted payment form](https://github.com/stymiee/authnetjson/tree/master/examples/acceptjs/selfHostedPaymentForm.php),
an [Authorize.Net hosted payment form](https://github.com/stymiee/authnetjson/tree/master/examples/acceptjs/getHostedPaymentPageRequest.php),
and a [hosted customer profile page](https://github.com/stymiee/authnetjson/tree/master/examples/acceptjs/getHostedProfilePageRequest.php),
visit the [Accept.js examples directory](https://github.com/stymiee/authnetjson/tree/master/examples/acceptjs).

## Debugging

To assist with debugging the `__toString()` method has been overridden to output important elements pertaining to the
usage of this library. Simple `echo` your AuthnetJSON object to see:

- The API Login ID used
- The API transaction Key used
- The API endpoint the request was sent to
- The request JSON
- The response JSON

### Basic Usage:
```php
$request = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
$response = $request->getUnsettledTransactionListRequest();
echo $request, $response;
```
## Support

If you require assistance using this library start by viewing the [HELP.md](HELP.md) file included in this package. It 
includes common problems and their solutions.

If you need additional assistance, I can be found at Stack Overflow. Be sure when you
[ask a question](http://stackoverflow.com/questions/ask?tags=php,authorize.net) pertaining to the usage of
this class be sure to tag your question with the **PHP** and **Authorize.Net** tags. Make sure you follow their
[guide for asking a good question](http://stackoverflow.com/help/how-to-ask) as poorly asked questions will be closed
and I will not be able to assist you.

**Do not use Stack Overflow to report bugs.** Bugs may be reported [here](https://github.com/stymiee/authnetjson/issues/new).
