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

namespace OrangeHRM\Tests\Pim\Service;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\EmployeeImmigrationRecord;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Dao\EmployeeImmigrationRecordDao;
use OrangeHRM\Pim\Dto\EmployeeImmigrationRecordSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeImmigrationRecordService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Service
 */
class EmployeeImmigrationServiceTest extends TestCase
{
    private EmployeeImmigrationRecordService $employeeImmigrationRecordService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->employeeImmigrationRecordService = new EmployeeImmigrationRecordService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeImmigrationRecordList(): void
    {
        $employeeImmigrationRecord1 = new EmployeeImmigrationRecord();
        $employeeImmigrationRecord1->getDecorator()->setEmployeeByEmpNumber(1);
        $employeeImmigrationRecord1->setRecordId('1');
        $employeeImmigrationRecord1->setNumber('HVN0003472');
        $employeeImmigrationRecord1->setType(1);
        $employeeImmigrationRecord1->setIssuedDate(new DateTime('2010-12-12'));
        $employeeImmigrationRecord1->setExpiryDate(new DateTime('2011-12-12'));
        $employeeImmigrationRecord1->setStatus('some status');
        $employeeImmigrationRecord1->setComment('test Comment');
        $employeeImmigrationRecord1->setReviewDate(new DateTime('2011-12-30'));
        $employeeImmigrationRecord1->setCountryCode('UK');

        $employeeImmigrationRecord2 = new EmployeeImmigrationRecord();
        $employeeImmigrationRecord2->getDecorator()->setEmployeeByEmpNumber(1);
        $employeeImmigrationRecord2->setRecordId('2');
        $employeeImmigrationRecord2->setNumber('RTF33323411');
        $employeeImmigrationRecord2->setType(2);
        $employeeImmigrationRecord2->setIssuedDate(new DateTime('2020-12-12'));
        $employeeImmigrationRecord2->setExpiryDate(new DateTime('2021-12-12'));
        $employeeImmigrationRecord2->setStatus('some status1');
        $employeeImmigrationRecord2->setComment('test Comment1');
        $employeeImmigrationRecord2->setReviewDate(new DateTime('2021-12-30'));
        $employeeImmigrationRecord2->setCountryCode('LK');

        $employeeImmigrationRecordDao = $this->getMockBuilder(EmployeeImmigrationRecordDao::class)->getMock();

        $employeeImmigrationRecordDao->expects($this->once())
            ->method('getEmployeeImmigrationRecordList')
            ->with(1)
            ->will($this->returnValue([$employeeImmigrationRecord1, $employeeImmigrationRecord2]));

        $this->employeeImmigrationRecordService->setEmployeeImmigrationRecordDao($employeeImmigrationRecordDao);
        $employeeImmigrationRecords = $this->employeeImmigrationRecordService->getEmployeeImmigrationRecordList(1);
        $this->assertCount(2, $employeeImmigrationRecords);
        $this->assertEquals('HVN0003472', $employeeImmigrationRecords[0]->getNumber());
        $this->assertEquals('RTF33323411', $employeeImmigrationRecords[1]->getNumber());
    }

    public function testGetEmployeeImmigrationRecord(): void
    {
        $employeeImmigrationRecord1 = new EmployeeImmigrationRecord();
        $employeeImmigrationRecord1->getDecorator()->setEmployeeByEmpNumber(1);
        $employeeImmigrationRecord1->setRecordId('1');
        $employeeImmigrationRecord1->setNumber('HVN0003472');
        $employeeImmigrationRecord1->setType(1);
        $employeeImmigrationRecord1->setIssuedDate(new DateTime('2010-12-12'));
        $employeeImmigrationRecord1->setExpiryDate(new DateTime('2011-12-12'));
        $employeeImmigrationRecord1->setStatus('some status');
        $employeeImmigrationRecord1->setComment('test Comment');
        $employeeImmigrationRecord1->setReviewDate(new DateTime('2011-12-30'));
        $employeeImmigrationRecord1->setCountryCode('UK');

        $employeeImmigrationRecordDao = $this->getMockBuilder(EmployeeImmigrationRecordDao::class)->getMock();


        $employeeImmigrationRecordDao->expects($this->once())
            ->method('getEmployeeImmigrationRecord')
            ->with(1, 1)
            ->will($this->returnValue($employeeImmigrationRecord1));

        $this->employeeImmigrationRecordService->setEmployeeImmigrationRecordDao($employeeImmigrationRecordDao);
        $employeeImmigrationRecord = $this->employeeImmigrationRecordService->getEmployeeImmigrationRecord(1, 1);
        $this->assertEquals('HVN0003472', $employeeImmigrationRecord->getNumber());
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

        $employeeImmigrationRecordDao = $this->getMockBuilder(EmployeeImmigrationRecordDao::class)->getMock();

        $employeeImmigrationRecordDao->expects($this->once())
            ->method('saveEmployeeImmigrationRecord')
            ->with($employeeImmigrationRecord)
            ->will($this->returnValue($employeeImmigrationRecord));

        $this->employeeImmigrationRecordService->setEmployeeImmigrationRecordDao($employeeImmigrationRecordDao);

        $employeeImmigrationRecordObj = $this->employeeImmigrationRecordService->saveEmployeeImmigrationRecord($employeeImmigrationRecord);
        $this->assertEquals('HVN0003472', $employeeImmigrationRecordObj->getNumber());
        $this->assertEquals(1, $employeeImmigrationRecordObj->getType());
        $this->assertEquals(new DateTime("2010-12-12"), $employeeImmigrationRecordObj->getIssuedDate());
        $this->assertEquals(new DateTime("2011-12-12"), $employeeImmigrationRecordObj->getExpiryDate());
        $this->assertEquals(new DateTime("2011-12-30"), $employeeImmigrationRecordObj->getReviewDate());
        $this->assertEquals("some status", $employeeImmigrationRecordObj->getStatus());
        $this->assertEquals("test Comment", $employeeImmigrationRecordObj->getComment());
        $this->assertEquals("UK", $employeeImmigrationRecordObj->getCountryCode());
        $this->assertEquals("E001", $employeeImmigrationRecordObj->getEmployee()->getEmployeeId());
        $this->assertEquals("Kayla", $employeeImmigrationRecordObj->getEmployee()->getFirstName());
    }

