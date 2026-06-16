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
 * @group Slack
 * @group Service
 */
class BirthdayResolverTest extends TestCase
{
    private BirthdayResolver $resolver;

    private function makeResolver(string $leapYearMode = WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_ONCE_IN_4_YEARS): BirthdayResolver
    {
        $settings = $this->getMockBuilder(WorkspaceNotificationSettingsService::class)
            ->onlyMethods(['getBirthdayLeapYearMode'])
            ->getMock();
        $settings->method('getBirthdayLeapYearMode')->willReturn($leapYearMode);
        return new BirthdayResolver($settings);
    }

    protected function setUp(): void
    {
        $this->resolver = $this->makeResolver();
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmWorkspaceNotificationsPlugin/test/fixtures/BirthdayResolver.yaml';
        TestDataService::populate($fixture);
    }

    public function testResolveReturnsAllBirthdayMatchesOnTargetDate(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);

        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        sort($names);
        $this->assertSame(
            ['Alice Avery', 'Bob Brown', 'Carol Clark', 'Henry Hill'],
            $names,
            'Match by month+day ignoring birth year; include employees regardless of subDivision'
        );
    }

    public function testResolveIgnoresOffByOneDay(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-02'), []);

        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertSame(['Eve Evans'], $names);
    }

    public function testResolveMatchesOnDifferentMonth(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-12-25'), []);

        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertSame(['Dave Dean'], $names);
    }

    public function testResolveReturnsEmptyWhenNoBirthdaysOnDate(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-03-15'), []);
        $this->assertSame([], $matches);
    }

    public function testResolveExcludesTerminatedEmployee(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertNotContains('Frank Foster', $names);
    }

    public function testResolveExcludesEmployeesWithNullBirthday(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertNotContains('Gina Green', $names);
    }

    public function testResolveWithSingleSubunitFilter(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2]);

        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        sort($names);
        $this->assertSame(['Alice Avery', 'Bob Brown'], $names);
    }

    public function testResolveWithMultipleSubunitFilter(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2, 4]);

        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        sort($names);
        $this->assertSame(['Alice Avery', 'Bob Brown', 'Carol Clark'], $names);
    }

    public function testResolveWithSubunitFilterExcludesEmployeesWithoutSubunit(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2]);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertNotContains('Henry Hill', $names);
    }

    public function testResolveWithSubunitFilterReturnsEmptyForUnknownSubunit(): void
    {
        $this->assertSame([], $this->resolver->resolve(new DateTime('2026-06-01'), [9999]));
    }

    public function testRecipientCarriesSubunitNameWhenPresent(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2]);
        $this->assertSame('Alice Avery', $matches[0]->getFullName());
        $this->assertSame('Engineering', $matches[0]->getSubunit());
        $this->assertNull($matches[0]->getMetadata(), 'Birthday resolver carries no leave metadata');
    }

    public function testRecipientSubunitIsNullForEmployeeWithoutSubDivision(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);
        $henry = array_values(array_filter($matches, fn ($r) => $r->getFullName() === 'Henry Hill'));
        $this->assertCount(1, $henry);
        $this->assertNull($henry[0]->getSubunit());
    }

    public function testRecipientFullNameTrimsWhitespace(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-12-25'), []);
        $this->assertSame('Dave Dean', $matches[0]->getFullName());
        $this->assertStringStartsNotWith(' ', $matches[0]->getFullName());
        $this->assertStringEndsNotWith(' ', $matches[0]->getFullName());
    }

    // ---- Leap year birthday (Feb 29) tests ----

    public function testFeb29MatchedOnLeapYearRegardlessOfMode(): void
    {
        // 2028 is a leap year; Feb 29 employees should match on that date in all modes.
        foreach ([
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_ONCE_IN_4_YEARS,
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_FEB_28,
            WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_MARCH_1,
        ] as $mode) {
            $resolver = $this->makeResolver($mode);
            $matches = $resolver->resolve(new DateTime('2028-02-29'), []);
            $names = array_map(fn ($r) => $r->getFullName(), $matches);
            $this->assertContains('Iris Ingram', $names, "Feb 29 employee should match on an actual Feb 29 (mode=$mode)");
        }
    }

    public function testFeb29NotMatchedOnFeb28InOnce4YearsMode(): void
    {
        // 2026 is not a leap year; once_every_4_years means no alias.
        $resolver = $this->makeResolver(WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_ONCE_IN_4_YEARS);
        $matches = $resolver->resolve(new DateTime('2026-02-28'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertNotContains('Iris Ingram', $names, 'Feb 29 employee must NOT appear on Feb 28 in once_every_4_years mode');
    }

    public function testFeb29NotMatchedOnMarch1InOnce4YearsMode(): void
    {
        $resolver = $this->makeResolver(WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_ONCE_IN_4_YEARS);
        $matches = $resolver->resolve(new DateTime('2026-03-01'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertNotContains('Iris Ingram', $names, 'Feb 29 employee must NOT appear on Mar 1 in once_every_4_years mode');
    }

    public function testFeb29MatchedOnFeb28InFeb28Mode(): void
    {
        // 2026 is not a leap year; feb_28 mode → Feb 29 employee gets notified on Feb 28.
        $resolver = $this->makeResolver(WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_FEB_28);
        $matches = $resolver->resolve(new DateTime('2026-02-28'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertContains('Iris Ingram', $names, 'Feb 29 employee should appear on Feb 28 in feb_28 mode (non-leap year)');
    }

    public function testFeb29NotMatchedOnMarch1InFeb28Mode(): void
    {
        $resolver = $this->makeResolver(WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_FEB_28);
        $matches = $resolver->resolve(new DateTime('2026-03-01'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertNotContains('Iris Ingram', $names, 'Feb 29 employee should NOT appear on Mar 1 in feb_28 mode');
    }

    public function testFeb29MatchedOnMarch1InMarch1Mode(): void
    {
        // 2026 is not a leap year; march_1 mode → Feb 29 employee gets notified on Mar 1.
        $resolver = $this->makeResolver(WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_MARCH_1);
        $matches = $resolver->resolve(new DateTime('2026-03-01'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertContains('Iris Ingram', $names, 'Feb 29 employee should appear on Mar 1 in march_1 mode (non-leap year)');
    }

    public function testFeb29NotMatchedOnFeb28InMarch1Mode(): void
    {
        $resolver = $this->makeResolver(WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_MARCH_1);
        $matches = $resolver->resolve(new DateTime('2026-02-28'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertNotContains('Iris Ingram', $names, 'Feb 29 employee should NOT appear on Feb 28 in march_1 mode');
    }

    public function testFeb29NotAliasedOnFeb28InLeapYearFeb28Mode(): void
    {
        // 2028 is a leap year; alias only fires in non-leap years — Feb 28 should not include Feb 29 employees.
        $resolver = $this->makeResolver(WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_FEB_28);
        $matches = $resolver->resolve(new DateTime('2028-02-28'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertNotContains('Iris Ingram', $names, 'Feb 29 employee should NOT be aliased to Feb 28 in a leap year');
    }

    public function testFeb29NotAliasedOnMarch1InLeapYearMarch1Mode(): void
    {
        // 2028 is a leap year; alias only fires in non-leap years.
        $resolver = $this->makeResolver(WorkspaceNotificationSettingsService::LEAP_YEAR_MODE_MARCH_1);
        $matches = $resolver->resolve(new DateTime('2028-03-01'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertNotContains('Iris Ingram', $names, 'Feb 29 employee should NOT be aliased to Mar 1 in a leap year');
    }
}
