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

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Education;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeEducation;

class EmployeeEducationDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    /**
     * @var EmployeeEducation
     */
    protected EmployeeEducation $employeeEducation;

    /**
     * @param EmployeeEducation $employeeEducation
     */
    public function __construct(EmployeeEducation $employeeEducation)
    {
        $this->employeeEducation = $employeeEducation;
    }

    /**
     * @return EmployeeEducation
     */
    protected function getEmployeeEducation(): EmployeeEducation
    {
        return $this->employeeEducation;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getEmployeeEducation()->setEmployee($employee);
    }

    /**
     * @param int $educationId
     */
    public function setEducationByEducationId(int $educationId): void
    {
        /** @var Education|null $education */
        $education = $this->getReference(Education::class, $educationId);
        $this->getEmployeeEducation()->setEducation($education);
    }

    /**
     * @return string|null
     */
    public function getStartDate(): ?string
    {
        $date = $this->getEmployeeEducation()->getStartDate();
        return $this->getDateTimeHelper()->formatDateTimeToYmd($date);
    }

    /**
     * @return string|null
     */
    public function getEndDate(): ?string
    {
        $date = $this->getEmployeeEducation()->getEndDate();
        return $this->getDateTimeHelper()->formatDateTimeToYmd($date);
    }
}
