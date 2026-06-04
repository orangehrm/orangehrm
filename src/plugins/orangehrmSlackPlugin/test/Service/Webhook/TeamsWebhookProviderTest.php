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
use OrangeHRM\Slack\Service\Formatter\TeamsMessageFormatter;
use OrangeHRM\Slack\Service\Webhook\TeamsWebhookProvider;
use PHPUnit\Framework\TestCase;

/**
 * Teams provider pins:
 *
 *  - validateUrl accepts canonical Power Automate workflow URLs and rejects
 *    everything else (incl. the deprecated webhook.office.com path)
 *  - maskUrl strips the entire query string (where `sig=` lives) and keeps
 *    the workflow path so the FE mirror matches byte-for-byte
 *  - send treats any HTTP 2xx as success — Power Automate workflows commonly
 *    return 202 Accepted with an empty body
 *  - send carries the `error.message` field from JSON error bodies, falls
 *    back to raw body, then to "HTTP {code}"
 *  - getFormatter returns a TeamsMessageFormatter (Teams-native markup)
 *
 * @group Slack
 * @group Service
 */
class TeamsWebhookProviderTest extends TestCase
{
    private const VALID_URL =
        'https://prod-12.westus.logic.azure.com:443/workflows/abc123def-456/triggers/manual/paths/invoke'
        . '?api-version=2016-06-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=HMACSIG123';

    /* ─────────────────────────── validateUrl ────────────────────────────────── */

    public function testValidateUrlAcceptsCanonicalWorkflowUrl(): void
    {
        $this->assertTrue((new TeamsWebhookProvider())->validateUrl(self::VALID_URL));
    }

    public function testValidateUrlRejectsLegacyOfficeConnector(): void
    {
        // The webhook.office.com path is the deprecated Office 365 Connector
        // shape that Microsoft is retiring. We refuse it so admins are nudged
        // onto the supported Power Automate workflow path.
        $this->assertFalse((new TeamsWebhookProvider())->validateUrl(
            'https://acme.webhook.office.com/webhookb2/abc@xyz/IncomingWebhook/def/ghi'
        ));
    }

    public function testValidateUrlRejectsMissingSigQueryParam(): void
    {
        // The workflow trigger HMAC lives in `sig=`. Without it the workflow
        // will 401 at the gateway, so we refuse the row at validation time.
        $this->assertFalse((new TeamsWebhookProvider())->validateUrl(
            'https://prod-12.westus.logic.azure.com:443/workflows/abc/triggers/manual/paths/invoke'
            . '?api-version=2016-06-01'
        ));
    }

    public function testValidateUrlRejectsLookalikeHosts(): void
    {
        $provider = new TeamsWebhookProvider();
        foreach (
            [
                // No subdomain
                'https://logic.azure.com/workflows/abc/triggers/manual/paths/invoke?sig=X',
                // Wrong TLD
                'https://prod-12.westus.logic.azure.io/workflows/abc/triggers/manual/paths/invoke?sig=X',
                // HTTP (not HTTPS)
                'http://prod-12.westus.logic.azure.com/workflows/abc/triggers/manual/paths/invoke?sig=X',
                // Host suffix attack
                'https://prod-12.westus.logic.azure.com.evil.com/workflows/abc/triggers/manual/paths/invoke?sig=X',
            ] as $bad
        ) {
            $this->assertFalse($provider->validateUrl($bad), "Should reject: {$bad}");
        }
    }

    /* ─────────────────────────── maskUrl ────────────────────────────────────── */

    public function testMaskDropsEntireQueryAndKeepsWorkflowPath(): void
    {
        $masked = (new TeamsWebhookProvider())->maskUrl(self::VALID_URL);
        $this->assertSame(
            'https://prod-12.westus.logic.azure.com:443/workflows/abc123def-456/triggers/manual/paths/invoke?…',
            $masked
        );
        $this->assertStringNotContainsString('HMACSIG123', $masked);
        $this->assertStringNotContainsString('sig=', $masked);
    }

    public function testMaskMatchesFEMirrorByteForByte(): void
    {
        // The FE mirror at SlackNotificationConfiguration.vue captures everything
        // up to `/paths/invoke` and appends `?…`. Drift here = duplicate-detection
        // false negatives on the FE for Teams rows.
        $url = self::VALID_URL;
        $beMask = (new TeamsWebhookProvider())->maskUrl($url);
        $expectedFEMask = 'https://prod-12.westus.logic.azure.com:443/workflows/abc123def-456/triggers/manual/paths/invoke?…';
        $this->assertSame($expectedFEMask, $beMask);
    }

    public function testMaskFallsBackForOffShapeInput(): void
    {
        $masked = (new TeamsWebhookProvider())->maskUrl('https://example.com/foo/bar');
        $this->assertStringEndsWith('/…', $masked);
    }

