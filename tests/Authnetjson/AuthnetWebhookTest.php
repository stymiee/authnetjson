<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Authnetjson;

use PHPUnit\Framework\TestCase;

class AuthnetWebhookTest extends TestCase
{
    /**
     * @covers            \Authnetjson\AuthnetWebhook::__construct()
     */
    public function testConstructor(): void
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

        self::assertEquals($signatureKey, $signature->getValue($webhook));
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhook::__construct()
     * @covers            \Authnetjson\AuthnetInvalidCredentialsException::__construct()
     */
    public function testExceptionIsRaisedForNoSignature(): void
    {
        $this->expectException(AuthnetInvalidCredentialsException::class);
        new AuthnetWebhook('', 'Not JSON', []);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhook::__construct()
     * @covers            \Authnetjson\AuthnetInvalidJsonException::__construct()
     */
    public function testExceptionIsRaisedForCannotSetParamsException(): void
    {
        $this->expectException(AuthnetInvalidJsonException::class);
        new AuthnetWebhook('a', 'Not JSON', []);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhook::__toString()
     */
    public function testToString(): void
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

        self::assertStringContainsString('{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a",', $string);
        self::assertStringContainsString('ae3b39b1-c58e-4a78-859b-1b4e6c62c5b7', $string);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhook::__get()
     */
    public function testGet(): void
    {
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $headers = [
            'X-Request-Id' => 'ae3b39b1-c58e-4a78-859b-1b4e6c62c5b7',
            'X-Anet-Signature' => 'sha512=A1E0CC0F8A7B6274E0951CAC5EB63146DCE02B98DEE3954579F70FC6B7B15384BA5A8E331661B51DE953E141E88994DFA7D267FFB8428BE0660511B6EFB94960'
        ];

        $webhook = new AuthnetWebhook($signatureKey, $webhookJson, $headers);

        self::assertEquals('182cbbff-cab2-4080-931d-80e5d818f23a', $webhook->notificationId);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhook::isValid()
     */
    public function testIsValidCamelCase(): void
    {
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $headers = ['X-Anet-Signature' => 'sha512=A1E0CC0F8A7B6274E0951CAC5EB63579F70FC6B7B15384B146DCE02B98DEE3954A5A8E331661B51DE953E141E88994DFA7D267FFB8428BE0660511B6EFB94960'];

        $webhook = new AuthnetWebhook($signatureKey, $webhookJson, $headers);

        $isValid = $webhook->isValid();

        self::assertTrue($isValid);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhook::isValid()
     */
    public function testIsValidUpperCase(): void
    {
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $headers = ['X-ANET-Signature' => 'sha512=A1E0CC0F8A7B6274E0951CAC5EB63579F70FC6B7B15384B146DCE02B98DEE3954A5A8E331661B51DE953E141E88994DFA7D267FFB8428BE0660511B6EFB94960'];

        $webhook = new AuthnetWebhook($signatureKey, $webhookJson, $headers);

        $isValid = $webhook->isValid();

        self::assertTrue($isValid);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhook::isValid()
     */
    public function testIsValidFailure(): void
    {
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $headers = ['X-Anet-Signature' => 'sha512=A1E0CC0F8A7B6274E0951CAC5EB63146DCE02B98DEE3954579F70FC6B7B15384BA5A8E331661B51DE953E141E88994DFA7D267FFB8428BE0660511B6EFB94960'];

        $webhook = new AuthnetWebhook($signatureKey, $webhookJson, $headers);

        $isValid = $webhook->isValid();

        self::assertFalse($isValid);
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhook::getRequestId()
     */
    public function testGetRequestId(): void
    {
        $signatureKey = '52CB4A002C634B84E397DC8A218E1A160BA7CAB7CBE4C05B35E9CBB05E14FE4A2385812E980CCF97D177F17863CE214D1BE6CE8E1E894487AACF3609C1A5FE17';
        $webhookJson = '{"notificationId":"182cbbff-cab2-4080-931d-80e5d818f23a","eventType":"net.authorize.payment.authcapture.created","eventDate":"2017-08-18T20:40:52.7722007Z","webhookId":"849eb87e-078a-4169-a34b-c0bded5019a8","payload":{"responseCode":0,"authCode":"Z3PV5H","avsResponse":"Y","authAmount":0.0,"entityName":"transaction","id":"40005915599"}}';
        $headers = [
            'X-Request-Id' => 'ae3b39b1-c58e-4a78-859b-1b4e6c62c5b7',
            'X-Anet-Signature' => 'sha512=A1E0CC0F8A7B6274E0951CAC5EB63146DCE02B98DEE3954579F70FC6B7B15384BA5A8E331661B51DE953E141E88994DFA7D267FFB8428BE0660511B6EFB94960'
        ];

        $webhook = new AuthnetWebhook($signatureKey, $webhookJson, $headers);

        self::assertEquals('ae3b39b1-c58e-4a78-859b-1b4e6c62c5b7', $webhook->getRequestId());
    }

    /**
     * @covers            \Authnetjson\AuthnetWebhook::getAllHeaders()
     */
    public function testGetAllHeaders(): void
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

        self::assertNotEmpty($headers->getValue($webhook));
    }
}