    public function testDeleteEmployeeImmigrationRecords(): void
    {
        $employeeImmigrationRecordDao = $this->getMockBuilder(EmployeeImmigrationRecordDao::class)->getMock();

        $employeeImmigrationRecordDao->expects($this->once())
            ->method('deleteEmployeeImmigrationRecords')
            ->with(1, [1, 2])
            ->will($this->returnValue(2));

        $this->employeeImmigrationRecordService->setEmployeeImmigrationRecordDao($employeeImmigrationRecordDao);

        $rows = $this->employeeImmigrationRecordService->deleteEmployeeImmigrationRecords(1, [1, 2]);
        $this->assertEquals(2, $rows);
    }

    public function testSearchEmployeeImmigrationRecords(): void
    {
        $employeeImmigrationRecord1 = new EmployeeImmigrationRecord();
        $employeeImmigrationRecord1->getDecorator()->setEmployeeByEmpNumber(1);
        $employeeImmigrationRecord1->setNumber('HVN0003472');

        $employeeImmigrationRecord2 = new EmployeeImmigrationRecord();
        $employeeImmigrationRecord2->getDecorator()->setEmployeeByEmpNumber(1);
        $employeeImmigrationRecord2->setNumber('RTF33323411');


        $employeeImmigrationRecordList = [$employeeImmigrationRecord1, $employeeImmigrationRecord2];
        $employeeImmigrationRecordSearchParams = new EmployeeImmigrationRecordSearchFilterParams();
        $employeeImmigrationRecordSearchParams->setEmpNumber(1);
        $employeeImmigrationRecordDao = $this->getMockBuilder(EmployeeImmigrationRecordDao::class)->getMock();

        $employeeImmigrationRecordDao->expects($this->once())
            ->method('searchEmployeeImmigrationRecords')
            ->with($employeeImmigrationRecordSearchParams)
            ->will($this->returnValue($employeeImmigrationRecordList));

        $this->employeeImmigrationRecordService->setEmployeeImmigrationRecordDao($employeeImmigrationRecordDao);
        $result = $this->employeeImmigrationRecordService->searchEmployeeImmigrationRecords($employeeImmigrationRecordSearchParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof EmployeeImmigrationRecord);
    }

    public function testGetSearchEmployeeImmigrationRecordsCount(): void
    {
        $employeeImmigrationRecordSearchParams = new EmployeeImmigrationRecordSearchFilterParams();
        $employeeImmigrationRecordSearchParams->setEmpNumber(1);
        $employeeImmigrationRecordDao = $this->getMockBuilder(EmployeeImmigrationRecordDao::class)->getMock();

        $employeeImmigrationRecordDao->expects($this->once())
            ->method('getSearchEmployeeImmigrationRecordsCount')
            ->with($employeeImmigrationRecordSearchParams)
            ->will($this->returnValue(2));
        $this->employeeImmigrationRecordService->setEmployeeImmigrationRecordDao($employeeImmigrationRecordDao);
        $result = $this->employeeImmigrationRecordService->getSearchEmployeeImmigrationRecordsCount($employeeImmigrationRecordSearchParams);
        $this->assertEquals(2, $result);
    }
}
