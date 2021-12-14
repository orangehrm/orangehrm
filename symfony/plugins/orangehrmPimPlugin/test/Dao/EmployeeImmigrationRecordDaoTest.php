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
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeImmigrationRecord;
use OrangeHRM\Pim\Dao\EmployeeImmigrationRecordDao;
use OrangeHRM\Pim\Dto\EmployeeImmigrationRecordSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeImmigrationRecordDaoTest extends TestCase
{
    private EmployeeImmigrationRecordDao $employeeImmigrationRecordDao;

    protected function setUp(): void
    {
        $this->employeeImmigrationRecordDao = new EmployeeImmigrationRecordDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmPimPlugin/test/fixtures/EmployeeImmigrationRecordDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveEmployeeImmigrationRecord(): void
    {
        $employee = $this->getEntityReference(Employee::class, 1);
        $employeeImmigrationRecord = new EmployeeImmigrationRecord();
        $employeeImmigrationRecord->setEmployee($employee);
        $employeeImmigrationRecord->setRecordId('1');
        $employeeImmigrationRecord->setNumber('HVN0003472');
        $employeeImmigrationRecord->setType(1);
        $employeeImmigrationRecord->setIssuedDate(new DateTime('2010-12-12'));
        $employeeImmigrationRecord->setExpiryDate(new DateTime('2011-12-12'));
        $employeeImmigrationRecord->setStatus('some status');
        $employeeImmigrationRecord->setComment('test Comment');
        $employeeImmigrationRecord->setReviewDate(new DateTime('2011-12-30'));
        $employeeImmigrationRecord->setCountryCode('UK');

        $result = $this->employeeImmigrationRecordDao->saveEmployeeImmigrationRecord($employeeImmigrationRecord);
        $this->assertTrue($result instanceof EmployeeImmigrationRecord);
        $this->assertEquals('E001', $result->getEmployee()->getEmployeeId());
        $this->assertEquals('1', $result->getRecordId());
        $this->assertEquals('HVN0003472', $result->getNumber());
        $this->assertEquals(1, $result->getType());
        $this->assertEquals(new DateTime("2010-12-12"), $result->getIssuedDate());
        $this->assertEquals(new DateTime("2011-12-12"), $result->getExpiryDate());
        $this->assertEquals(new DateTime("2011-12-30"), $result->getReviewDate());
        $this->assertEquals("some status", $result->getStatus());
        $this->assertEquals("test Comment", $result->getComment());
        $this->assertEquals("UK", $result->getCountryCode());
    }

    public function testEditEmployeeImmigrationRecord(): void
    {
        $employeeImmigrationRecord = $this->employeeImmigrationRecordDao->getEmployeeImmigrationRecord(2, 1);
        $employeeImmigrationRecord->setRecordId('1');
        $employeeImmigrationRecord->setNumber('RTF33323411');
        $employeeImmigrationRecord->setType(2);
        $employeeImmigrationRecord->setIssuedDate(new DateTime('2020-12-12'));
        $employeeImmigrationRecord->setExpiryDate(new DateTime('2021-12-12'));
        $employeeImmigrationRecord->setStatus('some status1');
        $employeeImmigrationRecord->setComment('test Comment1');
        $employeeImmigrationRecord->setReviewDate(new DateTime('2021-12-30'));
        $employeeImmigrationRecord->setCountryCode('LK');

        $result = $this->employeeImmigrationRecordDao->saveEmployeeImmigrationRecord($employeeImmigrationRecord);
        $this->assertTrue($result instanceof EmployeeImmigrationRecord);
        $this->assertEquals('RTF33323411', $result->getNumber());
        $this->assertEquals(2, $result->getType());
        $this->assertEquals(new DateTime("2020-12-12"), $result->getIssuedDate());
        $this->assertEquals(new DateTime("2021-12-12"), $result->getExpiryDate());
        $this->assertEquals(new DateTime("2021-12-30"), $result->getReviewDate());
        $this->assertEquals("some status1", $result->getStatus());
        $this->assertEquals("test Comment1", $result->getComment());
        $this->assertEquals("LK", $result->getCountryCode());
    }

    public function testGetEmployeeImmigrationRecord(): void
    {
        $result = $this->employeeImmigrationRecordDao->getEmployeeImmigrationRecord(2, 1);
        $this->assertEquals('E002', $result->getEmployee()->getEmployeeId());
        $this->assertEquals('1', $result->getRecordId());
        $this->assertEquals('RTF33323411', $result->getNumber());
        $this->assertEquals(2, $result->getType());
        $this->assertEquals(new DateTime("2010-12-12"), $result->getIssuedDate());
        $this->assertEquals(new DateTime("2011-12-12"), $result->getExpiryDate());
        $this->assertEquals(new DateTime("2011-12-30"), $result->getReviewDate());
        $this->assertEquals("some status", $result->getStatus());
        $this->assertEquals("i modify this", $result->getComment());
        $this->assertEquals("UK", $result->getCountryCode());
    }

    public function testDeleteEmployeeImmigrationRecords(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->employeeImmigrationRecordDao->deleteEmployeeImmigrationRecords(2, $toTobedeletedIds);
        $this->assertEquals(1, $result);
    }

    public function testSearchEmployeeImmigrationRecords(): void
    {
        $employeeImmigrationSearchParams = new EmployeeImmigrationRecordSearchFilterParams();
        $employeeImmigrationSearchParams->setEmpNumber(1);
        $result = $this->employeeImmigrationRecordDao->searchEmployeeImmigrationRecords($employeeImmigrationSearchParams);
        $this->assertCount(1, $result);
        $this->assertTrue($result[0] instanceof EmployeeImmigrationRecord);
    }

    public function testSearchEmployeeImmigrationRecordsWithLimit(): void
    {
        $employeeImmigrationSearchParams = new EmployeeImmigrationRecordSearchFilterParams();
        $employeeImmigrationSearchParams->setEmpNumber(1);
        $employeeImmigrationSearchParams->setLimit(1);
        $result = $this->employeeImmigrationRecordDao->searchEmployeeImmigrationRecords($employeeImmigrationSearchParams);
        $this->assertCount(1, $result);
    }

    public function testGetSearchEmployeeImmigrationRecordsCount(): void
    {
        $employeeImmigrationSearchParams = new EmployeeImmigrationRecordSearchFilterParams();
        $employeeImmigrationSearchParams->setEmpNumber(1);
        $result = $this->employeeImmigrationRecordDao->getSearchEmployeeImmigrationRecordsCount($employeeImmigrationSearchParams);
        $this->assertEquals(1, $result);
    }
}
