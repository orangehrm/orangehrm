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
use OrangeHRM\WorkspaceNotifications\Service\Formatter\TeamsMessageFormatter;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\TeamsWebhookProvider;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Service
 */
class TeamsWebhookProviderTest extends TestCase
{
    private const VALID_URL =
        'https://prod-12.westus.logic.azure.com:443/workflows/abc123def-456/triggers/manual/paths/invoke'
        . '?api-version=2016-06-01&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=HMACSIG123';

    public function testValidateUrlAcceptsCanonicalWorkflowUrl(): void
    {
        $this->assertTrue((new TeamsWebhookProvider())->validateUrl(self::VALID_URL));
    }

    public function testValidateUrlRejectsLegacyOfficeConnector(): void
    {
        $this->assertFalse((new TeamsWebhookProvider())->validateUrl(
            'https://acme.webhook.office.com/webhookb2/abc@xyz/IncomingWebhook/def/ghi'
        ));
    }

    public function testValidateUrlRejectsMissingSigQueryParam(): void
    {
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
                'https://logic.azure.com/workflows/abc/triggers/manual/paths/invoke?sig=X',
                'https://prod-12.westus.logic.azure.io/workflows/abc/triggers/manual/paths/invoke?sig=X',
                'http://prod-12.westus.logic.azure.com/workflows/abc/triggers/manual/paths/invoke?sig=X',
                'https://prod-12.westus.logic.azure.com.evil.com/workflows/abc/triggers/manual/paths/invoke?sig=X',
            ] as $bad
        ) {
            $this->assertFalse($provider->validateUrl($bad), "Should reject: {$bad}");
        }
    }

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

    public function testFailureMessageScrubsWebhookUrlFromGuzzleException(): void
    {
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

    public function testSendTreatsTwoOhTwoAsSuccess(): void
    {
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

    public function testProviderIdMatchesEntityConstant(): void
    {
        $this->assertSame(
            WorkspaceNotificationRegistration::PROVIDER_TEAMS,
            (new TeamsWebhookProvider())->getProviderId()
        );
    }

    public function testGetFormatterReturnsTeamsFormatter(): void
    {
        $this->assertInstanceOf(
            TeamsMessageFormatter::class,
            (new TeamsWebhookProvider())->getFormatter()
        );
    }

    public function testGetFormatterReturnsSameInstanceAcrossCalls(): void
    {
        $provider = new TeamsWebhookProvider();
        $this->assertSame($provider->getFormatter(), $provider->getFormatter());
    }

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
