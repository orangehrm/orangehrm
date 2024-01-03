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
use OrangeHRM\Entity\EmployeeLicense;
use OrangeHRM\Entity\License;

class EmployeeLicenseDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    /**
     * @var EmployeeLicense
     */
    protected EmployeeLicense $employeeLicense;

    /**
     * @param EmployeeLicense $employeeLicense
     */
    public function __construct(EmployeeLicense $employeeLicense)
    {
        $this->employeeLicense = $employeeLicense;
    }

    /**
     * @return EmployeeLicense
     */
    protected function getEmployeeLicense(): EmployeeLicense
    {
        return $this->employeeLicense;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getEmployeeLicense()->setEmployee($employee);
    }

    /**
     * @param int $licenseId
     */
    public function setLicenseByLicenseId(int $licenseId): void
    {
        /** @var License|null $license */
        $license = $this->getReference(License::class, $licenseId);
        $this->getEmployeeLicense()->setLicense($license);
    }

    /**
     * @return string|null
     */
    public function getLicenseIssuedDate(): ?string
    {
        $date = $this->getEmployeeLicense()->getLicenseIssuedDate();
        return $this->getDateTimeHelper()->formatDate($date);
    }

    /**
     * @return string|null
     */
    public function getLicenseExpiryDate(): ?string
    {
        $date = $this->getEmployeeLicense()->getLicenseExpiryDate();
        return $this->getDateTimeHelper()->formatDate($date);
    }
}