    /* ───────────── failure-message URL scrub ──────────────────────────────────── */

    public function testFailureMessageScrubsWebhookUrlFromGuzzleException(): void
    {
        // The `sig=` HMAC in the Teams workflow URL is as sensitive as a
        // password — must never appear in slack_log.error_message even when
        // Guzzle's exception message embeds the full URL.
        $exception = new ConnectException(
            'cURL error 28: timed out for ' . self::VALID_URL,
            new Request('POST', self::VALID_URL)
        );
        $provider = $this->providerWithResponses([$exception]);

        $result = $provider->send(self::VALID_URL, 'hello');

        $this->assertFalse($result->isOk());
        $this->assertStringNotContainsString('HMACSIG123', (string)$result->getErrorMessage());
        $this->assertStringNotContainsString('sig=', (string)$result->getErrorMessage());
        $this->assertStringContainsString('/paths/invoke?…', (string)$result->getErrorMessage());
    }

    /* ─────────────────────────── send ──────────────────────────────────────── */

    public function testSendTreatsTwoOhTwoAsSuccess(): void
    {
        // Power Automate "Accepted" — most common happy-path response code.
        $provider = $this->providerWithResponses([new Response(202, [], '')]);

        $result = $provider->send(self::VALID_URL, 'hello');
        $this->assertTrue($result->isOk());
        $this->assertNull($result->getErrorMessage());
    }

    public function testSendTreatsTwoHundredAsSuccess(): void
    {
        $provider = $this->providerWithResponses([new Response(200, [], '{"ok":true}')]);
        $result = $provider->send(self::VALID_URL, 'hello');
        $this->assertTrue($result->isOk());
    }

    public function testSendFailsOnFourOhOneWithJsonErrorMessage(): void
    {
        $provider = $this->providerWithResponses([
            new Response(401, [], '{"error":{"message":"Invalid signature"}}')
        ]);
        $result = $provider->send(self::VALID_URL, 'hello');
        $this->assertFalse($result->isOk());
        $this->assertStringContainsString('Invalid signature', (string)$result->getErrorMessage());
    }

    public function testSendFailsOnFourOhOneWithPlaintextBody(): void
    {
        // Some gateway-layer errors come back as plaintext, not JSON.
        $provider = $this->providerWithResponses([new Response(401, [], 'unauthorized')]);
        $result = $provider->send(self::VALID_URL, 'hello');
        $this->assertFalse($result->isOk());
        $this->assertStringContainsString('unauthorized', (string)$result->getErrorMessage());
    }

    public function testSendFailsOnFiveHundredEmptyBody(): void
    {
        $provider = $this->providerWithResponses([new Response(500, [], '')]);
        $result = $provider->send(self::VALID_URL, 'hello');
        $this->assertFalse($result->isOk());
        $this->assertStringContainsString('HTTP 500', (string)$result->getErrorMessage());
    }

    public function testSendFailsGracefullyOnConnectException(): void
    {
        $exception = new ConnectException(
            'cURL error 28: timeout',
            new Request('POST', self::VALID_URL)
        );
        $provider = $this->providerWithResponses([$exception]);

        $result = $provider->send(self::VALID_URL, 'hello');
        $this->assertFalse($result->isOk());
        $this->assertStringStartsWith(
            'Failed to reach Microsoft Teams',
            (string)$result->getErrorMessage()
        );
    }

    /* ─────────────────────────── getProviderId + getFormatter ─────────────────── */

    public function testProviderIdMatchesEntityConstant(): void
    {
        $this->assertSame(
            SlackRegistration::PROVIDER_TEAMS,
            (new TeamsWebhookProvider())->getProviderId()
        );
    }

    public function testGetFormatterReturnsTeamsFormatter(): void
    {
        // Teams must NOT reuse SlackMessageFormatter — sending Slack-mrkdwn to
        // Teams would surface raw `*asterisks*` and `:emoji:` shortcodes in
        // the channel. Pin the type so a refactor can't silently swap it.
        $this->assertInstanceOf(
            TeamsMessageFormatter::class,
            (new TeamsWebhookProvider())->getFormatter()
        );
    }

    public function testGetFormatterReturnsSameInstanceAcrossCalls(): void
    {
        // Cheap memoization — avoids re-instantiating the formatter on every
        // dispatch tick. Pinning this guards against an accidental `new` move
        // into getFormatter() that would silently break the optimisation.
        $provider = new TeamsWebhookProvider();
        $this->assertSame($provider->getFormatter(), $provider->getFormatter());
    }

    /* ─────────────────────────── Helpers ─────────────────────────────────────── */

    /**
     * @param array<int, Response|\Throwable> $responses
     */
    private function providerWithResponses(array $responses): TeamsWebhookProvider
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $provider = new TeamsWebhookProvider();
        $provider->setClient($client);
        return $provider;
    }
}
