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
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Slack\Service\Webhook\SlackWebhookProvider;
use PHPUnit\Framework\TestCase;

/**
 * Exercises the Slack-flavoured webhook provider against a Guzzle MockHandler so
 * we never touch the network. The orchestrator only ever sees the result of
 * `send()`, so the asserted contract here is:
 *
 *  - HTTP 200 + body "ok"        → success
 *  - HTTP 200 + body anything else → failure carrying the body
 *  - HTTP non-200                → failure carrying the body (or HTTP code if empty)
 *  - Connection error            → failure prefixed with "Failed to reach Slack"
 *
 * Plus the small URL-validation regex which the API layer also leans on.
 *
 * @group Slack
 * @group Service
 */
class SlackWebhookProviderTest extends TestCase
{
    /* ─────────────────────────── send() ──────────────────────────────────────── */

    public function testSendReturnsSuccessOnTwoHundredOk(): void
    {
        $provider = $this->providerWithResponses([new Response(200, [], 'ok')]);

        $result = $provider->send('https://hooks.slack.com/services/T/B/secret', 'hello');

        $this->assertTrue($result->isOk(), 'HTTP 200 + body "ok" must be success');
        $this->assertNull($result->getErrorMessage());
    }

    public function testSendTreatsTrailingWhitespaceOkAsSuccess(): void
    {
        // Slack pads with newline in some shapes; trim() handles it. Guard that
        // assumption from regressions.
        $provider = $this->providerWithResponses([new Response(200, [], "ok\n")]);

        $result = $provider->send('https://hooks.slack.com/services/T/B/secret', 'hello');

        $this->assertTrue($result->isOk());
    }

    public function testSendFailsOnTwoHundredNonOk(): void
    {
        // Slack returns 200 with a textual error code for some failures
        // (e.g. "invalid_payload", "channel_not_found"). Treat as failure.
        $provider = $this->providerWithResponses([new Response(200, [], 'invalid_payload')]);

        $result = $provider->send('https://hooks.slack.com/services/T/B/secret', 'hello');

        $this->assertFalse($result->isOk());
        $this->assertStringContainsString('Slack rejected the message', (string)$result->getErrorMessage());
        $this->assertStringContainsString('invalid_payload', (string)$result->getErrorMessage());
    }

    public function testSendFailsOnFourOhFour(): void
    {
        $provider = $this->providerWithResponses([new Response(404, [], 'no_service')]);

        $result = $provider->send('https://hooks.slack.com/services/T/B/secret', 'hello');

        $this->assertFalse($result->isOk());
        $this->assertStringContainsString('no_service', (string)$result->getErrorMessage());
    }

    public function testSendFailsOnFiveHundredEmptyBody(): void
    {
        // Empty body must still produce a usable error — fall back to HTTP code.
        $provider = $this->providerWithResponses([new Response(500, [], '')]);

        $result = $provider->send('https://hooks.slack.com/services/T/B/secret', 'hello');

        $this->assertFalse($result->isOk());
        $this->assertStringContainsString('HTTP 500', (string)$result->getErrorMessage());
    }

    public function testSendFailsGracefullyOnConnectException(): void
    {
        $exception = new ConnectException(
            'cURL error 28: timeout',
            new Request('POST', 'https://hooks.slack.com/services/T/B/secret')
        );
        $provider = $this->providerWithResponses([$exception]);

        $result = $provider->send('https://hooks.slack.com/services/T/B/secret', 'hello');

        $this->assertFalse($result->isOk());
        $this->assertStringStartsWith(
            'Failed to reach Slack',
            (string)$result->getErrorMessage(),
            'Connection errors must be tagged so they read clearly in slack_log.error_message'
        );
    }

    /* ─────────────────────────── validateUrl ────────────────────────────────── */

    public function testValidateUrlAcceptsCanonicalSlackHook(): void
    {
        $provider = new SlackWebhookProvider();
        $this->assertTrue(
            $provider->validateUrl('https://hooks.slack.com/services/T01ABCDEF/B02GHIJKL/abcSeCrEt123')
        );
    }

    public function testValidateUrlRejectsHttp(): void
    {
        $provider = new SlackWebhookProvider();
        $this->assertFalse(
            $provider->validateUrl('http://hooks.slack.com/services/T01ABCDEF/B02GHIJKL/abcSeCrEt123'),
            'Plain HTTP must be rejected — webhook secrets travel only over TLS'
        );
    }

