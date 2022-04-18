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
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Entity\TerminationReason;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Dao\EmployeeTerminationDao;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeTerminationDaoTest extends KernelTestCase
{
    private EmployeeTerminationDao $employeeTerminationDao;

    protected function setUp(): void
    {
        $this->employeeTerminationDao = new EmployeeTerminationDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmPimPlugin/test/fixtures/EmployeeTerminationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveEmployeeTermination(): void
    {
        $employee = $this->getEntityReference(Employee::class, 1);
        $terminationReason = $this->getEntityReference(TerminationReason::class, 1);
        $employeeTerminationRecord = new EmployeeTerminationRecord();
        $employeeTerminationRecord->setEmployee($employee);
        $employeeTerminationRecord->setTerminationReason($terminationReason);
        $employeeTerminationRecord->setDate(new DateTime('2020-05-23'));
        $employeeTerminationRecord->setNote('Test Note');
        $this->employeeTerminationDao->saveEmployeeTermination($employeeTerminationRecord);

        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);

        /** @var EmployeeTerminationRecord $resultEmployeeTerminationRecord */
        $resultEmployeeTerminationRecord = TestDataService::fetchLastInsertedRecord(
            EmployeeTerminationRecord::class,
            'id'
        );
        $this->assertEquals('2020-05-23', $resultEmployeeTerminationRecord->getDecorator()->getDate());
        $this->assertEquals('Test Note', $resultEmployeeTerminationRecord->getNote());
        $this->assertEquals('Kayla', $resultEmployeeTerminationRecord->getEmployee()->getFirstName());
        $this->assertEquals('Resigned', $resultEmployeeTerminationRecord->getTerminationReason()->getName());
    }

    public function testGetEmployeeTermination(): void
    {
        $resultEmployeeTerminationRecord = $this->employeeTerminationDao->getEmployeeTermination(1);

        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
        $this->assertEquals('2011-12-09', $resultEmployeeTerminationRecord->getDecorator()->getDate());
        $this->assertNull($resultEmployeeTerminationRecord->getNote());
        $this->assertEquals('Ashley', $resultEmployeeTerminationRecord->getEmployee()->getFirstName());
        $this->assertEquals('Resigned', $resultEmployeeTerminationRecord->getTerminationReason()->getName());
    }

    public function testGetTerminationReasonList(): void
    {
        $terminationReasons = $this->employeeTerminationDao->getTerminationReasonList();
        $this->assertCount(3, $terminationReasons);
        $this->assertEquals('Dismissed', $terminationReasons[0]->getName());
        $this->assertEquals('Laid-off', $terminationReasons[1]->getName());
        $this->assertEquals('Resigned', $terminationReasons[2]->getName());
    }
}
