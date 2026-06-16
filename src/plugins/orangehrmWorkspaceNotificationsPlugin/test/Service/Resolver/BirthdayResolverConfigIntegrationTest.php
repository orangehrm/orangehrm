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

namespace OrangeHRM\Tests\WorkspaceNotifications\Service\Resolver;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\WorkspaceNotifications\Service\Resolver\BirthdayResolver;
use OrangeHRM\WorkspaceNotifications\Service\WorkspaceNotificationSettingsService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * Integration tests that drive the leap-year birthday behaviour through the REAL
 * WorkspaceNotificationSettingsService -> ConfigDao -> hs_hr_config path, rather
 * than mocking the settings service. Each test writes the config value to the DB
 * and then runs a plain `new BirthdayResolver()` (which builds its own real
 * settings service) so the resolved mode is read straight from the database.
 *
 * @group Slack
 * @group Service
 */
class BirthdayResolverConfigIntegrationTest extends TestCase
{
    private WorkspaceNotificationSettingsService $settings;

    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmWorkspaceNotificationsPlugin/test/fixtures/BirthdayResolver.yaml';
        TestDataService::populate($fixture);

        $this->settings = new WorkspaceNotificationSettingsService();
    }

    /**
     * Persist the leap-year mode to hs_hr_config and resolve against a fresh,
     * real resolver so the value is read back from the DB.
     *
     * @return string[] resolved recipient full names
     */
    private function resolveNamesWithStoredMode(string $mode, string $date): array
    {
        $this->settings->setBirthdayLeapYearMode($mode);

        $matches = (new BirthdayResolver())->resolve(new DateTime($date), []);
        return array_map(fn ($r) => $r->getFullName(), $matches);
    }

    public function testStoredOnce4YearsModeDoesNotAliasFeb29OnFeb28(): void
    {
        // 2026 is a non-leap year.
        $names = $this->resolveNamesWithStoredMode(
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_ONCE_IN_4_YEARS,
            '2026-02-28'
        );
        $this->assertNotContains('Iris Ingram', $names);
    }

    public function testStoredFeb28ModeAliasesFeb29OnFeb28InNonLeapYear(): void
    {
        $names = $this->resolveNamesWithStoredMode(
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_FEB_28,
            '2026-02-28'
        );
        $this->assertContains('Iris Ingram', $names);
    }

    public function testStoredFeb28ModeDoesNotAliasOnMarch1(): void
    {
        $names = $this->resolveNamesWithStoredMode(
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_FEB_28,
            '2026-03-01'
        );
        $this->assertNotContains('Iris Ingram', $names);
    }

    public function testStoredMarch1ModeAliasesFeb29OnMarch1InNonLeapYear(): void
    {
        $names = $this->resolveNamesWithStoredMode(
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_MARCH_1,
            '2026-03-01'
        );
        $this->assertContains('Iris Ingram', $names);
    }

    public function testStoredMarch1ModeDoesNotAliasOnFeb28(): void
    {
        $names = $this->resolveNamesWithStoredMode(
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_MARCH_1,
            '2026-02-28'
        );
        $this->assertNotContains('Iris Ingram', $names);
    }

    public function testStoredFeb28ModeDoesNotAliasInLeapYear(): void
    {
        // 2028 is a leap year — the alias must not fire; Feb 29 employees get
        // their notification on the real Feb 29 instead.
        $names = $this->resolveNamesWithStoredMode(
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_FEB_28,
            '2028-02-28'
        );
        $this->assertNotContains('Iris Ingram', $names);
    }

    public function testFeb29MatchesOnRealFeb29RegardlessOfStoredMode(): void
    {
        $names = $this->resolveNamesWithStoredMode(
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_FEB_28,
            '2028-02-29'
        );
        $this->assertContains('Iris Ingram', $names);
    }

    public function testInvalidStoredModeFallsBackToOnce4YearsAndDoesNotAlias(): void
    {
        // A garbage value persisted in hs_hr_config must normalise to the safe
        // default (once_every_4_years), so no aliasing happens on Feb 28.
        $this->settings->getConfigDao()->setValue(
            WorkspaceNotificationSettingsService::KEY_BIRTHDAY_LEAP_YEAR_MODE,
            'totally_invalid_value'
        );
        $this->assertSame(
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_ONCE_IN_4_YEARS,
            $this->settings->getBirthdayLeapYearMode()
        );

        $names = array_map(
            fn ($r) => $r->getFullName(),
            (new BirthdayResolver())->resolve(new DateTime('2026-02-28'), [])
        );
        $this->assertNotContains('Iris Ingram', $names);
    }

    public function testStoredModeStillResolvesNormalSameDayBirthdays(): void
    {
        // Sanity: switching the leap-year mode does not disturb ordinary
        // (non-Feb-29) birthday resolution on a normal date.
        $names = $this->resolveNamesWithStoredMode(
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_MARCH_1,
            '2026-06-01'
        );
        sort($names);
        $this->assertSame(['Alice Avery', 'Bob Brown', 'Carol Clark', 'Henry Hill'], $names);
    }
}
