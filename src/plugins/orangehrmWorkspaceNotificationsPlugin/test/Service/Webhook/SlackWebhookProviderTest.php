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
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\SlackWebhookProvider;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Service
 */
class SlackWebhookProviderTest extends TestCase
{
    public function testSendReturnsSuccessOnTwoHundredOk(): void
    {
        $provider = $this->providerWithResponses([new Response(200, [], 'ok')]);

        $result = $provider->send('https://hooks.slack.com/services/T/B/secret', 'hello');

        $this->assertTrue($result->isOk(), 'HTTP 200 + body "ok" must be success');
        $this->assertNull($result->getErrorMessage());
    }

    public function testSendTreatsTrailingWhitespaceOkAsSuccess(): void
    {
        $provider = $this->providerWithResponses([new Response(200, [], "ok\n")]);

        $result = $provider->send('https://hooks.slack.com/services/T/B/secret', 'hello');

        $this->assertTrue($result->isOk());
    }

    public function testSendFailsOnTwoHundredNonOk(): void
    {
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
            'Connection errors must be tagged so they read clearly in workspace_notification_log.error_message'
        );
    }

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
                'https://hooks.slack.com/services/T/B',
                'https://hooks.slack.com/services/lowercase/B/secret',
                'https://hooks.slack.com/services/T/B/secret/extra',
                '',
            ] as $bad
        ) {
            $this->assertFalse($provider->validateUrl($bad));
        }
    }

    public function testFailureMessageScrubsWebhookUrlFromGuzzleException(): void
    {
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
        $secretUrl = 'https://hooks.slack.com/services/T01/B02/SECRETabc123';
        $provider = $this->providerWithResponses([
            new Response(500, [], "internal error sending to {$secretUrl}")
        ]);

        $result = $provider->send($secretUrl, 'hello');

        $this->assertStringNotContainsString('SECRETabc123', (string)$result->getErrorMessage());
    }

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
        $this->assertSame('…', SlackWebhookProvider::genericMask('weird'));
    }

    public function testProviderIdMatchesEntityConstant(): void
    {
        $this->assertSame(
            WorkspaceNotificationRegistration::PROVIDER_SLACK,
            (new SlackWebhookProvider())->getProviderId()
        );
    }

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
