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

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Pim\Dao\EmployeeReportingMethodDao;
use OrangeHRM\Pim\Dto\EmployeeSubordinateSearchFilterParams;
use OrangeHRM\Pim\Dto\EmployeeSupervisorSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 * @group Yasiru
 */
class EmployeeReportingMethodDaoTest extends TestCase
{
    private EmployeeReportingMethodDao $employeeReportingMethodDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->employeeReportingMethodDao = new EmployeeReportingMethodDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmPimPlugin/test/fixtures/EmployeeReportingMethodDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmployeeReportToByEmpNumbers_WhenReportToAvailable(): void
    {
        $result = $this->employeeReportingMethodDao->getEmployeeReportToByEmpNumbers(2, 1);
        $this->assertEquals(1, $result->getSupervisor()->getEmpNumber());
        $this->assertEquals(2, $result->getSubordinate()->getEmpNumber());
    }

    public function testGetEmployeeReportToByEmpNumbers_WhenReportToNotAvailable(): void
    {
        $result = $this->employeeReportingMethodDao->getEmployeeReportToByEmpNumbers(1, 2);
        $this->assertNull($result);
    }

    public function testGetSearchEmployeeSubordinatesCount_WhenSubordinatesAvailable(): void
    {
        $employeeSubordinateSearchFilterParams = new EmployeeSubordinateSearchFilterParams();
        $employeeSubordinateSearchFilterParams->setEmpNumber(1);
        $result = $this->employeeReportingMethodDao->getSearchEmployeeSubordinatesCount(
            $employeeSubordinateSearchFilterParams
        );
        $this->assertEquals(3, $result);
    }

    public function testGetSearchEmployeeSubordinatesCount_WhenSubordinatesNotAvailable(): void
    {
        $employeeSubordinateSearchFilterParams = new EmployeeSubordinateSearchFilterParams();
        $employeeSubordinateSearchFilterParams->setEmpNumber(5);
        $result = $this->employeeReportingMethodDao->getSearchEmployeeSubordinatesCount(
            $employeeSubordinateSearchFilterParams
        );
        $this->assertEquals(0, $result);
    }

    public function testGetSearchImmediateEmployeeSupervisorsCount_WhenSupervisorsAvailable(): void
    {
        $employeeSupervisorSearchFilterParams = new EmployeeSupervisorSearchFilterParams();
        $employeeSupervisorSearchFilterParams->setEmpNumber(5);
        $result = $this->employeeReportingMethodDao->getSearchImmediateEmployeeSupervisorsCount(
            $employeeSupervisorSearchFilterParams
        );
        $this->assertEquals(2, $result);
    }

    public function testGetSearchImmediateEmployeeSupervisorsCount_WhenSupervisorsNotAvailable(): void
    {
        $employeeSupervisorSearchFilterParams = new EmployeeSupervisorSearchFilterParams();
        $employeeSupervisorSearchFilterParams->setEmpNumber(1);
        $result = $this->employeeReportingMethodDao->getSearchImmediateEmployeeSupervisorsCount(
            $employeeSupervisorSearchFilterParams
        );
        $this->assertEquals(0, $result);
    }


    public function testSearchImmediateEmployeeSupervisors_WhenSupervisorsNotAvailable(): void
    {
        $employeeSupervisorSearchFilterParams = new EmployeeSupervisorSearchFilterParams();
        $employeeSupervisorSearchFilterParams->setEmpNumber(1);
        $result = $this->employeeReportingMethodDao->searchImmediateEmployeeSupervisors(
            $employeeSupervisorSearchFilterParams
        );
        $this->assertEquals([], $result);
    }

    public function testSearchImmediateEmployeeSupervisors_WhenSupervisorsAvailable(): void
    {
        $employeeSupervisorSearchFilterParams = new EmployeeSupervisorSearchFilterParams();
        $employeeSupervisorSearchFilterParams->setEmpNumber(2);
        $result = $this->employeeReportingMethodDao->searchImmediateEmployeeSupervisors(
            $employeeSupervisorSearchFilterParams
        );
        $this->assertEquals(1, $result[0]->getSupervisor()->getEmpNumber());
        $this->assertEquals(2, $result[0]->getSubordinate()->getEmpNumber());
        $this->assertEquals('Indirect', $result[0]->getReportingMethod()->getName());
    }

