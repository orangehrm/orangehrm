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

namespace OrangeHRM\Tests\Slack\Service\Resolver;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Slack\Service\Resolver\LeaveTodayResolver;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * The leave-today resolver's contract:
 *
 *   - matches by exact date
 *   - includes status APPROVED (2) and TAKEN (3); excludes PENDING/CANCELLED/REJECTED
 *   - excludes terminated employees
 *   - deduplicates: an employee with two approved leaves on the same date appears
 *     in the recipient list only once
 *   - subunit filter excludes employees with NULL subDivision (same as birthday)
 *   - metadata on the recipient is the LEAVE TYPE name (not the leave id)
 *
 * Fixture layout (see LeaveTodayResolver.yaml):
 *   id 1: Alice  Engineering APPROVED Annual  on 2026-06-01  ← match
 *   id 2: Bob    People Ops  TAKEN    Casual  on 2026-06-01  ← match
 *   id 3: Carol  Sales       PENDING  Annual  on 2026-06-01  ← excluded
 *   id 4: Dave   terminated  APPROVED Annual  on 2026-06-01  ← excluded
 *   id 5: Eve    Engineering APPROVED Medical on 2026-06-01  ← match
 *   id 6: Frank  (no subunit) APPROVED Annual on 2026-06-01  ← match unless subunit filter
 *   id 7: Alice  Engineering APPROVED Annual  on 2026-06-01  ← dedup with id 1
 *   id 8: Alice  Engineering APPROVED Annual  on 2026-06-02  ← different date
 *
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
            . '/orangehrmSlackPlugin/test/fixtures/LeaveTodayResolver.yaml';
        TestDataService::populate($fixture);
    }

    /* ───────────────── No subunit filter (whole-org) ────────────────────────── */

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
        // 2026-05-31 → no rows in fixture
        $this->assertSame([], $this->resolver->resolve(new DateTime('2026-05-31'), []));

        // 2026-06-02 → only Alice's other leave
        $matches = $this->resolver->resolve(new DateTime('2026-06-02'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertSame(['Alice Avery'], $names);
    }

    /* ───────────────── Subunit filter ──────────────────────────────────────── */

    public function testResolveWithSingleSubunitFilter(): void
    {
        // subunit 2 = Engineering → Alice (dedup), Eve. NOT Bob (PO) or Frank (no sub).
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2]);

        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        sort($names);
        $this->assertSame(['Alice Avery', 'Eve Evans'], $names);
    }

    public function testResolveWithMultipleSubunitFilter(): void
    {
        // subunit 2 + 3 = Engineering + People Ops → Alice, Bob, Eve.
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2, 3]);

        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        sort($names);
        $this->assertSame(['Alice Avery', 'Bob Brown', 'Eve Evans'], $names);
    }

    public function testResolveWithSubunitFilterExcludesEmployeesWithoutSubunit(): void
    {
        // Frank has no subDivision → excluded by any non-empty filter.
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

    /* ───────────────── Recipient shape (metadata carries leave type) ───────── */

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
