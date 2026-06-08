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
use OrangeHRM\WorkspaceNotifications\Service\Resolver\LeaveTodayResolver;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Slack
 * @group Service
 */
class LeaveTodayResolverTest extends TestCase
{
    private LeaveTodayResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new LeaveTodayResolver();
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmWorkspaceNotificationsPlugin/test/fixtures/LeaveTodayResolver.yaml';
        TestDataService::populate($fixture);
    }

    public function testResolveReturnsAllApprovedAndTakenLeavesForDate(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);

        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        sort($names);
        $this->assertSame(
            ['Alice Avery', 'Bob Brown', 'Eve Evans', 'Frank Foster'],
            $names,
            'APPROVED + TAKEN only; PENDING (Carol) and terminated (Dave) must be excluded'
        );
    }

    public function testResolveExcludesPendingLeave(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);
        $this->assertNotContains(
            'Carol Clark',
            array_map(fn ($r) => $r->getFullName(), $matches),
            'PENDING leaves must NOT be announced — only approved/taken'
        );
    }

    public function testResolveExcludesTerminatedEmployee(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);
        $this->assertNotContains(
            'Dave Dean',
            array_map(fn ($r) => $r->getFullName(), $matches),
            'Terminated employees never appear, even when leave row is approved'
        );
    }

    public function testResolveDeduplicatesEmployeeWithTwoApprovedLeavesOnSameDay(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);
        $alices = array_filter($matches, fn ($r) => $r->getFullName() === 'Alice Avery');
        $this->assertCount(
            1,
            $alices,
            'Half-day + half-day or split-shift produces 2 leave rows → recipient list still 1'
        );
    }

    public function testResolveOnlyMatchesExactDate(): void
    {
        $this->assertSame([], $this->resolver->resolve(new DateTime('2026-05-31'), []));

        $matches = $this->resolver->resolve(new DateTime('2026-06-02'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertSame(['Alice Avery'], $names);
    }

    public function testResolveWithSingleSubunitFilter(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2]);

        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        sort($names);
        $this->assertSame(['Alice Avery', 'Eve Evans'], $names);
    }

    public function testResolveWithMultipleSubunitFilter(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2, 3]);

        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        sort($names);
        $this->assertSame(['Alice Avery', 'Bob Brown', 'Eve Evans'], $names);
    }

    public function testResolveWithSubunitFilterExcludesEmployeesWithoutSubunit(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2]);
        $this->assertNotContains(
            'Frank Foster',
            array_map(fn ($r) => $r->getFullName(), $matches)
        );
    }

    public function testResolveWithSubunitFilterReturnsEmptyForUnknownSubunit(): void
    {
        $this->assertSame([], $this->resolver->resolve(new DateTime('2026-06-01'), [9999]));
    }

    public function testRecipientCarriesLeaveTypeNameAsMetadata(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);
        $byName = [];
        foreach ($matches as $r) {
            $byName[$r->getFullName()] = $r;
        }

        $this->assertSame('Annual', $byName['Alice Avery']->getMetadata());
        $this->assertSame('Casual', $byName['Bob Brown']->getMetadata());
        $this->assertSame('Medical', $byName['Eve Evans']->getMetadata());
    }

    public function testRecipientCarriesSubunitNameWhenPresent(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);
        $byName = [];
        foreach ($matches as $r) {
            $byName[$r->getFullName()] = $r;
        }

        $this->assertSame('Engineering', $byName['Alice Avery']->getSubunit());
        $this->assertSame('People Ops', $byName['Bob Brown']->getSubunit());
    }

    public function testRecipientSubunitIsNullForEmployeeWithoutSubDivision(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);
        $frank = array_values(array_filter($matches, fn ($r) => $r->getFullName() === 'Frank Foster'));
        $this->assertCount(1, $frank);
        $this->assertNull($frank[0]->getSubunit());
    }
}
