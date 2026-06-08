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

use InvalidArgumentException;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\GoogleChatWebhookProvider;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\SlackWebhookProvider;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\WebhookDeliveryResult;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\WebhookProviderInterface;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\WebhookProviderRegistry;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Service
 */
class WebhookProviderRegistryTest extends TestCase
{
    public function testBuiltInProvidersAreRegistered(): void
    {
        $registry = new WebhookProviderRegistry();

        $this->assertTrue($registry->has(WorkspaceNotificationRegistration::PROVIDER_SLACK));
        $this->assertTrue($registry->has('google_chat'));
        $this->assertTrue($registry->has(WorkspaceNotificationRegistration::PROVIDER_TEAMS));
    }

    public function testGetReturnsRegisteredImplementation(): void
    {
        $registry = new WebhookProviderRegistry();

        $this->assertInstanceOf(SlackWebhookProvider::class, $registry->get('slack'));
        $this->assertInstanceOf(GoogleChatWebhookProvider::class, $registry->get('google_chat'));
        $this->assertInstanceOf(
            \OrangeHRM\WorkspaceNotifications\Service\Webhook\TeamsWebhookProvider::class,
            $registry->get('teams')
        );
    }

    public function testGetThrowsForUnknownProvider(): void
    {
        $registry = new WebhookProviderRegistry();

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
        $registry = new WebhookProviderRegistry();
        $fake = $this->makeFakeProvider('slack');
        $registry->register($fake);

        $this->assertSame($fake, $registry->get('slack'));
    }

    public function testGetForRegistrationRoutesByProviderColumn(): void
    {
        $registry = new WebhookProviderRegistry();

        $slackReg = new WorkspaceNotificationRegistration();
        $slackReg->setProvider(WorkspaceNotificationRegistration::PROVIDER_SLACK);
        $this->assertInstanceOf(SlackWebhookProvider::class, $registry->getForRegistration($slackReg));

        $gcReg = new WorkspaceNotificationRegistration();
        $gcReg->setProvider('google_chat');
        $this->assertInstanceOf(GoogleChatWebhookProvider::class, $registry->getForRegistration($gcReg));

        $teamsReg = new WorkspaceNotificationRegistration();
        $teamsReg->setProvider(WorkspaceNotificationRegistration::PROVIDER_TEAMS);
        $this->assertInstanceOf(
            \OrangeHRM\WorkspaceNotifications\Service\Webhook\TeamsWebhookProvider::class,
            $registry->getForRegistration($teamsReg)
        );
    }

    public function testGetForRegistrationThrowsForUnknownProviderColumn(): void
    {
        $registry = new WebhookProviderRegistry();
        $reg = new WorkspaceNotificationRegistration();
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
                return 'FAKE-MASKED';
            }

            public function getFormatter(): \OrangeHRM\WorkspaceNotifications\Service\Formatter\MessageFormatterInterface
            {
                return new class () implements \OrangeHRM\WorkspaceNotifications\Service\Formatter\MessageFormatterInterface {
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
