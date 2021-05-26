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

use DateTime;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\EmpDependent;
use OrangeHRM\Entity\Employee;

class EmpDependentDecorator
{
    use EntityManagerHelperTrait;

    /**
     * @var EmpDependent
     */
    protected EmpDependent $empDependent;

    /**
     * @param EmpDependent $employee
     */
    public function __construct(EmpDependent $employee)
    {
        $this->empDependent = $employee;
    }

    /**
     * @return EmpDependent
     */
    protected function getEmpDependent(): EmpDependent
    {
        return $this->empDependent;
    }

    /**
     * @return string|null
     */
    public function getDateOfBirth(): ?string
    {
        $date = $this->getEmpDependent()->getDateOfBirth();
        return $date ? $date->format('Y-m-d') : null;
    }

    /**
     * @param string|null $dateOfBirth Y-m-d format
     */
    public function setDateOfBirth(?string $dateOfBirth): void
    {
        if (!is_null($dateOfBirth)) {
            $dateOfBirth = new DateTime($dateOfBirth);
        }
        $this->getEmpDependent()->setDateOfBirth($dateOfBirth);
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getEmpDependent()->setEmployee($employee);
    }
}
