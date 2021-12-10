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

namespace OrangeHRM\Tests\Time\Entity;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Time
 * @group Entity
 */
class TimesheetTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([Timesheet::class]);
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmTimePlugin/test/fixtures/TimesheetTest.yaml';
        TestDataService::populate($fixture);
        $this->getEntityManager()->clear();
    }

    public function testTimesheetEntity(): void
    {
        $this->assertTrue(true);
        $timesheet = new Timesheet();
        $timesheet->setState('INITIAL');
        $timesheet->setId(1);
        $timesheet->setStartDate(new DateTime('2021-12-06'));
        $timesheet->setEndDate(new DateTime('2021-12-12'));
        $timesheet->setEmployee($this->getEntityReference(Employee::class, 1));
        $this->persist($timesheet);

        $this->assertEquals(1, $timesheet->getId());
        $this->assertEquals('INITIAL', $timesheet->getState());
        $this->assertEquals('2021-12-06', $timesheet->getStartDate()->format('Y-m-d'));
        $this->assertEquals('2021-12-12', $timesheet->getEndDate()->format('Y-m-d'));
        $this->assertEquals(1, $timesheet->getEmployee()->getEmpNumber());
        $this->assertEquals('Kayla', $timesheet->getEmployee()->getFirstName());
        $this->assertEquals('Abbey', $timesheet->getEmployee()->getLastName());
    }
}
