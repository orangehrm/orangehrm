<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\WorkspaceNotifications\Service\Webhook;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\GoogleChatWebhookProvider;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Service
 */
class GoogleChatWebhookProviderTest extends TestCase
{
    public function testMaskDropsKeyAndTokenButKeepsSpaceAndMessages(): void
    {
        $masked = (new GoogleChatWebhookProvider())->maskUrl(
            'https://chat.googleapis.com/v1/spaces/AAQA-abc/messages?key=SECRETKEY&token=SECRETTOKEN'
        );
        $this->assertSame(
            'https://chat.googleapis.com/v1/spaces/AAQA-abc/messages?…',
            $masked
        );
        $this->assertStringNotContainsString('SECRETKEY', $masked);
        $this->assertStringNotContainsString('SECRETTOKEN', $masked);
    }

    public function testMaskFallsBackForOffShapeInput(): void
    {
        $masked = (new GoogleChatWebhookProvider())->maskUrl(
            'https://chat.googleapis.com/v1/spaces/AAQA-abc/messages'
        );
        $this->assertStringEndsWith('/…', $masked);
    }

    public function testMaskOutputMatchesFEMirrorByteForByte(): void
    {
        $url = 'https://chat.googleapis.com/v1/spaces/X1Y2-z3/messages?key=abc&token=def';
        $beMask = (new GoogleChatWebhookProvider())->maskUrl($url);
        $expectedFEMask = 'https://chat.googleapis.com/v1/spaces/X1Y2-z3/messages?…';
        $this->assertSame($expectedFEMask, $beMask);
    }

    public function testValidateUrlAcceptsCanonicalGoogleChatHook(): void
    {
        $this->assertTrue(
            (new GoogleChatWebhookProvider())->validateUrl(
                'https://chat.googleapis.com/v1/spaces/AAQA-abc/messages?key=K&token=T'
            )
        );
    }

    public function testValidateUrlRejectsMissingKeyOrToken(): void
    {
        $provider = new GoogleChatWebhookProvider();
        foreach (
            [
                'https://chat.googleapis.com/v1/spaces/AAQA/messages?key=K',
                'https://chat.googleapis.com/v1/spaces/AAQA/messages?token=T',
                'https://chat.googleapis.com/v1/spaces/AAQA/messages?key=&token=T',
                'https://chat.googleapis.com/v1/spaces/AAQA/messages',
            ] as $bad
        ) {
            $this->assertFalse($provider->validateUrl($bad), "Should reject: {$bad}");
        }
    }

    public function testValidateUrlRejectsLookalikeHosts(): void
    {
        $provider = new GoogleChatWebhookProvider();
        foreach (
            [
                'https://chat.googleapis.com.evil.com/v1/spaces/A/messages?key=K&token=T',
                'http://chat.googleapis.com/v1/spaces/A/messages?key=K&token=T',
                'https://googleapis.com/v1/spaces/A/messages?key=K&token=T',
            ] as $bad
        ) {
            $this->assertFalse($provider->validateUrl($bad), "Should reject: {$bad}");
        }
    }

    public function testFailureMessageScrubsWebhookUrlFromGuzzleException(): void
    {
        $secretUrl = 'https://chat.googleapis.com/v1/spaces/AAA/messages?key=SECRETKEY&token=SECRETTOKEN';
        $exception = new ConnectException(
            "cURL error 28: timed out for {$secretUrl}",
            new Request('POST', $secretUrl)
        );
        $provider = $this->providerWithResponses([$exception]);

        $result = $provider->send($secretUrl, 'hello');

        $this->assertFalse($result->isOk());
        $this->assertStringNotContainsString('SECRETKEY', (string)$result->getErrorMessage());
        $this->assertStringNotContainsString('SECRETTOKEN', (string)$result->getErrorMessage());
        $this->assertStringContainsString('/messages?…', (string)$result->getErrorMessage());
    }

    /**
     * @param array<int, Response|\Throwable> $responses
     */
    private function providerWithResponses(array $responses): GoogleChatWebhookProvider
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $provider = new GoogleChatWebhookProvider();
        $provider->setClient($client);
        return $provider;
    }
}
