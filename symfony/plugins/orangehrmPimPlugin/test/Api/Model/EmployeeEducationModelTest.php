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

use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeEducation;
use OrangeHRM\Entity\Education;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\Model\EmployeeEducationModel;
use OrangeHRM\Tests\Util\KernelTestCase;
use DateTime;

/**
 * @group Pim
 * @group Model
 */
class EmployeeEducationModelTest extends KernelTestCase
{
    use ServiceContainerTrait;

    public function testToArray()
    {
        $resultArray = [
            "id"=> 1,
            "institute"=> "UoP",
            "major"=> "CE",
            "year"=> 2020,
            "score"=> "First Class",
            "startDate"=> '2017-01-01',
            "endDate"=> '2020-12-31',
            "education"=> [
                "id"=> 1,
                "name"=> "BSc"
            ]
        ];

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName('First');
        $employee->setMiddleName('Middle');
        $employee->setLastName('Last');
        $employee->setEmployeeId('0001');
        $employee->setEmployeeTerminationRecord(null);

        $education = new Education();
        $education->setId(1);
        $education->setName('BSc');

        $employeeEducation = new EmployeeEducation();
        $employeeEducation->setId(1);
        $employeeEducation->setInstitute('UoP');
        $employeeEducation->setMajor('CE');
        $employeeEducation->setYear(2020);
        $employeeEducation->setScore('First Class');
        $employeeEducation->setStartDate(new DateTime('2017-01-01'));
        $employeeEducation->setEndDate(new DateTime('2020-12-31'));
        $employeeEducation->setEducation($education);
        $employeeEducation->setEmployee($employee);

        $employeeModel = new EmployeeEducationModel($employeeEducation);
        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        $this->assertEquals($resultArray, $employeeModel->toArray());
    }
}
