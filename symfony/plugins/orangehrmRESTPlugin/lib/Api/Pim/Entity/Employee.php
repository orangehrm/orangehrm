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
namespace Orangehrm\Rest\Api\Pim\Entity;

use Orangehrm\Rest\Api\Entity\Serializable;
use Orangehrm\Rest\Api\Pim\Entity\Supervisor;

class Employee implements Serializable {
    /**
     * @var
     */
    private $firstName = '';

    private $middleName = '';

    private $lastName = '';

    private $age = 0;

    private $licenseNo ='';

    private $employeeId = 0;

    private $empBirthDate =  '';

    private $country;

    private $city;

    private $jobTitle;

    private $gender;

    private $mobile;

    private $workEmail;

    private $joinedDate;

    private $employeeStatus;

    private $nationality;

    private $supervisor;

    private $supervisorId;

    private $employeeFullName;

    private $unit;

    private $supervisors;


    /**
     * Employee constructor.
     * @param string $firstName
     * @param string $middleName
     * @param string $lastName
     * @param int $age age of the employee
     */
    public function __construct($firstName, $middleName, $lastName, $id)
    {

        $this->setFirstName($firstName)
            ->setMiddleName($middleName)
            ->setLastName($lastName)
            ->setEmployeeId($id);
        return $this;
    }


    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     * @return $this;
     */
    private function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param mixed $middleName
     * @return $this;
     */
    private function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     * @return $this;
     */
     private function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getLicenseNo()
    {
        return $this->licenseNo;
    }

    /**
     * @param string $licenseNo
     */
    public function setLicenseNo($licenseNo)
    {
        $this->licenseNo = $licenseNo;
    }

    /**
     * @return int
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @param int $employeeId
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return string
     */
    public function getEmpBirthDate()
    {
        return $this->empBirthDate;
    }

    /**
     * @param string $empBirthDate
     */
    public function setEmpBirthDate($empBirthDate)
    {
        $this->empBirthDate = $empBirthDate;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * @param mixed $jobTitle
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getWorkEmail()
    {
        return $this->workEmail;
    }

    /**
     * @param mixed $workEmail
     */
    public function setWorkEmail($workEmail)
    {
        $this->workEmail = $workEmail;
    }

    /**
     * @return mixed
     */
    public function getJoinedDate()
    {
        return $this->joinedDate;
    }

    /**
     * @param mixed $joinedDate
     */
    public function setJoinedDate($joinedDate)
    {
        $this->joinedDate = $joinedDate;
    }

    /**
     * @return mixed
     */
    public function getEmployeeStatus()
    {
        return $this->employeeStatus;
    }

    /**
     * @param mixed $employeeStatus
     */
    public function setEmployeeStatus($employeeStatus)
    {
        $this->employeeStatus = $employeeStatus;
    }

    /**
     * @return mixed
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param mixed $nationality
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }

    /**
     * @return mixed
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }

    /**
     * @param mixed $supervisor
     */
    public function setSupervisor($supervisor)
    {
        $this->supervisor = $supervisor;
    }

    /**
     * @return mixed
     */
    public function getSupervisorId()
    {
        return $this->supervisorId;
    }

    /**
     * @param mixed $supervisorId
     */
    public function setSupervisorId($supervisorId)
    {
        $this->supervisorId = $supervisorId;
    }

    /**
     * @return mixed
     */
    public function getEmployeeFullName()
    {
        return $this->employeeFullName;
    }

    /**
     * @param mixed $employeeFullName
     */
    public function setEmployeeFullName($employeeFullName)
    {
        $this->employeeFullName = $employeeFullName;
    }

    /**
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param mixed $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return mixed
     */
    public function getSupervisors()
    {
        return $this->supervisors;
    }

    /**
     * @param mixed $supervisors
     */
    public function setSupervisors($supervisors)
    {
        $this->supervisors = $supervisors;
    }


    /**
     * Converting to an array
     * @return array
     */
    public function toArray() {
        return array(
            'firstName' => $this->getFirstName(),
            'middleName' => $this->getMiddleName(),
            'id'         => $this->getEmployeeId(),
            'lastName' => $this->getLastName(),
            'fullName' => $this->getEmployeeFullName(),
            'status'   => $this->getEmployeeStatus(),
            'dob'      => $this->getEmpBirthDate(),
            'unit'    =>$this->getUnit(),
            'jobtitle'=> $this->getJobTitle(),
            'supervisor' => $this->getSupervisors()

        );
    }

    /**
     * Converting Doctraine Employee entity values to Employee
     *
     * @param $employee Doctraine Entity
     */
    public function buildEmployee($employee){

        $this->setCity($employee->getCity());
        $this->setCountry($employee->getCountry());
        $this->setEmpBirthDate($employee->getEmpBirthday());
        $this->setEmployeeStatus($employee->getEmployeeStatus()->getName());
        $this->setGender($employee->getEmpGender());
        $this->setEmployeeFullName($employee->getFullName());
        $this->setJobTitle($employee->getJobTitleName());
        $this->setUnit($employee->getSubDivision()->getName());
        $supervisorList [] = array();
        foreach ($employee->getSupervisors() as $supervisor){

            $supervisorEnt = new Supervisor($supervisor->getFullName(),$supervisor->getEmployeeId());
            $supervisorList = $supervisorEnt->toArray();
        }
        $this->setSupervisors($supervisorList);
    }
}
