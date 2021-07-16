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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\EmployeeImmigrationRecord;
use OrangeHRM\Entity\Employee;

class EmployeeImmigrationRecordDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    /**
     * @var EmployeeImmigrationRecord
     */
    protected EmployeeImmigrationRecord $employeeImmigrationRecord;

    /**
     * @var CountryService
     */
    protected CountryService $countryService;

    /**
     * EmployeeImmigrationRecordDecorator constructor.
     * @param EmployeeImmigrationRecord $employeeImmigrationRecord
     */
    public function __construct(EmployeeImmigrationRecord $employeeImmigrationRecord)
    {
        $this->employeeImmigrationRecord = $employeeImmigrationRecord;
    }

    /**
     * @return EmployeeImmigrationRecord
     */
    protected function getEmployeeImmigrationRecord(): EmployeeImmigrationRecord
    {
        return $this->employeeImmigrationRecord;
    }

    /**
     * @return CountryService
     */
    public function getCountryService(): CountryService
    {
        if (is_null($this->countryService)) {
            $this->countryService = new CountryService();
        }
        return $this->countryService;
    }

    /**
     * @param $countryService
     */
    public function setCountryService($countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getEmployeeImmigrationRecord()->setEmployee($employee);
    }

    /**
     * @return string|null
     */
    public function getIssuedDate(): ?string
    {
        $date = $this->getEmployeeImmigrationRecord()->getIssuedDate();
        return $this->getDateTimeHelper()->formatDateTimeToYmd($date);
    }

    /**
     * @return string|null
     */
    public function getExpiryDate(): ?string
    {
        $date = $this->getEmployeeImmigrationRecord()->getExpiryDate();
        return $this->getDateTimeHelper()->formatDateTimeToYmd($date);
    }

    /**
     * @return string|null
     */
    public function getReviewDate(): ?string
    {
        $date = $this->getEmployeeImmigrationRecord()->getReviewDate();
        return $this->getDateTimeHelper()->formatDateTimeToYmd($date);
    }

    public function getCountryName()
    {
        $countryCode = $this->getEmployeeImmigrationRecord()->getCountryCode();
        $countryService = new CountryService();
        $country = $countryService->getCountryByCountryCode($countryCode);
        return $country->getCountryName();
    }
}
