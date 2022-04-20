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

use OrangeHRM\Admin\Service\CompanyStructureService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\TextHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeWorkShift;
use OrangeHRM\Framework\Services;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\Pim\Dao\EmployeeDao;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class EmployeeDaoTest extends KernelTestCase
{
    private EmployeeDao $employeeDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->employeeDao = new EmployeeDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmployeeDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetSubordinateIdListBySupervisorId(): void
    {
        $subordinateIdList = $this->employeeDao->getSubordinateIdListBySupervisorId(3, true);
        $this->assertEquals(2, count($subordinateIdList));

        $subordinateIdList = $this->employeeDao->getSubordinateIdListBySupervisorId(4, true);
        $this->assertEquals(3, count($subordinateIdList));

        $subordinateIdList = $this->employeeDao->getSubordinateIdListBySupervisorId(4, false);
        $this->assertEquals(1, count($subordinateIdList));

        $subordinateIdList = $this->employeeDao->getSubordinateIdListBySupervisorId(10, true);
        $this->assertEquals(0, count($subordinateIdList));

        $subordinateIdList = $this->employeeDao->getSubordinateIdListBySupervisorId(1, true);
        $this->assertEquals(0, count($subordinateIdList));

        $subordinateIdList = $this->employeeDao->getSubordinateIdListBySupervisorId(3, true);
        $subordinateIdArray = [1, 2];
        $this->assertEquals($subordinateIdArray, $subordinateIdList);
    }

    /**
     * @group ReportingChain
     */
    public function testGetSubordinateList(): void
    {
        $chain = $this->employeeDao->getSubordinateList(3, false, true);
        $this->assertTrue(count($chain) > 0);
    }

    /**
     * @group ReportingChain
     */
    public function testGetSubordinateList_ReportingChain_Simple2LevelHierarchy(): void
    {
        $chain = $this->employeeDao->getSubordinateList(3, false, true);
        $this->assertTrue(is_array($chain));
        $this->assertEquals(2, count($chain));

        list($subordinate1, $subordinate2) = $chain;

        $this->assertTrue($subordinate1 instanceof Employee);
        $this->assertEquals(1, $subordinate1->getEmpNumber());

        $this->assertTrue($subordinate2 instanceof Employee);
        $this->assertEquals(2, $subordinate2->getEmpNumber());
    }

    /**
     * @group ReportingChain
     */
    public function testGetSubordinateList_ReportingChain_3LevelHierarchy(): void
    {
        $chain = $this->employeeDao->getSubordinateList(5, false, true);
        $this->assertTrue(is_array($chain));
        $this->assertEquals(3, count($chain));

        list($subordinate1, $subordinate2, $subordinate3) = $chain;

        $this->assertTrue($subordinate1 instanceof Employee);
        $this->assertEquals(3, $subordinate1->getEmpNumber());

        $this->assertTrue($subordinate2 instanceof Employee);
        $this->assertEquals(1, $subordinate2->getEmpNumber());

        $this->assertTrue($subordinate3 instanceof Employee);
        $this->assertEquals(2, $subordinate3->getEmpNumber());

        $chain = $this->employeeDao->getSubordinateList(4, false, true);
        $this->assertTrue(is_array($chain));
        $this->assertEquals(3, count($chain));

        list($subordinate1, $subordinate2, $subordinate3) = $chain;

        $this->assertTrue($subordinate1 instanceof Employee);
        $this->assertEquals(3, $subordinate1->getEmpNumber());

        $this->assertTrue($subordinate2 instanceof Employee);
        $this->assertEquals(1, $subordinate2->getEmpNumber());

        $this->assertTrue($subordinate3 instanceof Employee);
        $this->assertEquals(2, $subordinate3->getEmpNumber());
    }

    public function testIsSupervisor(): void
    {
        $result = $this->employeeDao->isSupervisor(3);

        $this->assertTrue($result);
    }

    /**
     * @param Employee[] $employees
     * @return int[]
     */
    protected function getEmployeeIds(array $employees): array
    {
        $ids = [];
        foreach ($employees as $employee) {
            $ids[] = $employee->getEmpNumber();
        }
        return $ids;
    }

    public function testGetEmployeeIdList(): void
    {
        $employeeIdList = $this->employeeDao->getEmpNumberList();
        $this->assertEquals(5, count($employeeIdList));

        $employeeIdList = $this->employeeDao->getEmpNumberList();
        $employees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');
        $employeeIdArray = $this->getEmployeeIds($employees);
        $this->assertEquals($employeeIdArray, $employeeIdList);
    }

    public function testGetEmployeeIdListOneEmployee(): void
    {
        $q = $this->getEntityManager()->createQueryBuilder()->from(Employee::class, 'e');
        $q->delete()->where('e.empNumber > 1')->getQuery()->execute();

        $employeeIdList = $this->employeeDao->getEmpNumberList();
        $this->assertTrue(is_array($employeeIdList));
        $this->assertEquals(1, count($employeeIdList));
        $this->assertEquals(1, $employeeIdList[0]);
    }

    public function testDeleteEmployees(): void
    {
        $employees = TestDataService::loadObjectList(Employee::class, $this->fixture, 'Employee');
        foreach ($employees as $emp) {
            $empNumbers[] = $emp->getEmpNumber();
        }

        $retVal = $this->employeeDao->deleteEmployees($empNumbers);
        $this->assertEquals(count($empNumbers), $retVal);

        $retVal = $this->employeeDao->deleteEmployees($empNumbers);
        $this->assertEquals(0, $retVal);

        $retVal = $this->employeeDao->deleteEmployees([]);
        $this->assertEquals(0, $retVal);
    }

    public function testGetSupervisorIdListBySubordinateId(): void
    {
        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(3, true);
        $this->assertEquals(2, count($supervisorIdList));

        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(3, false);
        $this->assertEquals(2, count($supervisorIdList));

        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(2, true);
        $this->assertEquals(3, count($supervisorIdList));

        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(1, true);
        $this->assertEquals(3, count($supervisorIdList));

        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(1, false);
        $this->assertEquals(1, count($supervisorIdList));

        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(4, true);
        $this->assertEquals(0, count($supervisorIdList));

        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(5, true);
        $this->assertEquals(0, count($supervisorIdList));

        $supervisorIdList = $this->employeeDao->getSupervisorIdListBySubordinateId(3, true);
        $subordinateIdArray = [4, 5];
        $this->assertEquals($subordinateIdArray, $supervisorIdList);
    }

    public function testGetEmployeeWorkShift(): void
    {
        $workShift = $this->employeeDao->getEmployeeWorkShift(1);
        $this->assertTrue($workShift instanceof EmployeeWorkShift);
        $this->assertEquals('General', $workShift->getWorkShift()->getName());

        $workShift = $this->employeeDao->getEmployeeWorkShift(2);
        $this->assertNull($workShift);
    }

    public function testGetEmployeeList(): void
    {
        $this->createKernelWithMockServices([Services::TEXT_HELPER_SERVICE => new TextHelperService()]);
        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $empList = $this->employeeDao->getEmployeeList($employeeSearchFilterParams);
        $this->assertCount(5, $empList);
        $this->assertEquals('Kayla', $empList[0]->getFirstName());

        $employeeSearchFilterParams->setLimit(2);
        $empList = $this->employeeDao->getEmployeeList($employeeSearchFilterParams);
        $this->assertCount(2, $empList);
        $this->assertEquals('Ashley', $empList[1]->getFirstName());

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $employeeSearchFilterParams->setSortOrder(ListSorter::DESCENDING);
        $empList = $this->employeeDao->getEmployeeList($employeeSearchFilterParams);
        $this->assertCount(5, $empList);
        $this->assertEquals('Renukshan', $empList[0]->getFirstName());

        $this->createKernelWithMockServices(
            [
                Services::COMPANY_STRUCTURE_SERVICE => new CompanyStructureService(),
                Services::TEXT_HELPER_SERVICE => new TextHelperService(),
            ]
        );
        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $employeeSearchFilterParams->setSubunitId(2);
        $empList = $this->employeeDao->getEmployeeList($employeeSearchFilterParams);
        $this->assertCount(1, $empList);
        $this->assertEquals('Sales', $empList[0]->getSubDivision()->getName());

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $employeeSearchFilterParams->setSubunitId(1);
        $empList = $this->employeeDao->getEmployeeList($employeeSearchFilterParams);
        $this->assertCount(3, $empList);
    }

    public function testGetEmpNumbersByFilterParams(): void
    {
        $this->createKernelWithMockServices([Services::TEXT_HELPER_SERVICE => new TextHelperService()]);

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $empList = $this->employeeDao->getEmpNumbersByFilterParams($employeeSearchFilterParams);
        $this->assertEquals([1, 2, 3, 4, 5], $empList);

        $this->createKernelWithMockServices(
            [
                Services::COMPANY_STRUCTURE_SERVICE => new CompanyStructureService(),
                Services::TEXT_HELPER_SERVICE => new TextHelperService(),
            ]
        );
        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $employeeSearchFilterParams->setSubunitId(2);
        $empList = $this->employeeDao->getEmpNumbersByFilterParams($employeeSearchFilterParams);
        $this->assertEquals([1], $empList);

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $employeeSearchFilterParams->setSubunitId(1);
        $empList = $this->employeeDao->getEmpNumbersByFilterParams($employeeSearchFilterParams);
        $this->assertEquals([1, 2, 4], $empList);

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        // sort field override to employee.empNumber
        $employeeSearchFilterParams->setSortField('supervisor.firstName');
        $empList = $this->employeeDao->getEmpNumbersByFilterParams($employeeSearchFilterParams);
        $this->assertEquals([1, 2, 3, 4, 5], $empList);
    }

    public function testGetEmployeeCount(): void
    {
        $this->createKernelWithMockServices(
            [
                Services::COMPANY_STRUCTURE_SERVICE => new CompanyStructureService(),
                Services::TEXT_HELPER_SERVICE => new TextHelperService(),
            ]
        );

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $empList = $this->employeeDao->getEmpNumbersByFilterParams($employeeSearchFilterParams);
        $this->assertEquals([1, 2, 3, 4, 5], $empList);

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $employeeSearchFilterParams->setSubunitId(2);
        $empList = $this->employeeDao->getEmpNumbersByFilterParams($employeeSearchFilterParams);
        $this->assertEquals([1], $empList);

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $employeeSearchFilterParams->setSubunitId(1);
        $empList = $this->employeeDao->getEmpNumbersByFilterParams($employeeSearchFilterParams);
        $this->assertEquals([1, 2, 4], $empList);

        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        // sort field override to employee.empNumber
        $employeeSearchFilterParams->setSortField('supervisor.firstName');
        $empList = $this->employeeDao->getEmpNumbersByFilterParams($employeeSearchFilterParams);
        $this->assertEquals([1, 2, 3, 4, 5], $empList);
    }

    public function testGetEmailList(): void
    {
        $emailList = $this->employeeDao->getEmailList();
        $this->assertEquals([['workEmail' => 'kayla@xample.com', 'otherEmail' => 'kayla2@xample.com'],
                                  ['workEmail' => 'ashley@xample.com', 'otherEmail' => 'ashley2@xample.com'],
                                  ['workEmail' => 'renukshan@xample.com', 'otherEmail' => 'renukshan2@xample.com'],
                                  ['workEmail' => '', 'otherEmail' => ''],
                                  ['workEmail' => '', 'otherEmail' => '']], $emailList);
    }

    public function testIsWorkEmailIsAvailable(): void
    {
        $status = $this->employeeDao->isEmailAvailable('kayla0001@xample.com', 'kayla@xample.com');
        $this->assertEquals(true, $status);

        //with existing work email
        $status = $this->employeeDao->isEmailAvailable('kayla@xample.com', 'nihan@xample.com');
        $this->assertEquals(false, $status);

        //with existing other email
        $status = $this->employeeDao->isEmailAvailable('kayla2@xample.com', 'nihan2@xample.com');
        $this->assertEquals(false, $status);

        //with same email
        $status = $this->employeeDao->isEmailAvailable('ashley@xample.com', 'ashley@xample.com');
        $this->assertEquals(true, $status);

        //with null email
        $status = $this->employeeDao->isEmailAvailable('devi@admin.com', null);
        $this->assertEquals(true, $status);
    }
}
