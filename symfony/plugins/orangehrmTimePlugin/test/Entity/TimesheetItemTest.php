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
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\ProjectActivity;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Entity\TimesheetItem;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class TimesheetItemTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([TimesheetItem::class]);
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmTimePlugin/test/fixtures/TimesheetItemTest.yaml';
        TestDataService::populate($fixture);
        $this->getEntityManager()->clear();
    }

    public function testTimesheetItemEntity(): void
    {
        $timesheetItem = new TimesheetItem();
        $timesheetItem->setDuration(60 * 60);
        $timesheetItem->setId(1);
        $timesheetItem->setDate(new DateTime('2021-12-06'));
        $timesheetItem->setTimesheet($this->getEntityReference(Timesheet::class, 1));
        $timesheetItem->setEmployee($this->getEntityReference(Employee::class, 1));
        $timesheetItem->setProject($this->getEntityReference(Project::class, 1));
        $timesheetItem->setProjectActivity($this->getEntityReference(ProjectActivity::class, 1));
        $timesheetItem->setComment('Test comment');
        $this->persist($timesheetItem);

        $this->assertEquals(1, $timesheetItem->getId());
        $this->assertEquals('2021-12-06', $timesheetItem->getTimesheet()->getStartDate()->format('Y-m-d'));
        $this->assertEquals('2021-12-12', $timesheetItem->getTimesheet()->getEndDate()->format('Y-m-d'));
        $this->assertEquals(3600, $timesheetItem->getDuration());
        $this->assertEquals('2021-12-06', $timesheetItem->getDate()->format('Y-m-d'));
        $this->assertEquals(1, $timesheetItem->getEmployee()->getEmpNumber());
        $this->assertEquals('Kayla', $timesheetItem->getEmployee()->getFirstName());
        $this->assertEquals('Abbey', $timesheetItem->getEmployee()->getLastName());
        $this->assertEquals('Test comment', $timesheetItem->getComment());
        $this->assertTrue($timesheetItem->getProject() instanceof Project);
        $this->assertTrue($timesheetItem->getProjectActivity() instanceof ProjectActivity);
    }
}
