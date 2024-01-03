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

namespace OrangeHRM\LDAP\Dto;

use OrangeHRM\Entity\Employee;

class PartialEmployee
{
    private ?string $employeeId = null;
    private ?string $workEmail = null;

    /**
     * @return string|null
     */
    public function getEmployeeId(): ?string
    {
        return $this->employeeId;
    }

    /**
     * @param string|null $employeeId
     * @return PartialEmployee
     */
    public function setEmployeeId(?string $employeeId): PartialEmployee
    {
        $this->employeeId = $employeeId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getWorkEmail(): ?string
    {
        return $this->workEmail;
    }

    /**
     * @param string|null $workEmail
     * @return PartialEmployee
     */
    public function setWorkEmail(?string $workEmail): PartialEmployee
    {
        $this->workEmail = $workEmail;
        return $this;
    }

    /**
     * @param Employee $employee
     * @return static
     */
    public static function createFromEmployee(Employee $employee): self
    {
        return (new self())
            ->setEmployeeId($employee->getEmployeeId())
            ->setWorkEmail($employee->getWorkEmail());
    }
}
