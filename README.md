[![Latest Stable Version](https://poser.pugx.org/stymiee/authnetjson/v/stable.svg)](https://packagist.org/packages/stymiee/authnetjson)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/stymiee/authnetjson/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/stymiee/authnetjson/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/stymiee/authnetjson/badges/build.png?b=master)](https://scrutinizer-ci.com/g/stymiee/authnetjson/build-status/master)
[![Reference Status](https://www.versioneye.com/php/stymiee:authnetjson/reference_badge.svg?style=flat)](https://www.versioneye.com/php/stymiee:authnetjson/references)

# AuthnetJSON

Library that abstracts [Authorize.Net](http://www.authorize.net/)'s [JSON APIs](http://developer.authorize.net/api/reference/).

**The JSON API is currently in BETA and requires contacting Authorize.Net to enable it in their production environment.**

## Installation

Simply add a dependency on `stymiee/authnetjson` to your project's `composer.json` file if you use [Composer](http://getcomposer.org/)
to manage the dependencies of your project.

Here is a minimal example of a `composer.json` file that just defines a dependency on Authnet-Json:

    {
        "require": {
            "stymiee/authnetjson": "1.0.*"
        }
    }
    
## Basic Usage
Using this library usually consists of three steps:

1. Initiate the library with the login credentials for the Authorize.Net account
2. Make the API call passing any required parameters as an array
3. Check for the results and use them appropriately

*NOTE: If you are viewing any of the examples in a browser you will need to fill your Authorize.Net credentials in `config.inc.php` before usage*

Simple usage:

    $json = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
    $json->getTransactionDetailsRequest(array(
        'transId' => '2162566217'
    ));
    if ($json->isSuccessful()) {
        echo $json->transaction->transactionStatus;
    }
    
The format of the array to be passed during the API call follows the structure outlined in [Authorize.Net's Integration Guide](http://developer.authorize.net/api/reference/).

## Using the Authorize.Net Development Server

Authorize.Net provides a development environment for developers to test their integration against. To use this server
(as opposed to their production endpoint) set the optional third parameter of `AuthnetApiFactory::getJsonApiHandler()` to be `1` or use the built in class constant `AuthnetApiFactory::USE_DEVELOPMENT_SERVER`:

    $json = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    
## Usage Examples

To help make how this library is used easier to understand example API calls are provioded in the `example` directory.
Examples for all of the current APIs calls are represented. You *may* need to make adjustments to get some to work as
they may be dependant on valid values created from other API calls (i.e. a void will not work without a valid
transaction ID).

#### Authorize and Capture

    $json = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
    $json->createTransactionRequest(array(
        'refId' => rand(1000000, 100000000),
        'transactionRequest' => array(
            'transactionType' => 'authCaptureTransaction',
            'amount' => 5,
            'payment' => array(
                'creditCard' => array(
                    'cardNumber' => '4111111111111111',
                    'expirationDate' => '122016',
                    'cardCode' => '999',
                ),
            ),
            'order' => array(
                'invoiceNumber' => '1324567890',
                'description' => 'this is a test transaction',
            ),
            'lineItems' => array(
                'lineItem' => array(
                    0 => array(
                        'itemId' => '1',
                        'name' => 'vase',
                        'description' => 'Cannes logo',
                        'quantity' => '18',
                        'unitPrice' => '45.00'
                    ),
                    1 => array(
                        'itemId' => '2',
                        'name' => 'desk',
                        'description' => 'Big Desk',
                        'quantity' => '10',
                        'unitPrice' => '85.00'
                    )
                )
            ),
            'tax' => array(
               'amount' => '4.26',
               'name' => 'level2 tax name',
               'description' => 'level2 tax',
            ),
            'duty' => array(
               'amount' => '8.55',
               'name' => 'duty name',
               'description' => 'duty description',
            ),
            'shipping' => array(
               'amount' => '4.26',
               'name' => 'level2 tax name',
               'description' => 'level2 tax',
            ),
            'poNumber' => '456654',
            'customer' => array(
               'id' => '18',
               'email' => 'someone@blackhole.tv',
            ),
            'billTo' => array(
               'firstName' => 'Ellen',
               'lastName' => 'Johnson',
               'company' => 'Souveniropolis',
               'address' => '14 Main Street',
               'city' => 'Pecan Springs',
               'state' => 'TX',
               'zip' => '44628',
               'country' => 'USA',
            ),
            'shipTo' => array(
               'firstName' => 'China',
               'lastName' => 'Bayles',
               'company' => 'Thyme for Tea',
               'address' => '12 Main Street',
               'city' => 'Pecan Springs',
               'state' => 'TX',
               'zip' => '44628',
               'country' => 'USA',
            ),
            'customerIP' => '192.168.1.1',
            'transactionSettings' => array(
                'setting' => array(
                    0 => array(
                        'settingName' =>'allowPartialAuth',
                        'settingValue' => 'false'
                    ),
                    1 => array(
                        'settingName' => 'duplicateWindow',
                        'settingValue' => '0'
                    ),
                    2 => array(
                        'settingName' => 'emailCustomer',
                        'settingValue' => 'false'
                    ),
                    3 => array(
                        'settingName' => 'recurringBilling',
                        'settingValue' => 'false'
                    ),
                    4 => array(
                        'settingName' => 'testRequest',
                        'settingValue' => 'false'
                    )
                )
            ),
            'userFields' => array(
                'userField' => array(
                    'name' => 'MerchantDefinedFieldName1',
                    'value' => 'MerchantDefinedFieldValue1',
                ),
                'userField' => array(
                    'name' => 'favorite_color',
                    'value' => 'blue',
                ),
            ),
        ),
    ));
    
    if ($json->isSuccessful()) {
        echo $json->transactionResponse->authCode;
    }
    
#### Create a Customer Profile

    $json = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
    $json->createCustomerProfileRequest(array(
            'profile' => array(
			'merchantCustomerId' => '12345',
			'email' => 'user@example.com',
			'paymentProfiles' => array(
				'billTo' => array(
					'firstName' => 'John',
					'lastName' => 'Smith',
					'address' => '123 Main Street',
					'city' => 'Townsville',
					'state' => 'NJ',
					'zip' => '12345',
					'phoneNumber' => '800-555-1234'
				),
				'payment' => array(
					'creditCard' => array(
					'cardNumber' => '4111111111111111',
					'expirationDate' => '2016-08',
					),
				),
			),
    		'shipToList' => array(
    		    'firstName' => 'John',
				'lastName' => 'Smith',
				'address' => '123 Main Street',
				'city' => 'Townsville',
				'state' => 'NJ',
				'zip' => '12345',
				'phoneNumber' => '800-555-1234'
    		),
		),
		'validationMode' => 'liveMode'
	));

    if ($json->isSuccessful()) {
        echo $json->customerProfileId;
    }
    
#### Create a Recurring Subscription

    $json = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
    $json->ARBCreateSubscriptionRequest(array(
        'refId' => 'Sample',
        'subscription' => array(
            'name' => 'Sample subscription',
            'paymentSchedule' => array(
                'interval' => array(
                    'length' => '1',
                    'unit' => 'months'
                ),
                'startDate' => '2015-04-18',
                'totalOccurrences' => '12',
                'trialOccurrences' => '1'
            ),
            'amount' => '10.29',
            'trialAmount' => '0.00',
            'payment' => array(
                'creditCard' => array(
                    'cardNumber' => '4111111111111111',
                    'expirationDate' => '2016-08'
                )
            ),
            'billTo' => array(
                'firstName' => 'John',
                'lastName' => 'Smith'
            )
        )
    ));
    
    if ($json->isSuccessful()) {
        echo $json->subscriptionId;
    }

#### Get a List of Settled Batches

    $json = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
    $json->getSettledBatchListRequest(array(
        'includeStatistics'   => 'true',
        'firstSettlementDate' => '2015-01-01T08:15:30',
        'lastSettlementDate'  => '2015-01-30T08:15:30',
    ));
    
    if ($json->isSuccessful()) {
        foreach ($json->batchList as $batch) {
            echo $batch->batchId;
        }
    }
    
## Debugging

To assist with debugging the `__toString()` method has been overridden to output important elements pertaining to the
usage of this library. Simple `echo` your AuthnetJson object to see:

- The API Login ID used
- The API transaction Key used
- The API endpoint the request was sent to
- The request JSON
- The response JSON

Basic Usage:

    $json = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY);
    $json->getUnsettledTransactionListRequest();  
    echo $json;

## Support

If you require assistance using this library I can be found at Stack Overflow. Be sure when you
[ask a question](http://stackoverflow.com/questions/ask?tags=php,authorize.net) pertaining to the usage of
this class to tag your question with the **PHP** and **Authorize.Net** tags. Make sure you follow their
[guide for asking a good question](http://stackoverflow.com/help/how-to-ask) as poorly asked questions will be closed
and I will not be able to assist you.

**Do not use Stack Overflow to report bugs.** Bugs may be reported [here](https://github.com/stymiee/authnetjson/issues/new).