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

use OrangeHRM\Core\Dao\ConfigDao;
use OrangeHRM\Slack\Service\SlackSettingsService;
use PHPUnit\Framework\TestCase;

/**
 * @group Slack
 * @group Service
 */
class SlackSettingsServiceTest extends TestCase
{
    public function testIsEnabledReturnsTrueForStringOne(): void
    {
        $service = $this->serviceWithStoredValue('1');
        $this->assertTrue($service->isEnabled());
    }

    public function testIsEnabledReturnsFalseForStringZero(): void
    {
        $service = $this->serviceWithStoredValue('0');
        $this->assertFalse($service->isEnabled());
    }

    public function testIsEnabledReturnsFalseWhenKeyMissing(): void
    {
        // ConfigDao::getValue returns null when no row exists. The service must NOT
        // throw or accidentally interpret null as truthy.
        $service = $this->serviceWithStoredValue(null);
        $this->assertFalse($service->isEnabled());
    }

    public function testIsEnabledIsStrictAboutTruthyValues(): void
    {
        // Common "almost-truthy" strings must NOT activate slack.
        foreach (['true', 'yes', 'on', 'enabled', ' 1 ', '01'] as $stored) {
            $service = $this->serviceWithStoredValue($stored);
            $this->assertFalse(
                $service->isEnabled(),
                "Stored value '{$stored}' must NOT activate slack notifications"
            );
        }
    }

    public function testSetEnabledTrueWritesStringOne(): void
    {
        $dao = $this->createMock(ConfigDao::class);
        $dao->expects($this->once())
            ->method('setValue')
            ->with(SlackSettingsService::KEY_SLACK_ENABLED, '1');

        $service = new class ($dao) extends SlackSettingsService {
            public function __construct(ConfigDao $injected)
            {
                $this->setMockedDao($injected);
            }

            public function setMockedDao(ConfigDao $dao): void
            {
                // Reflection-friendly setter for the protected/private dao slot.
                $ref = new \ReflectionClass(SlackSettingsService::class);
                $prop = $ref->getProperty('configDao');
                $prop->setAccessible(true);
                $prop->setValue($this, $dao);
            }
        };
        $service->setEnabled(true);
    }

    public function testSetEnabledFalseWritesStringZero(): void
    {
        $dao = $this->createMock(ConfigDao::class);
        $dao->expects($this->once())
            ->method('setValue')
            ->with(SlackSettingsService::KEY_SLACK_ENABLED, '0');

        $service = $this->injectDao(new SlackSettingsService(), $dao);
        $service->setEnabled(false);
    }

    public function testHs_hr_configKeyMatchesMigrationSeed(): void
    {
        // The migration seeds `slack.notifications.enabled` — the service must read/write
        // the exact same key, otherwise the toggle silently misses the seeded row.
        $this->assertSame('slack.notifications.enabled', SlackSettingsService::KEY_SLACK_ENABLED);
    }

    /* ──────────────── Helpers ──────────────── */

    private function serviceWithStoredValue(?string $stored): SlackSettingsService
    {
        $dao = $this->createMock(ConfigDao::class);
        $dao->method('getValue')
            ->with(SlackSettingsService::KEY_SLACK_ENABLED)
            ->willReturn($stored);

        return $this->injectDao(new SlackSettingsService(), $dao);
    }

    private function injectDao(SlackSettingsService $service, ConfigDao $dao): SlackSettingsService
    {
        $ref = new \ReflectionClass(SlackSettingsService::class);
        $prop = $ref->getProperty('configDao');
        $prop->setAccessible(true);
        $prop->setValue($service, $dao);
        return $service;
    }
}
