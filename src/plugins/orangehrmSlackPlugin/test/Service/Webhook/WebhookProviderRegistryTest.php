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

use InvalidArgumentException;
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Slack\Service\Webhook\GoogleChatWebhookProvider;
use OrangeHRM\Slack\Service\Webhook\SlackWebhookProvider;
use OrangeHRM\Slack\Service\Webhook\WebhookDeliveryResult;
use OrangeHRM\Slack\Service\Webhook\WebhookProviderInterface;
use OrangeHRM\Slack\Service\Webhook\WebhookProviderRegistry;
use PHPUnit\Framework\TestCase;

/**
 * The registry is the only seam between the orchestrator and the per-provider
 * implementations; if it misroutes, the orchestrator silently picks the wrong
 * one. These tests pin:
 *
 *  - the built-ins ship registered (slack + google_chat + teams)
 *  - get() throws for unknown ids (never returns a default)
 *  - getForRegistration() routes by entity.provider, not by event-type or id
 *  - register() lets tests swap in a fake (used by the orchestrator test below)
 *
 * @group Slack
 * @group Service
 */
class WebhookProviderRegistryTest extends TestCase
{
    public function testBuiltInProvidersAreRegistered(): void
    {
        $registry = new WebhookProviderRegistry();

        $this->assertTrue($registry->has(SlackRegistration::PROVIDER_SLACK));
        $this->assertTrue($registry->has('google_chat'));
        $this->assertTrue($registry->has(SlackRegistration::PROVIDER_TEAMS));
    }

    public function testGetReturnsRegisteredImplementation(): void
    {
        $registry = new WebhookProviderRegistry();

        $this->assertInstanceOf(SlackWebhookProvider::class, $registry->get('slack'));
        $this->assertInstanceOf(GoogleChatWebhookProvider::class, $registry->get('google_chat'));
        $this->assertInstanceOf(
            \OrangeHRM\Slack\Service\Webhook\TeamsWebhookProvider::class,
            $registry->get('teams')
        );
    }

    public function testGetThrowsForUnknownProvider(): void
    {
        $registry = new WebhookProviderRegistry();

        // `discord` is intentionally not (yet) registered — useful as a stable
        // "unknown" id even as new platforms get added to the built-in set.
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches("/discord/");
        $registry->get('discord');
    }

    public function testHasReturnsFalseForUnknown(): void
    {
        $registry = new WebhookProviderRegistry();
        $this->assertFalse($registry->has('discord'));
        $this->assertFalse($registry->has(''));
    }

    public function testRegisterReplacesExistingProvider(): void
    {
        // Tests use this to swap a real provider for a fake — must overwrite.
        $registry = new WebhookProviderRegistry();
        $fake = $this->makeFakeProvider('slack');
        $registry->register($fake);

        $this->assertSame($fake, $registry->get('slack'));
    }

    public function testGetForRegistrationRoutesByProviderColumn(): void
    {
        $registry = new WebhookProviderRegistry();

        $slackReg = new SlackRegistration();
        $slackReg->setProvider(SlackRegistration::PROVIDER_SLACK);
        $this->assertInstanceOf(SlackWebhookProvider::class, $registry->getForRegistration($slackReg));

        $gcReg = new SlackRegistration();
        $gcReg->setProvider('google_chat');
        $this->assertInstanceOf(GoogleChatWebhookProvider::class, $registry->getForRegistration($gcReg));

        $teamsReg = new SlackRegistration();
        $teamsReg->setProvider(SlackRegistration::PROVIDER_TEAMS);
        $this->assertInstanceOf(
            \OrangeHRM\Slack\Service\Webhook\TeamsWebhookProvider::class,
            $registry->getForRegistration($teamsReg)
        );
    }

    public function testGetForRegistrationThrowsForUnknownProviderColumn(): void
    {
        // Defensive: if the DB ever holds a provider we don't know (downgrade
        // scenario, hand-edited row) we want a loud failure at dispatch time.
        $registry = new WebhookProviderRegistry();
        $reg = new SlackRegistration();
        $reg->setProvider('discord');

        $this->expectException(InvalidArgumentException::class);
        $registry->getForRegistration($reg);
    }

    public function testAllReturnsRegistryKeyedByProviderId(): void
    {
        $registry = new WebhookProviderRegistry();
        $all = $registry->all();

        $this->assertArrayHasKey('slack', $all);
        $this->assertArrayHasKey('google_chat', $all);
        $this->assertArrayHasKey('teams', $all);
        foreach ($all as $key => $provider) {
            $this->assertSame($key, $provider->getProviderId(), 'Registry key must match provider id');
        }
    }

    /* ─────────────────────────── Helpers ─────────────────────────────────────── */

    private function makeFakeProvider(string $id): WebhookProviderInterface
    {
        return new class ($id) implements WebhookProviderInterface {
            private string $id;

            public function __construct(string $id)
            {
                $this->id = $id;
            }

            public function getProviderId(): string
            {
                return $this->id;
            }

            public function validateUrl(string $url): bool
            {
                return true;
            }

            public function send(string $webhookUrl, string $text): WebhookDeliveryResult
            {
                return WebhookDeliveryResult::success();
            }

            public function maskUrl(string $url): string
            {
                // Test fake — return a recognisable token so any caller that
                // accidentally leaks the raw URL is visible.
                return 'FAKE-MASKED';
            }

            public function getFormatter(): \OrangeHRM\Slack\Service\Formatter\MessageFormatterInterface
            {
                // Anonymous formatter — produces a recognisable string so
                // any orchestrator path that accidentally uses the fake
                // provider's formatter is immediately visible in assertions.
                return new class () implements \OrangeHRM\Slack\Service\Formatter\MessageFormatterInterface {
                    public function format(string $eventType, \DateTime $date, array $recipients, ?string $subunitLabel = null): string
                    {
                        return 'FAKE-FORMATTED';
                    }

                    public function formatTestMessage(string $eventType): string
                    {
                        return 'FAKE-TEST-FORMATTED';
                    }
                };
            }
        };
    }
}
