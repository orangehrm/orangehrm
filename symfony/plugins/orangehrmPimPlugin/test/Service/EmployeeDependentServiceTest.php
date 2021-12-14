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
use OrangeHRM\Entity\EmpDependent;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Dao\EmployeeDependentDao;
use OrangeHRM\Pim\Dto\EmployeeDependentSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeDependentService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Service
 */
class EmployeeDependentServiceTest extends TestCase
{
    private EmployeeDependentService $employeeDependentService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->employeeDependentService = new EmployeeDependentService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPimPlugin/test/fixtures/EmpDependentDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeDependents(): void
    {
        $empDependent1 = new EmpDependent();
        $empDependent1->getDecorator()->setEmployeeByEmpNumber(1);
        $empDependent1->setName('Abrahamson');
        $empDependent1->setRelationshipType('child');
        $empDependent1->setDateOfBirth(new DateTime('2007-02-23'));

        $empDependent2 = new EmpDependent();
        $empDependent2->getDecorator()->setEmployeeByEmpNumber(1);
        $empDependent2->setName('Abram');
        $empDependent2->setRelationship('friend');
        $empDependent2->setRelationshipType('other');
        $empDependent2->setDateOfBirth(new DateTime('2004-02-23'));

        $employeeDependentDao = $this->getMockBuilder(EmployeeDependentDao::class)->getMock();

        $employeeDependentDao->expects($this->once())
            ->method('getEmployeeDependents')
            ->with(1)
            ->will($this->returnValue([$empDependent1, $empDependent2]));

        $this->employeeDependentService->setEmployeeDependentDao($employeeDependentDao);
        $empDependents = $this->employeeDependentService->getEmployeeDependents(1);
        $this->assertCount(2, $empDependents);
        $this->assertEquals('Abrahamson', $empDependents[0]->getName());
        $this->assertEquals('Abram', $empDependents[1]->getName());
    }

    public function testGetEmployeeDependent(): void
    {
        $empDependent1 = new EmpDependent();
        $empDependent1->getDecorator()->setEmployeeByEmpNumber(1);
        $empDependent1->setName('Abrahamson');
        $empDependent1->setRelationshipType('child');
        $empDependent1->setDateOfBirth(new DateTime('2007-02-23'));

        $employeeDependentDao = $this->getMockBuilder(EmployeeDependentDao::class)->getMock();


        $employeeDependentDao->expects($this->once())
            ->method('getEmployeeDependent')
            ->with(1, 1)
            ->will($this->returnValue($empDependent1));

        $this->employeeDependentService->setEmployeeDependentDao($employeeDependentDao);
        $empDependent = $this->employeeDependentService->getEmployeeDependent(1, 1);
        $this->assertEquals('Abrahamson', $empDependent->getName());
    }

    public function testSaveEmployeeDependent(): void
    {
        $employee = $this->getEntityReference(Employee::class, 1);
        $empDependent = new EmpDependent();
        $empDependent->setEmployee($employee);
        $empDependent->setName("Barker");
        $empDependent->setRelationshipType(EmpDependent::RELATIONSHIP_TYPE_CHILD);

        $employeeDependentDao = $this->getMockBuilder(EmployeeDependentDao::class)->getMock();

        $employeeDependentDao->expects($this->once())
            ->method('saveEmployeeDependent')
            ->with($empDependent)
            ->will($this->returnValue($empDependent));

        $this->employeeDependentService->setEmployeeDependentDao($employeeDependentDao);

        $empDependentObj = $this->employeeDependentService->saveEmployeeDependent($empDependent);
        $this->assertEquals("Barker", $empDependentObj->getName());
        $this->assertEquals("child", $empDependentObj->getRelationshipType());
        $this->assertEquals("", $empDependentObj->getRelationship());
        $this->assertNull($empDependentObj->getDateOfBirth());
        $this->assertEquals(1, $empDependentObj->getEmployee()->getEmpNumber());
        $this->assertEquals("Kayla", $empDependentObj->getEmployee()->getFirstName());
    }

    public function testDeleteEmployeeDependents(): void
    {
        $employeeDependentDao = $this->getMockBuilder(EmployeeDependentDao::class)->getMock();

        $employeeDependentDao->expects($this->once())
            ->method('deleteEmployeeDependents')
            ->with(1, [1, 2])
            ->will($this->returnValue(2));

        $this->employeeDependentService->setEmployeeDependentDao($employeeDependentDao);

        $rows = $this->employeeDependentService->deleteEmployeeDependents(1, [1, 2]);
        $this->assertEquals(2, $rows);
    }

    public function testSearchEmployeeDependent(): void
    {
        $empDependent1 = new EmpDependent();
        $empDependent1->getDecorator()->setEmployeeByEmpNumber(1);
        $empDependent1->setName('Abrahamson');
        $empDependent1->setRelationshipType('child');
        $empDependent1->setDateOfBirth(new DateTime('2007-02-23'));

        $empDependent2 = new EmpDependent();
        $empDependent2->getDecorator()->setEmployeeByEmpNumber(1);
        $empDependent2->setName('Abram');
        $empDependent2->setRelationship('friend');
        $empDependent2->setRelationshipType('other');
        $empDependent2->setDateOfBirth(new DateTime('2004-02-23'));

        $empDependentList = [$empDependent1, $empDependent2];
        $empDependentSearchParams = new EmployeeDependentSearchFilterParams();
        $empDependentSearchParams->setEmpNumber(1);
        $empDependentDao = $this->getMockBuilder(EmployeeDependentDao::class)->getMock();

        $empDependentDao->expects($this->once())
            ->method('searchEmployeeDependent')
            ->with($empDependentSearchParams)
            ->will($this->returnValue($empDependentList));

        $this->employeeDependentService->setEmployeeDependentDao($empDependentDao);
        $result = $this->employeeDependentService->searchEmployeeDependent($empDependentSearchParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof EmpDependent);
    }

    public function testGetSearchEmployeeDependentsCount(): void
    {
        $empDependentSearchParams = new EmployeeDependentSearchFilterParams();
        $empDependentSearchParams->setEmpNumber(1);
        $empDependentDao = $this->getMockBuilder(EmployeeDependentDao::class)->getMock();

        $empDependentDao->expects($this->once())
            ->method('getSearchEmployeeDependentsCount')
            ->with($empDependentSearchParams)
            ->will($this->returnValue(2));
        $this->employeeDependentService->setEmployeeDependentDao($empDependentDao);
        $result = $this->employeeDependentService->getSearchEmployeeDependentsCount($empDependentSearchParams);
        $this->assertEquals(2, $result);
    }
}
