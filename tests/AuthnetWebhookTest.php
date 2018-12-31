<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnConde\Authnet;

use PHPUnit\Framework\TestCase;

class AuthnetWebhookTest extends TestCase
{
    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhook::__construct()
     */
    public function testConstructor()
    {
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $headers = [
            'X-Request-Id' => 'ae3b39b1-c58e-4a78-859b-1b4e6c62c5b7',
            'X-Anet-Signature' => 'sha512=A1E0CC0F8A7B6274E0951CAC5EB63146DCE02B98DEE3954579F70FC6B7B15384BA5A8E331661B51DE953E141E88994DFA7D267FFB8428BE0660511B6EFB94960'
        ];

        $webhook = new AuthnetWebhook($signatureKey, $webhookJson, $headers);

        $reflectionOfWebhook = new \ReflectionObject($webhook);
        $signature = $reflectionOfWebhook->getProperty('signature');
        $signature->setAccessible(true);

        $this->assertEquals($signatureKey, $signature->getValue($webhook));
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhook::__construct()
     * @expectedException \JohnConde\Authnet\AuthnetInvalidCredentialsException
     */
    public function testExceptionIsRaisedForNoSignature()
    {
        new AuthnetWebhook('', 'Not JSON', []);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhook::__construct()
     * @expectedException \JohnConde\Authnet\AuthnetInvalidJsonException
     */
    public function testExceptionIsRaisedForCannotSetParamsException()
    {
        new AuthnetWebhook('a', 'Not JSON', []);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhook::__toString()
     */
    public function testToString()
    {
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $headers = [
            'X-Request-Id' => 'ae3b39b1-c58e-4a78-859b-1b4e6c62c5b7',
            'X-Anet-Signature' => 'sha512=A1E0CC0F8A7B6274E0951CAC5EB63146DCE02B98DEE3954579F70FC6B7B15384BA5A8E331661B51DE953E141E88994DFA7D267FFB8428BE0660511B6EFB94960'
        ];

        $webhook = new AuthnetWebhook($signatureKey, $webhookJson, $headers);

        ob_start();
        echo $webhook;
        $string = ob_get_clean();

        $this->assertContains('{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a",', $string);
        $this->assertContains('ae3b39b1-c58e-4a78-859b-1b4e6c62c5b7', $string);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhook::__get()
     */
    public function testGet()
    {
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $headers = [
            'X-Request-Id' => 'ae3b39b1-c58e-4a78-859b-1b4e6c62c5b7',
            'X-Anet-Signature' => 'sha512=A1E0CC0F8A7B6274E0951CAC5EB63146DCE02B98DEE3954579F70FC6B7B15384BA5A8E331661B51DE953E141E88994DFA7D267FFB8428BE0660511B6EFB94960'
        ];

        $webhook = new AuthnetWebhook($signatureKey, $webhookJson, $headers);

        $this->assertEquals('182cbbff-cab2-4080-931d-80e5d818f23a', $webhook->notificationId);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhook::isValid()
     */
    public function testIsValidCamelCase()
    {
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $headers = ['X-Anet-Signature' => 'sha512=A1E0CC0F8A7B6274E0951CAC5EB63579F70FC6B7B15384B146DCE02B98DEE3954A5A8E331661B51DE953E141E88994DFA7D267FFB8428BE0660511B6EFB94960'];

        $webhook = new AuthnetWebhook($signatureKey, $webhookJson, $headers);

        $isValid = $webhook->isValid();

        $this->assertTrue($isValid);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhook::isValid()
     */
    public function testIsValidUpperCase()
    {
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $headers = ['X-ANET-Signature' => 'sha512=A1E0CC0F8A7B6274E0951CAC5EB63579F70FC6B7B15384B146DCE02B98DEE3954A5A8E331661B51DE953E141E88994DFA7D267FFB8428BE0660511B6EFB94960'];

        $webhook = new AuthnetWebhook($signatureKey, $webhookJson, $headers);

        $isValid = $webhook->isValid();

        $this->assertTrue($isValid);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhook::isValid()
     */
    public function testIsValidFailure()
    {
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $headers = ['X-Anet-Signature' => 'sha512=A1E0CC0F8A7B6274E0951CAC5EB63146DCE02B98DEE3954579F70FC6B7B15384BA5A8E331661B51DE953E141E88994DFA7D267FFB8428BE0660511B6EFB94960'];

        $webhook = new AuthnetWebhook($signatureKey, $webhookJson, $headers);

        $isValid = $webhook->isValid();

        $this->assertFalse($isValid);
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhook::getRequestId()
     */
    public function testGetRequestId()
    {
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $headers = [
            'X-Request-Id' => 'ae3b39b1-c58e-4a78-859b-1b4e6c62c5b7',
            'X-Anet-Signature' => 'sha512=A1E0CC0F8A7B6274E0951CAC5EB63146DCE02B98DEE3954579F70FC6B7B15384BA5A8E331661B51DE953E141E88994DFA7D267FFB8428BE0660511B6EFB94960'
        ];

        $webhook = new AuthnetWebhook($signatureKey, $webhookJson, $headers);

        $this->assertEquals('ae3b39b1-c58e-4a78-859b-1b4e6c62c5b7', $webhook->getRequestId());
    }

    /**
     * @covers            \JohnConde\Authnet\AuthnetWebhook::getAllHeaders()
     */
    public function testGetAllHeaders()
    {
        $_SERVER += [
            'HTTP_TEST' => 'test'
        ];
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $webhook = new AuthnetWebhook($signatureKey, $webhookJson);

        $reflectionOfWebhook = new \ReflectionObject($webhook);
        $headers = $reflectionOfWebhook->getProperty('headers');
        $headers->setAccessible(true);

        $this->assertNotEmpty($headers->getValue($webhook));
    }
}