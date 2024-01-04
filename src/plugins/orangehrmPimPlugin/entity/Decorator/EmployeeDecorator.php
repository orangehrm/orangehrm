<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmploymentStatus;
use OrangeHRM\Entity\JobCategory;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\Location;
use OrangeHRM\Entity\Nationality;
use OrangeHRM\Entity\Subunit;

class EmployeeDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    /**
     * @var Employee
     */
    protected Employee $employee;

    /**
     * @param Employee $employee
     */
    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    /**
     * @return Employee
     */
    protected function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @return string|null
     */
    public function getJoinedDate(): ?string
    {
        $date = $this->getEmployee()->getJoinedDate();
        return $this->getDateTimeHelper()->formatDate($date);
    }

    /**
     * @return string|null
     */
    public function getDrivingLicenseExpiredDate(): ?string
    {
        $date = $this->getEmployee()->getDrivingLicenseExpiredDate();
        return $this->getDateTimeHelper()->formatDate($date);
    }

    /**
     * @return string|null
     */
    public function getBirthday(): ?string
    {
        $date = $this->getEmployee()->getBirthday();
        return $this->getDateTimeHelper()->formatDate($date);
    }

    /**
     * @return bool
     */
    public function getSmoker(): bool
    {
        return $this->getEmployee()->getSmoker() == 1;
    }

    /**
     * @param bool|null $smoker
     */
    public function setSmoker(?bool $smoker): void
    {
        if (!is_null($smoker)) {
            $smoker = $smoker == 1;
        }
        $this->getEmployee()->setSmoker($smoker);
    }

    /**
     * @param int|null $id
     */
    public function setNationality(?int $id): void
    {
        $nationality = null;
        if (!is_null($id)) {
            /** @var Nationality|null $nationality */
            $nationality = $this->getReference(Nationality::class, $id);
        }
        $this->getEmployee()->setNationality($nationality);
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->getEmployee()->getEmployeeTerminationRecord() == null ?
            Employee::STATE_ACTIVE : Employee::STATE_TERMINATED;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        $locations = $this->getEmployee()->getLocations();
        if (empty($locations) || !isset($locations[0])) {
            return null;
        }
        return $locations[0];
    }

    /**
     * @param int|null $id
     */
    public function setLocationById(?int $id): void
    {
        $location = $this->getLocation();
        $locationId = $location instanceof Location ? $location->getId() : null;

        if (is_null($id)) {
            // Remove location
            $this->getEmployee()->setLocations([]);
        } elseif ($locationId !== $id) {
            // Changed location
            $this->getEmployee()->setLocations([]);

            $location = $this->getReference(Location::class, $id);
            if ($location) {
                $this->getEmployee()->setLocations([$location]);
            }
        } // else not changed location
    }

    /**
     * @param int|null $id
     */
    public function setJobTitleById(?int $id): void
    {
        /** @var JobTitle|null $jobTitle */
        $jobTitle = is_null($id) ? null : $this->getReference(JobTitle::class, $id);
        $this->getEmployee()->setJobTitle($jobTitle);
    }

    /**
     * @param int|null $id
     */
    public function setEmpStatusById(?int $id): void
    {
        /** @var EmploymentStatus|null $empStatus */
        $empStatus = is_null($id) ? null : $this->getReference(EmploymentStatus::class, $id);
        $this->getEmployee()->setEmpStatus($empStatus);
    }

    /**
     * @param int|null $id
     */
    public function setJobCategoryById(?int $id): void
    {
        /** @var JobCategory|null $jobCategory */
        $jobCategory = is_null($id) ? null : $this->getReference(JobCategory::class, $id);
        $this->getEmployee()->setJobCategory($jobCategory);
    }

    /**
     * @param int|null $id
     */
    public function setSubunitById(?int $id): void
    {
        /** @var Subunit|null $subunit */
        $subunit = is_null($id) ? null : $this->getReference(Subunit::class, $id);
        $this->getEmployee()->setSubDivision($subunit);
    }

    /**
     * @return string
     */
    public function getFirstAndLastNames(): string
    {
        return $this->getEmployee()->getFirstName() . ' ' . $this->getEmployee()->getLastName();
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return trim($this->getEmployee()->getFirstName()) . ' ' . trim($this->getEmployee()->getMiddleName()) . ' ' . trim($this->getEmployee()->getLastName());
    }
}
