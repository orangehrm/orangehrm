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

namespace OrangeHRM\Pim\Service;

use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Dao\EmployeeDao;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;

class EmployeeService
{
    /**
     * @var EmployeeDao|null
     */
    protected ?EmployeeDao $employeeDao = null;

    /**
     * @return EmployeeDao
     */
    public function getEmployeeDao(): EmployeeDao
    {
        if (is_null($this->employeeDao)) {
            $this->employeeDao = new EmployeeDao();
        }
        return $this->employeeDao;
    }

    /**
     * @param EmployeeDao $employeeDao
     */
    public function setEmployeeDao(EmployeeDao $employeeDao): void
    {
        $this->employeeDao = $employeeDao;
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchParamHolder
     * @return array
     * @throws DaoException
     */
    public function getEmployeeList(EmployeeSearchFilterParams $employeeSearchParamHolder)
    {
        return $this->getEmployeeDao()->getEmployeeList($employeeSearchParamHolder);
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchParamHolder
     * @return int
     * @throws DaoException
     */
    public function getEmployeeCount(EmployeeSearchFilterParams $employeeSearchParamHolder): int
    {
        return $this->getEmployeeDao()->getEmployeeCount($employeeSearchParamHolder);
    }

    /**
     * @param Employee $employee
     * @return Employee
     * @throws DaoException
     */
    public function saveEmployee(Employee $employee): Employee
    {
        $employee = $this->getEmployeeDao()->saveEmployee($employee);
        return $employee;
    }

    /**
     * @param int $empNumber
     * @return Employee|null
     * @throws DaoException
     */
    public function getEmployeeByEmpNumber(int $empNumber): ?Employee
    {
        return $this->getEmployeeDao()->getEmployeeByEmpNumber($empNumber);
    }
}
