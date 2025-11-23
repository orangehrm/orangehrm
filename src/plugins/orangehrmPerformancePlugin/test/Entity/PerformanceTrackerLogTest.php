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

namespace OrangeHRM\Tests\Performance\Entity;

use DateTime;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerLog;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Entity
 */
class PerformanceTrackerLogTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([
            Employee::class,
            User::class,
            UserRole::class,
            PerformanceTracker::class,
            PerformanceTrackerLog::class
        ]);
    }

    public function testPerformanceTrackerLogEntity(): void
    {
        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName('Devi');
        $this->persist($employee);

        $userRole = new UserRole();
        $userRole->setId(1);
        $userRole->setName('Test Role');
        $userRole->setDisplayName('Test Display');
        $this->persist($userRole);

        $user = new User();
        $user->setId(1);
        $user->setEmployee($employee);
        $user->setUserRole($userRole);
        $this->persist($user);

        $performanceTracker = new PerformanceTracker();
        $performanceTracker->setTrackerName('Test Tracker');
        $performanceTracker->setEmployee($employee);
        $this->persist($performanceTracker);

        $date = new DateTime('06/27/2022');

        $performanceTrackerLog = new PerformanceTrackerLog();
        $performanceTrackerLog->setId(1);
        $performanceTrackerLog->setLog('Test log');
        $performanceTrackerLog->setComment('Test comment');
        $performanceTrackerLog->setStatus(PerformanceTrackerLog::STATUS_NOT_DELETED);
        $performanceTrackerLog->setAchievement(PerformanceTrackerLog::POSITIVE_ACHIEVEMENT);
        $performanceTrackerLog->setAddedDate($date);
        $performanceTrackerLog->setModifiedDate($date);
        $performanceTrackerLog->setPerformanceTracker($performanceTracker);
        $performanceTrackerLog->setEmployee($employee);
        $performanceTrackerLog->setUser($user);
        $this->persist($performanceTrackerLog);

        /** @var PerformanceTrackerLog $result */
        $result = $this->getRepository(PerformanceTrackerLog::class)->find(1);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('Test log', $result->getLog());
        $this->assertEquals('Test comment', $result->getComment());
        $this->assertEquals(PerformanceTrackerLog::STATUS_NOT_DELETED, $result->getStatus());
        $this->assertEquals(PerformanceTrackerLog::POSITIVE_ACHIEVEMENT, $result->getAchievement());
        $this->assertEquals($date, $result->getAddedDate());
        $this->assertEquals($date, $result->getModifiedDate());
        $this->assertEquals($performanceTracker, $result->getPerformanceTracker());
        $this->assertEquals($employee, $result->getEmployee());
        $this->assertEquals($user, $result->getUser());
    }
}
