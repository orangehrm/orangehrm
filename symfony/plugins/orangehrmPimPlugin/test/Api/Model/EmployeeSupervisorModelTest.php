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

namespace OrangeHRM\Tests\Pim\Api\Model;

use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Pim\Api\Model\EmployeeSupervisorModel;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Pim
 * @group Model
 */
class EmployeeSupervisorModelTest extends TestCase
{
    public function testToArray()
    {
        $resultArray = [
            "supervisor" => [
                "empNumber" => 1,
                "firstName" => 'Kayla',
                "lastName" => "Abbey",
                "middleName" => "",
                "terminationId" => null,
            ],
            "reportingMethod" => [
                "id" => 1,
                "name" => 'Direct'
            ]
        ];

        $employee1 = new Employee();
        $employee1->setFirstName('Kayla');
        $employee1->setLastName('Abbey');
        $employee1->setEmployeeId('0001');
        $employee1->setEmpNumber(1);
        $employee1->setEmployeeTerminationRecord(null);

        $employee2 = new Employee();
        $employee2->setFirstName('Andy');
        $employee2->setLastName('Smith');
        $employee2->setEmployeeId('0002');
        $employee2->setEmpNumber(2);
        $employee2->setEmployeeTerminationRecord(null);

        $reportingMethod = new ReportingMethod();
        $reportingMethod->setName('Direct');
        $reportingMethod->setId(1);

        $reportTo = new ReportTo();
        $reportTo->setSupervisor($employee1);
        $reportTo->setSubordinate($employee2);
        $reportTo->setReportingMethod($reportingMethod);

        $employeeSupervisorModel = new EmployeeSupervisorModel($reportTo);
        $this->assertEquals($resultArray, $employeeSupervisorModel->toArray());
    }
}
