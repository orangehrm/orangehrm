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

namespace OrangeHRM\Tests\Pim\Dao;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dto\DateRange;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeEvent;
use OrangeHRM\Pim\Dao\EmployeeEventDao;
use OrangeHRM\Pim\Dto\EmployeeEventSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeEventDaoTest extends TestCase
{
    private EmployeeEventDao $employeeEventDao;

    protected function setUp(): void
    {
        $this->employeeEventDao = new EmployeeEventDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeEventDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveEmployeeEvent(): void
    {
        $employeeEvent = new EmployeeEvent();
        $employee = $this->getEntityReference(Employee::class, 1);

        $employeeEvent->setEmpNumber($employee->getEmpNumber());
        $employeeEvent->setType(EmployeeEvent::EVENT_TYPE_EMPLOYEE);
        $employeeEvent->setEvent(EmployeeEvent::EVENT_SAVE);
        $employeeEvent->setNote('Saving Employee');
        $employeeEvent->setCreatedBy('Admin');
        $employeeEvent->setCreatedDate(new DateTime());
        $employeeEvent = $this->employeeEventDao->saveEmployeeEvent($employeeEvent);
        $this->assertNotNull($employeeEvent->getEventId());

        $resultEmployeeEvent = TestDataService::fetchLastInsertedRecord(EmployeeEvent::class, 'eventId', false);
        $this->assertEquals($employeeEvent->getType(), $resultEmployeeEvent->getType());
        $this->assertEquals($employeeEvent->getEvent(), $resultEmployeeEvent->getEvent());
        $this->assertEquals($employeeEvent->getNote(), $resultEmployeeEvent->getNote());
        $this->assertEquals($employeeEvent->getCreatedBy(), $resultEmployeeEvent->getCreatedBy());
        $this->assertEquals($employeeEvent->getCreatedDate(), $resultEmployeeEvent->getCreatedDate());
    }

    public function testGetEmployeeEvents(): void
    {
        $employeeEventSearchFilterParams = new EmployeeEventSearchFilterParams();
        $employeeEventSearchFilterParams->setEmpNumber(2);
        $employeeEventSearchFilterParams->setType('subordinate');
        $employeeEventSearchFilterParams->setEvent('SAVE');
        $employeeEventSearchFilterParams->setDateRange(
            new DateRange(
                new DateTime('2020-08-18'),
                new DateTime('2020-08-20')
            )
        );
        $employeeEvents = $this->employeeEventDao->getEmployeeEvents($employeeEventSearchFilterParams);
        $this->assertCount(1, $employeeEvents);
    }
}
