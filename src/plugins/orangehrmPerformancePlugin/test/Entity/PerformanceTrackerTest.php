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
use Doctrine\Common\Collections\ArrayCollection;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerReviewer;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Entity
 */
class PerformanceTrackerTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([
            Employee::class,
            PerformanceTracker::class,
            PerformanceTrackerReviewer::class
        ]);
    }

    public function testPerformanceTrackerEntity(): void
    {
        $employee = new Employee();
        $employee->setEmployeeId('E001');
        $employee->setFirstName('test1');
        $employee->setLastName('test2');
        $employee->setMiddleName('middle');
        $this->persist($employee);

        $performanceTracker = new PerformanceTracker();
        $performanceTracker->setId(1);
        $performanceTracker->setTrackerName('Develop vue apps');
        $performanceTracker->getDecorator()->setEmployeeByEmpNumber(1);
        $performanceTracker->setAddedDate(new DateTime('03/02/2022'));
        $performanceTracker->setModifiedDate(new DateTime('03/02/2022'));
        $performanceTracker->setStatus(1);
        $performanceTracker->setAddedBy($employee);
        $this->persist($performanceTracker);

        $performanceTrackerReviewer = new PerformanceTrackerReviewer();
        $performanceTrackerReviewer->getDecorator()->setReviewerByEmpNumber(1);
        $performanceTrackerReviewer->setPerformanceTracker($performanceTracker);
        $this->persist($performanceTrackerReviewer);

        $performanceTracker->setPerformanceTrackerReviewer(new ArrayCollection([$performanceTrackerReviewer]));
        $this->persist($performanceTracker);

        $result = $this->getRepository(PerformanceTracker::class)->find(1);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('Develop vue apps', $result->getTrackerName());
        $this->assertEquals($employee, $result->getEmployee());
        $this->assertEquals(new DateTime('03/02/2022'), $result->getAddedDate());
        $this->assertEquals(new DateTime('03/02/2022'), $result->getModifiedDate());
        $this->assertEquals(1, $result->getStatus());
        $this->assertEquals($performanceTrackerReviewer, $result->getPerformanceTrackerReviewer()[0]);
        $this->assertEquals($employee, $performanceTracker->getAddedBy());
    }
}
