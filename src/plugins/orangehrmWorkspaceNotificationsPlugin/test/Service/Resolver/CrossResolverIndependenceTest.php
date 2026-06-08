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
use OrangeHRM\WorkspaceNotifications\Service\Resolver\LeaveTodayResolver;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
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
            . '/orangehrmWorkspaceNotificationsPlugin/test/fixtures/CrossResolverIndependence.yaml'
        );
    }

    public function testEmployeeWithBirthdayAndApprovedLeaveSurfacesInBothResolvers(): void
    {
        $birthdayNames = $this->names($this->birthdayResolver->resolve($this->today, []));
        $leaveNames = $this->names($this->leaveResolver->resolve($this->today, []));

        $this->assertContains('Alice Avery', $birthdayNames, 'Alice must appear in birthday list');
        $this->assertContains('Alice Avery', $leaveNames, 'Alice must appear in leave-today list');
    }

    public function testBirthdayOnlyEmployeeDoesNotSurfaceInLeaveResolver(): void
    {
        $birthdayNames = $this->names($this->birthdayResolver->resolve($this->today, []));
        $leaveNames = $this->names($this->leaveResolver->resolve($this->today, []));

        $this->assertContains('Bob Brown', $birthdayNames);
        $this->assertNotContains('Bob Brown', $leaveNames);
    }

    public function testLeaveOnlyEmployeeDoesNotSurfaceInBirthdayResolver(): void
    {
        $birthdayNames = $this->names($this->birthdayResolver->resolve($this->today, []));
        $leaveNames = $this->names($this->leaveResolver->resolve($this->today, []));

        $this->assertContains('Carol Clark', $leaveNames);
        $this->assertNotContains('Carol Clark', $birthdayNames);
    }

    public function testPendingLeaveDoesNotSurfaceInLeaveResolverEvenWhenEmployeeHasBirthday(): void
    {
        $birthdayNames = $this->names($this->birthdayResolver->resolve($this->today, []));
        $leaveNames = $this->names($this->leaveResolver->resolve($this->today, []));

        $this->assertContains('Dan Drake', $birthdayNames);
        $this->assertNotContains('Dan Drake', $leaveNames);
    }

    public function testResolversReturnIndependentRecipientShape(): void
    {
        $birthday = $this->find($this->birthdayResolver->resolve($this->today, []), 'Alice Avery');
        $leave = $this->find($this->leaveResolver->resolve($this->today, []), 'Alice Avery');

        $this->assertNotNull($birthday);
        $this->assertNotNull($leave);
        $this->assertNull($birthday->getMetadata(), 'Birthday DTO carries no leave-type metadata');
        $this->assertSame('Annual', $leave->getMetadata(), 'Leave DTO carries leave type as metadata');
    }

    public function testOverlapEmployeeIsCountedOnceInLeaveResolver(): void
    {
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
