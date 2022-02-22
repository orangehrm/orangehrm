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

namespace OrangeHRM\Maintenance\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Employee;

class PurgeEmployeeModel implements Normalizable
{
    private Employee $employee;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'empNumber' => $this->employee->getEmpNumber(),
            'firstName' => $this->employee->getFirstName(),
            'lastName' => $this->employee->getLastName(),
            'middleName' => $this->employee->getMiddleName(),
            'nickName' => $this->employee->getNickName(),
            'smoker' => $this->employee->getSmoker(),
            'ethnicRaceCode' => $this->employee->getEthnicRaceCode(),
            'birthday' => $this->employee->getBirthday(),
            'nationality' => $this->employee->getNationality(),
            'gender' => $this->employee->getGender(),
            'maritalStatus' => $this->employee->getMaritalStatus(),
            'ssnNumber' => $this->employee->getSsnNumber(),
            'sinNumber' => $this->employee->getSinNumber(),
            'otherId' => $this->employee->getOtherId(),
            'drivingLicenseNo' => $this->employee->getDrivingLicenseNo(),
            'drivingLicenseExpiredDate' => $this->employee->getDrivingLicenseExpiredDate(),
            'militaryService' => $this->employee->getMilitaryService(),
            'empStatus' => $this->employee->getEmpStatus(),
            'jobTitle' => $this->employee->getJobTitle(),
            'jobCategory' => $this->employee->getJobCategory(),
            'subDivision' => $this->employee->getSubDivision(),
            'street1' => $this->employee->getStreet1(),
            'street2' => $this->employee->getStreet2(),
            'city' => $this->employee->getCity(),
            'country' => $this->employee->getCountry(),
            'province' => $this->employee->getProvince(),
            'zipcode' => $this->employee->getZipcode(),
            'homeTelephone' => $this->employee->getHomeTelephone(),
            'mobile' => $this->employee->getMobile(),
            'workTelephone' => $this->employee->getWorkTelephone(),
            'workEmail' => $this->employee->getWorkEmail(),
            'joinedDate' => $this->employee->getJoinedDate(),
            'otherEmail' => $this->employee->getOtherEmail(),
            'custom1' => $this->employee->getCustom1(),
            'custom2' => $this->employee->getCustom2(),
            'custom3' => $this->employee->getCustom3(),
            'custom4' => $this->employee->getCustom4(),
            'custom5' => $this->employee->getCustom5(),
            'custom6' => $this->employee->getCustom6(),
            'custom7' => $this->employee->getCustom7(),
            'custom8' => $this->employee->getCustom8(),
            'custom9' => $this->employee->getCustom9(),
            'custom10' => $this->employee->getCustom10(),
            'empPicture' => [],
            'employeeAttachment' => [],
            'empEmergencyContact' => [],
            'empDependent' => [],
            'employeeImmigrationRecord' => [],
            'empWorkExperience' => [],
            'employeeEducation' => [],
            'employeeSkill' => [],
            'employeeLanguage' => [],
            'employeeMembership' => [],
            'empUsTaxExemption' => [],
            'employeeLicense' => [],
            'employeeSalary' => [],
            'empLocations' => [],
            'empContract' => [],
            'user' => [],
            'reportTo' => [],
            'leaveRequestComment' => [
                'comment' => 'Purge'
            ],
            'leaveComment' => [
                'comment' => 'Purge'
            ],
            'attendanceNote' => [
                'punchInNote' => 'Purge',
                'punchOutNote' => 'Purge'
            ],
            'timesheetItem' => [
                'comment' => 'Purge'
            ]
        ];
    }
}
