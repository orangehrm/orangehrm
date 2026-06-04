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
use OrangeHRM\Slack\Service\Resolver\BirthdayResolver;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * The birthday resolver's contract:
 *
 *   - matches by month + day, NOT by year (people don't share a birth year)
 *   - excludes terminated employees (employeeTerminationRecord IS NULL)
 *   - excludes employees with null birthdays
 *   - when subunitIds is empty → include everyone regardless of subDivision
 *     (including employees with NO subDivision)
 *   - when subunitIds is non-empty → restrict via IN-list; employees with NULL
 *     subDivision must be excluded (a subunit filter implies "these subunits")
 *
 * Fixture layout (see BirthdayResolver.yaml):
 *   1 Alice  Engineering 1990-06-01  ← match
 *   2 Bob    Engineering 1985-06-01  ← match (different year)
 *   3 Carol  Sales       1992-06-01  ← match (different subunit)
 *   4 Dave   Engineering 1990-12-25  ← off-date
 *   5 Eve    Engineering 1990-06-02  ← off-by-one day
 *   6 Frank  Engineering 1990-06-01 (terminated) ← excluded
 *   7 Gina   Engineering (null bday)             ← excluded
 *   8 Henry  no subunit  1988-06-01              ← match unless subunit filter
 *
 * @group Slack
 * @group Service
 */
class BirthdayResolverTest extends TestCase
{
    private BirthdayResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new BirthdayResolver();
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmSlackPlugin/test/fixtures/BirthdayResolver.yaml';
        TestDataService::populate($fixture);
    }

    /* ─────────────────── No subunit filter (whole-org) ──────────────────────── */

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
        // Frank shares Alice's birthday but is terminated. He must NOT appear
        // anywhere in the returned set — pinning the termination filter.
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertNotContains('Frank Foster', $names);
    }

    public function testResolveExcludesEmployeesWithNullBirthday(): void
    {
        // Gina has no birthday; even when the resolver scans her row she
        // must be filtered out. Defensive — protects against month()/day() on null.
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), []);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertNotContains('Gina Green', $names);
    }

    /* ─────────────────── Subunit filter ─────────────────────────────────────── */

    public function testResolveWithSingleSubunitFilter(): void
    {
        // subunit 2 = Engineering → Alice, Bob; NOT Carol (Sales) or Henry (null).
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2]);

        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        sort($names);
        $this->assertSame(['Alice Avery', 'Bob Brown'], $names);
    }

    public function testResolveWithMultipleSubunitFilter(): void
    {
        // subunit 2 + 4 = Engineering + Sales → Alice, Bob, Carol; NOT Henry.
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2, 4]);

        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        sort($names);
        $this->assertSame(['Alice Avery', 'Bob Brown', 'Carol Clark'], $names);
    }

    public function testResolveWithSubunitFilterExcludesEmployeesWithoutSubunit(): void
    {
        // Henry has no subDivision. A non-empty filter MUST exclude him —
        // otherwise the "filter to specific subunit" semantics is meaningless.
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2]);
        $names = array_map(fn ($r) => $r->getFullName(), $matches);
        $this->assertNotContains('Henry Hill', $names);
    }

    public function testResolveWithSubunitFilterReturnsEmptyForUnknownSubunit(): void
    {
        // No one is in subunit 9999.
        $this->assertSame([], $this->resolver->resolve(new DateTime('2026-06-01'), [9999]));
    }

    /* ─────────────────── Recipient shape ────────────────────────────────────── */

    public function testRecipientCarriesSubunitNameWhenPresent(): void
    {
        $matches = $this->resolver->resolve(new DateTime('2026-06-01'), [2]);
        // Returned in firstName asc order → Alice first.
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
        // emp_middle_name is empty in fixtures; the resolver does `first . ' ' . last`.
        // The trim() in the resolver must collapse the leading/trailing space when
        // middle is empty (we don't include middle, but be belt-and-braces).
        $matches = $this->resolver->resolve(new DateTime('2026-12-25'), []);
        $this->assertSame('Dave Dean', $matches[0]->getFullName());
        $this->assertStringStartsNotWith(' ', $matches[0]->getFullName());
        $this->assertStringEndsNotWith(' ', $matches[0]->getFullName());
    }
}