    public function testValidateUrlRejectsLookalikeHosts(): void
    {
        $provider = new SlackWebhookProvider();
        foreach (
            [
                'https://evil.com/services/T/B/x',
                'https://hooks.slack.com.evil.com/services/T/B/x',
                'https://hooks.slack.io/services/T/B/x',
                'https://slack.com/services/T/B/x',
            ] as $bad
        ) {
            $this->assertFalse($provider->validateUrl($bad), "Lookalike host accepted: {$bad}");
        }
    }

    public function testValidateUrlRejectsMalformedPath(): void
    {
        $provider = new SlackWebhookProvider();
        foreach (
            [
                'https://hooks.slack.com/services/T/B',                 // missing secret
                'https://hooks.slack.com/services/lowercase/B/secret',  // workspace id must be A-Z0-9
                'https://hooks.slack.com/services/T/B/secret/extra',    // trailing segment
                '',
            ] as $bad
        ) {
            $this->assertFalse($provider->validateUrl($bad));
        }
    }

    /* ───────────── failure-message URL scrub (no leakage into slack_log) ──────── */

    public function testFailureMessageScrubsWebhookUrlFromGuzzleException(): void
    {
        // Some Guzzle handlers / curl verbose modes embed the request URL in
        // the exception message. Whatever the upstream does, our failure
        // message must never include the raw URL — which would land in
        // `slack_log.error_message` and defeat the whole encrypt-and-mask story.
        $secretUrl = 'https://hooks.slack.com/services/T01/B02/SECRETabc123';
        $exception = new ConnectException(
            "cURL error 28: Operation timed out for {$secretUrl}",
            new Request('POST', $secretUrl)
        );
        $provider = $this->providerWithResponses([$exception]);

        $result = $provider->send($secretUrl, 'hello');

        $this->assertFalse($result->isOk());
        $this->assertStringNotContainsString('SECRETabc123', (string)$result->getErrorMessage());
        $this->assertStringContainsString('/B02/…', (string)$result->getErrorMessage());
    }

    public function testFailureMessageScrubsWebhookUrlFromEchoedResponseBody(): void
    {
        // Defensive: if a server ever echoes the URL back in the error body,
        // the mask should fire there too.
        $secretUrl = 'https://hooks.slack.com/services/T01/B02/SECRETabc123';
        $provider = $this->providerWithResponses([
            new Response(500, [], "internal error sending to {$secretUrl}")
        ]);

        $result = $provider->send($secretUrl, 'hello');

        $this->assertStringNotContainsString('SECRETabc123', (string)$result->getErrorMessage());
    }

    /* ─────────────────────────── maskUrl ──────────────────────────────────────── */

    public function testMaskUrlKeepsWorkspaceAndChannelIdsAndDropsSecret(): void
    {
        $masked = (new SlackWebhookProvider())->maskUrl(
            'https://hooks.slack.com/services/T01ABC/B02DEF/abc123secretValue'
        );
        $this->assertSame('https://hooks.slack.com/services/T01ABC/B02DEF/…', $masked);
        $this->assertStringNotContainsString('abc123secretValue', $masked);
    }

    public function testMaskUrlFallsBackToGenericForOffShapeInput(): void
    {
        // Wrong host but otherwise looks Slack-ish — fall back, never echo raw.
        $masked = (new SlackWebhookProvider())->maskUrl('https://evil.com/foo/bar/secret');
        $this->assertStringEndsWith('/…', $masked);
        $this->assertStringNotContainsString('secret', $masked);
    }

    public function testGenericMaskDropsTheLastPathSegment(): void
    {
        $this->assertSame(
            'https://example.com/a/b/…',
            SlackWebhookProvider::genericMask('https://example.com/a/b/c')
        );
        // 1-segment fallback yields just the ellipsis — never the raw input.
        $this->assertSame('…', SlackWebhookProvider::genericMask('weird'));
    }

    /* ─────────────────────────── getProviderId ──────────────────────────────── */

    public function testProviderIdMatchesEntityConstant(): void
    {
        // The registry indexes providers by this id and resolves them against
        // SlackRegistration::getProvider() — drift would silently break dispatch.
        $this->assertSame(
            SlackRegistration::PROVIDER_SLACK,
            (new SlackWebhookProvider())->getProviderId()
        );
    }

    /* ─────────────────────────── Helpers ─────────────────────────────────────── */

    /**
     * @param array<int, Response|\Throwable> $responses
     */
    private function providerWithResponses(array $responses): SlackWebhookProvider
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $provider = new SlackWebhookProvider();
        $provider->setClient($client);
        return $provider;
    }
}
