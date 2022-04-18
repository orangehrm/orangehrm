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

class EmployeeJobDetail implements Serializable
{
    /**
     * @var
     */
    private $title = '';

    private $category = '';

    private $joinedDate = '';

    private $startDate = '';

    private $endDate = '';

    private $jobSpecification = '';

    private $employmentStatus = '';

    private $subunit = '';

    private $location = '';

    private $contractDetails = '';


    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getJobSpecification()
    {
        return $this->jobSpecification;
    }

    /**
     * @param string $jobSpecification
     */
    public function setJobSpecification($jobSpecification)
    {
        $this->jobSpecification = $jobSpecification;
    }

    /**
     * @return string
     */
    public function getEmploymentStatus()
    {
        return $this->employmentStatus;
    }

    /**
     * @param string $employmentStatus
     */
    public function setEmploymentStatus($employmentStatus)
    {
        $this->employmentStatus = $employmentStatus;
    }

    /**
     * @return string
     */
    public function getSubunit()
    {
        return $this->subunit;
    }

    /**
     * @param string $subunit
     */
    public function setSubunit($subunit)
    {
        $this->subunit = $subunit;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getContractDetails()
    {
        return $this->contractDetails;
    }

    /**
     * @param string $contractDetails
     */
    public function setContractDetails($contractDetails)
    {
        $this->contractDetails = $contractDetails;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getJoinedDate()
    {
        return $this->joinedDate;
    }

    /**
     * @param string $joinedDate
     */
    public function setJoinedDate($joinedDate)
    {
        $this->joinedDate = $joinedDate;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param string $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param string $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }


    public function toArray()
    {
        return array(
            'title' => $this->getTitle(),
            'category' => $this->getCategory(),
            'status' => $this->getEmploymentStatus(),
            'subunit' => $this->getSubunit(),
            'location' => $this->getLocation(),
            'joinedDate' => $this->getJoinedDate(),
            'startDate' => $this->getStartDate(),
            'endDate' => $this->getEndDate()

        );
    }

    /**
     * Setting Job details values
     *
     * @param $employee
     */
    public function build(\Employee $employee)
    {

        $this->setTitle($employee->getJobTitle()->jobTitleName);
        $this->setJoinedDate($employee->getJoinedDate());
        $this->setCategory($employee->getJobCategory()->getName());
        if (!empty($employee->contracts[0]->getEndDate())) {
            $this->setEndDate(substr($employee->contracts[0]->getEndDate(), 0, -9));
        }
        if (!empty($employee->contracts[0]->getStartDate())) {
            $this->setStartDate(substr($employee->contracts[0]->getStartDate(), 0, -9));
        }
        $this->setEmploymentStatus($this->_getEmpStatusName($employee->getEmpStatus()));
        if (!empty($employee->getLocations())) {
            $this->setLocation($employee->getLocations()[0]->getName());

        }
        $this->setSubunit($employee->getSubDivision()->getName());

    }

    /**
     * Get employee status name
     *
     * @param $statusId
     * @return string
     */
    protected function _getEmpStatusName($statusId)
    {

        $empStatusService = new \EmploymentStatusService();

        $statuses = $empStatusService->getEmploymentStatusList();

        foreach ($statuses as $status) {
            if ($status->getId() == $statusId) {
                return $status->getName();
            }
        }

        return '';
    }
}