    public function testSearchEmployeeSubordinates_WhenSubordinatesAvailable(): void
    {
        $employeeSubordinateSearchFilterParams = new EmployeeSubordinateSearchFilterParams();
        $employeeSubordinateSearchFilterParams->setEmpNumber(1);
        $result = $this->employeeReportingMethodDao->searchEmployeeSubordinates(
            $employeeSubordinateSearchFilterParams
        );
        $this->assertEquals(3, sizeof($result));
        $this->assertEquals(1, $result[0]->getSupervisor()->getEmpNumber());
        $this->assertEquals(2, $result[0]->getSubordinate()->getEmpNumber());
        $this->assertEquals('Indirect', $result[0]->getReportingMethod()->getName());
        $this->assertEquals(1, $result[1]->getSupervisor()->getEmpNumber());
        $this->assertEquals(4, $result[1]->getSubordinate()->getEmpNumber());
        $this->assertEquals('Direct', $result[1]->getReportingMethod()->getName());
        $this->assertEquals(1, $result[2]->getSupervisor()->getEmpNumber());
        $this->assertEquals(3, $result[2]->getSubordinate()->getEmpNumber());
        $this->assertEquals('Indirect', $result[2]->getReportingMethod()->getName());
    }

    public function testSearchEmployeeSubordinates_WhenSubordinatesNotAvailable(): void
    {
        $employeeSubordinateSearchFilterParams = new EmployeeSubordinateSearchFilterParams();
        $employeeSubordinateSearchFilterParams->setEmpNumber(6);
        $result = $this->employeeReportingMethodDao->searchEmployeeSubordinates(
            $employeeSubordinateSearchFilterParams
        );
        $this->assertEquals(0, sizeof($result));
    }


    public function testSaveEmployeeReportTo_WithSupervisor(): void
    {
        $supervisor = new ReportTo();
        $supervisor->getDecorator()->setSubordinateEmployeeByEmpNumber(6);
        $supervisor->getDecorator()->setSupervisorEmployeeByEmpNumber(1);
        $supervisor->getDecorator()->setReportingMethodByReportingMethodId(2);
        $savedSupervisor = $this->employeeReportingMethodDao->saveEmployeeReportTo($supervisor);
        $this->assertEquals(1, $savedSupervisor->getSupervisor()->getEmpNumber());
        $this->assertEquals(6, $savedSupervisor->getSubordinate()->getEmpNumber());
        $this->assertEquals('Direct', $savedSupervisor->getReportingMethod()->getName());
    }

    public function testSaveEmployeeReportTo_WithSubordinate(): void
    {
        $subordinate = new ReportTo();
        $subordinate->getDecorator()->setSubordinateEmployeeByEmpNumber(6);
        $subordinate->getDecorator()->setSupervisorEmployeeByEmpNumber(5);
        $subordinate->getDecorator()->setReportingMethodByReportingMethodId(1);
        $savedSubordinate = $this->employeeReportingMethodDao->saveEmployeeReportTo($subordinate);
        $this->assertEquals(5, $savedSubordinate->getSupervisor()->getEmpNumber());
        $this->assertEquals(6, $savedSubordinate->getSubordinate()->getEmpNumber());
        $this->assertEquals('Indirect', $savedSubordinate->getReportingMethod()->getName());
    }

    public function testDeleteEmployeeSupervisors(): void
    {
        $empNumber = 5;
        $toDeleteIds = [2, 4];
        $result = $this->employeeReportingMethodDao->deleteEmployeeSupervisors($empNumber, $toDeleteIds);
        $this->assertEquals(2, $result);
    }

    public function testDeleteEmployeeSubordinates(): void
    {
        $empNumber = 1;
        $toDeleteIds = [2, 3];
        $result = $this->employeeReportingMethodDao->deleteEmployeeSubordinates($empNumber, $toDeleteIds);
        $this->assertEquals(2, $result);
    }
}
