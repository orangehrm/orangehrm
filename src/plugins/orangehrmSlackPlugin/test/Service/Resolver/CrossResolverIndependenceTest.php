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
use OrangeHRM\Slack\Service\Resolver\LeaveTodayResolver;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * Pins row-22's acceptance criterion: "Both notifications arrive in Slack and
 * behave independently of each other" when the same employee has both a
 * birthday AND an approved leave on the same day.
 *
 * The orchestrator treats every registration as independent (separate log rows
 * keyed by `registration_id`), but the *resolvers* themselves are the load-
 * bearing seam — if either one accidentally suppressed an employee that the
 * other should surface, the user-visible bug would be "one of the two
 * notifications silently goes missing on overlap days."
 *
 * This test exercises the resolvers directly against a shared fixture where
 * Alice has both a birthday and an APPROVED leave on 2026-06-01:
 *
 *   - BirthdayResolver MUST include Alice (status of her leave is irrelevant
 *     to birthday matching).
 *   - LeaveTodayResolver MUST include Alice (her birthday is irrelevant to
 *     leave matching).
 *   - Neither resolver runs the other; their outputs are independent.
 *
 * @group Slack
 * @group Service
 */
class CrossResolverIndependenceTest extends TestCase
{
    private BirthdayResolver $birthdayResolver;
    private LeaveTodayResolver $leaveResolver;
    private DateTime $today;

    protected function setUp(): void
    {
        $this->birthdayResolver = new BirthdayResolver();
        $this->leaveResolver = new LeaveTodayResolver();
        $this->today = new DateTime('2026-06-01');

        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR)
            . '/orangehrmSlackPlugin/test/fixtures/CrossResolverIndependence.yaml'
        );
    }

    public function testEmployeeWithBirthdayAndApprovedLeaveSurfacesInBothResolvers(): void
    {
        // The headline assertion of row-22: Alice has both, both fire.
        $birthdayNames = $this->names($this->birthdayResolver->resolve($this->today, []));
        $leaveNames = $this->names($this->leaveResolver->resolve($this->today, []));

        $this->assertContains('Alice Avery', $birthdayNames, 'Alice must appear in birthday list');
        $this->assertContains('Alice Avery', $leaveNames, 'Alice must appear in leave-today list');
    }

    public function testBirthdayOnlyEmployeeDoesNotSurfaceInLeaveResolver(): void
    {
        // Bob has a birthday today but no leave row at all. Independence
        // means the birthday handler picks him up while the leave handler
        // does not — neither one leaks into the other's output.
        $birthdayNames = $this->names($this->birthdayResolver->resolve($this->today, []));
        $leaveNames = $this->names($this->leaveResolver->resolve($this->today, []));

        $this->assertContains('Bob Brown', $birthdayNames);
        $this->assertNotContains('Bob Brown', $leaveNames);
    }

    public function testLeaveOnlyEmployeeDoesNotSurfaceInBirthdayResolver(): void
    {
        // Carol has an approved leave today but no birthday. Mirror of the
        // above — the leave handler picks her up, the birthday handler
        // doesn't (and doesn't crash on a NULL emp_birthday either).
        $birthdayNames = $this->names($this->birthdayResolver->resolve($this->today, []));
        $leaveNames = $this->names($this->leaveResolver->resolve($this->today, []));

        $this->assertContains('Carol Clark', $leaveNames);
        $this->assertNotContains('Carol Clark', $birthdayNames);
    }

    public function testPendingLeaveDoesNotSurfaceInLeaveResolverEvenWhenEmployeeHasBirthday(): void
    {
        // Dan has both a birthday and a leave row today, but the leave is
        // status=PENDING. Birthday handler picks him up; leave handler must
        // NOT (its status gate is the second layer of "what really counts
        // as on leave today"). Catches a regression where a future change
        // to LeaveTodayResolver might widen the status filter.
        $birthdayNames = $this->names($this->birthdayResolver->resolve($this->today, []));
        $leaveNames = $this->names($this->leaveResolver->resolve($this->today, []));

        $this->assertContains('Dan Drake', $birthdayNames);
        $this->assertNotContains('Dan Drake', $leaveNames);
    }

    public function testResolversReturnIndependentRecipientShape(): void
    {
        // When Alice surfaces in both lists, the LEAVE recipient carries
        // leave-type metadata ("Annual") and the BIRTHDAY recipient does not
        // — same employee, different DTO shape per resolver. If the two
        // ever started sharing a cached DTO, this would break.
        $birthday = $this->find($this->birthdayResolver->resolve($this->today, []), 'Alice Avery');
        $leave = $this->find($this->leaveResolver->resolve($this->today, []), 'Alice Avery');

        $this->assertNotNull($birthday);
        $this->assertNotNull($leave);
        $this->assertNull($birthday->getMetadata(), 'Birthday DTO carries no leave-type metadata');
        $this->assertSame('Annual', $leave->getMetadata(), 'Leave DTO carries leave type as metadata');
    }

    public function testOverlapEmployeeIsCountedOnceInLeaveResolver(): void
    {
        // Alice has one APPROVED leave today (the fixture intentionally does
        // NOT seed a second one for her — the dedup case is already pinned
        // by LeaveTodayResolverTest::testResolveDeduplicatesEmployeeWithTwoApprovedLeavesOnSameDay).
        // What we assert here is the *combined* picture: she appears once in
        // each resolver, not twice in either.
        $birthdayHits = array_filter(
            $this->birthdayResolver->resolve($this->today, []),
            fn ($r) => $r->getFullName() === 'Alice Avery'
        );
        $leaveHits = array_filter(
            $this->leaveResolver->resolve($this->today, []),
            fn ($r) => $r->getFullName() === 'Alice Avery'
        );
        $this->assertCount(1, $birthdayHits);
        $this->assertCount(1, $leaveHits);
    }

    /* ─────────────────────────── Helpers ─────────────────────────────────────── */

    /**
     * @param object[] $recipients
     * @return string[]
     */
    private function names(array $recipients): array
    {
        return array_map(fn ($r) => $r->getFullName(), $recipients);
    }

    /**
     * @param object[] $recipients
     */
    private function find(array $recipients, string $fullName)
    {
        foreach ($recipients as $r) {
            if ($r->getFullName() === $fullName) {
                return $r;
            }
        }
        return null;
    }
}
