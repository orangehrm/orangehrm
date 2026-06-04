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

namespace OrangeHRM\Tests\Slack\Service;

use OrangeHRM\Slack\Service\SlackRegistrationService;
use PHPUnit\Framework\TestCase;

/**
 * Pure-unit coverage for the bits of SlackRegistrationService that don't need a DB —
 * the URL masker and the static helpers. DB-backed behaviour (createRegistration /
 * updateRegistration / applyPayload subunit-sync) is covered by SlackRegistrationDaoTest
 * + API integration tests.
 *
 * @group Slack
 * @group Service
 */
class SlackRegistrationServiceTest extends TestCase
{
    /* ─────────── maskWebhookUrl ─────────── */

    public function testMaskRealSlackUrl(): void
    {
        $masked = SlackRegistrationService::maskWebhookUrl(
            'https://hooks.slack.com/services/T01ABC/B02DEF/exampleSecret'
        );
        $this->assertSame(
            'https://hooks.slack.com/services/T01ABC/B02DEF/…',
            $masked,
            'Slack masker must keep workspace + channel ids but strip the secret'
        );
    }

    public function testMaskHidesSecretEvenForLongTokens(): void
    {
        $masked = SlackRegistrationService::maskWebhookUrl(
            'https://hooks.slack.com/services/T012X3Y/B98W2L1/' . str_repeat('a', 64)
        );
        $this->assertStringNotContainsString(str_repeat('a', 10), $masked);
        $this->assertStringEndsWith('/…', $masked);
    }

    public function testMaskNonSlackUrlUsesFallback(): void
    {
        $masked = SlackRegistrationService::maskWebhookUrl(
            'https://example.com/foo/bar/secret123'
        );
        $this->assertSame('https://example.com/foo/bar/…', $masked);
    }

    public function testMaskNullReturnsNull(): void
    {
        $this->assertNull(SlackRegistrationService::maskWebhookUrl(null));
    }

    public function testMaskEmptyStringReturnsNull(): void
    {
        $this->assertNull(SlackRegistrationService::maskWebhookUrl(''));
    }

    public function testMaskUrlWithoutPathReturnsEllipsis(): void
    {
        $masked = SlackRegistrationService::maskWebhookUrl('weird');
        $this->assertSame('…', $masked);
    }

    public function testMaskIsCaseSensitiveOnSlackPrefix(): void
    {
        $masked = SlackRegistrationService::maskWebhookUrl(
            'http://hooks.SLACK.com/services/TH6/B0A/secret'
        );
        $this->assertStringNotContainsString('hooks.slack.com', $masked);
    }

    /* ─────────── per-provider mask via providerId hint ─────────── */

    public function testMaskGoogleChatUrlWithProviderHint(): void
    {
        $masked = SlackRegistrationService::maskWebhookUrl(
            'https://chat.googleapis.com/v1/spaces/AAQA-abc/messages?key=SECRETKEY&token=SECRETTOKEN',
            'google_chat'
        );
        $this->assertSame(
            'https://chat.googleapis.com/v1/spaces/AAQA-abc/messages?…',
            $masked
        );
        $this->assertStringNotContainsString('SECRETKEY', $masked);
        $this->assertStringNotContainsString('SECRETTOKEN', $masked);
    }

    public function testMaskSlackUrlWithProviderHint(): void
    {
        $masked = SlackRegistrationService::maskWebhookUrl(
            'https://hooks.slack.com/services/T01/B02/superSecretAbc',
            'slack'
        );
        $this->assertSame(
            'https://hooks.slack.com/services/T01/B02/…',
            $masked
        );
    }

    public function testMaskInfersProviderFromUrlShapeWhenHintMissing(): void
    {
        $googleChat = SlackRegistrationService::maskWebhookUrl(
            'https://chat.googleapis.com/v1/spaces/AAQA-abc/messages?key=K&token=T'
        );
        $this->assertStringEndsWith('/messages?…', (string)$googleChat);

        $slack = SlackRegistrationService::maskWebhookUrl(
            'https://hooks.slack.com/services/T01/B02/superSecretAbc'
        );
        $this->assertStringEndsWith('/B02/…', (string)$slack);
    }

    public function testMaskFEAndBEAgreeForGoogleChat(): void
    {
        $url = 'https://chat.googleapis.com/v1/spaces/AAQA-abc/messages?key=K&token=T';
        $beMask = SlackRegistrationService::maskWebhookUrl($url, 'google_chat');
        $feMask = 'https://chat.googleapis.com/v1/spaces/AAQA-abc/messages?…';
        $this->assertSame($feMask, $beMask);
    }

    /* ─────────── encryptForStorage round-trip ─────────── */

    public function testEncryptIsReversibleViaDecrypt(): void
    {
        $service = new SlackRegistrationService();
        $plaintext = 'https://hooks.slack.com/services/T01ABCDEF/B02GHIJKL/abcSeCrEt123';
        $stored = $service->encryptForStorage($plaintext);

        $registration = new \OrangeHRM\Entity\SlackRegistration();
        $registration->setWebhookUrl($stored);

        $this->assertSame($plaintext, $service->decryptWebhookUrl($registration));
    }
}
