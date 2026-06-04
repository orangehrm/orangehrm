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

namespace OrangeHRM\Tests\Slack\Service\Webhook;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use OrangeHRM\Slack\Service\Webhook\GoogleChatWebhookProvider;
use PHPUnit\Framework\TestCase;

/**
 * Focused coverage for the Google Chat provider's URL handling — the bits we
 * lean on for security:
 *
 *   - validateUrl rejects URLs missing the `key` or `token` query params
 *   - maskUrl drops the ENTIRE query string (where the secrets live)
 *   - maskUrl preserves `messages` so the FE mirror's output matches
 *     byte-for-byte (duplicate-detection depends on this)
 *
 * The `send()` happy/failure paths are exercised in the Slack provider's
 * test via the same MockHandler shape; the Google Chat send path is the
 * same plumbing with a different success-signal shape, so we don't repeat
 * the network coverage here.
 *
 * @group Slack
 * @group Service
 */
class GoogleChatWebhookProviderTest extends TestCase
{
    /* ─────────────────────────── maskUrl ──────────────────────────────────────── */

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
        // No `?` at all — masker must NOT echo the raw URL.
        $masked = (new GoogleChatWebhookProvider())->maskUrl(
            'https://chat.googleapis.com/v1/spaces/AAQA-abc/messages'
        );
        $this->assertStringEndsWith('/…', $masked);
    }

    public function testMaskOutputMatchesFEMirrorByteForByte(): void
    {
        // The FE mirror at SlackNotificationConfiguration.vue:290-293 captures
        // everything up to `/messages` and appends `?…`. The duplicate-detection
        // compares this byte-for-byte against the BE-masked stored URL, so
        // drift here = false negatives.
        $url = 'https://chat.googleapis.com/v1/spaces/X1Y2-z3/messages?key=abc&token=def';
        $beMask = (new GoogleChatWebhookProvider())->maskUrl($url);
        $expectedFEMask = 'https://chat.googleapis.com/v1/spaces/X1Y2-z3/messages?…';
        $this->assertSame($expectedFEMask, $beMask);
    }

    /* ─────────────────────────── validateUrl ──────────────────────────────────── */

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
                'https://chat.googleapis.com/v1/spaces/AAQA/messages?key=K',          // no token
                'https://chat.googleapis.com/v1/spaces/AAQA/messages?token=T',        // no key
                'https://chat.googleapis.com/v1/spaces/AAQA/messages?key=&token=T',   // empty key
                'https://chat.googleapis.com/v1/spaces/AAQA/messages',                // no query
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

    /* ───────────── failure-message URL scrub ──────────────────────────────────── */

    public function testFailureMessageScrubsWebhookUrlFromGuzzleException(): void
    {
        // `key=` and `token=` in the URL must NOT leak through the exception
        // message into slack_log.error_message.
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
