<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Tests\Performance\Entity;

use DateTime;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\PerformanceTracker;
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
        TestDataService::truncateSpecificTables([Employee::class]);
        TestDataService::truncateSpecificTables([PerformanceTracker::class]);
    }

    public function testerformanceTrackerEntity(): void
    {
        $employee = new Employee();
        $employee->setEmployeeId('E001');
        $employee->setFirstName('test1');
        $employee->setLastName('test2');
        $employee->setMiddleName('middle');
        $this->persist($employee);

        $performanceTracker = new PerformanceTracker();
        $performanceTracker->setId(1);
        $performanceTracker->setTrackerName('Devp vue apps');
        $performanceTracker->getDecorator()->setEmployeeByEmpNumber(1);
        $performanceTracker->setAddedDate(new DateTime('03/02/2022'));
        $this->persist($performanceTracker);

        $result = $this->getRepository(PerformanceTracker::class)->find(1);
        $this->assertEquals('Devp vue apps', $result->getTrackerName());
    }
}
