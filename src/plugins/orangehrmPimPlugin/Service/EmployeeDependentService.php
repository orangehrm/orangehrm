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

namespace OrangeHRM\Pim\Service;

use OrangeHRM\Pim\Dto\EmployeeDependentSearchFilterParams;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Entity\EmpDependent;
use OrangeHRM\Pim\Dao\EmployeeDependentDao;
use Exception;

class EmployeeDependentService
{
    /**
     * @var EmployeeDependentDao|null
     */
    protected ?EmployeeDependentDao $employeeDependentDao = null;

    /**
     * @return EmployeeDependentDao|null
     */
    public function getEmployeeDependentDao(): ?EmployeeDependentDao
    {
        if (!$this->employeeDependentDao instanceof EmployeeDependentDao) {
            $this->employeeDependentDao = new EmployeeDependentDao();
        }
        return $this->employeeDependentDao;
    }

    /**
     * @param EmployeeDependentDao|null $employeeDependentDao
     */
    public function setEmployeeDependentDao(?EmployeeDependentDao $employeeDependentDao): void
    {
        $this->employeeDependentDao = $employeeDependentDao;
    }

    /**
     * @param int $empNumber
     * @return EmpDependent[]
     */
    public function getEmployeeDependents(int $empNumber): array
    {
        return $this->getEmployeeDependentDao()->getEmployeeDependents($empNumber);
    }

    /**
     * @param int $empNumber
     * @param int $seqNo
     * @return EmpDependent|null
     */
    public function getEmployeeDependent(int $empNumber, int $seqNo): ?EmpDependent
    {
        return $this->getEmployeeDependentDao()->getEmployeeDependent($empNumber, $seqNo);
    }

    /**
     * @param EmpDependent $dependent
     * @return EmpDependent
     */
    public function saveEmployeeDependent(EmpDependent $dependent): EmpDependent
    {
        return $this->getEmployeeDependentDao()->saveEmployeeDependent($dependent);
    }

    /**
     * @param int $empNumber
     * @param array $entriesToDelete
     * @return int
     */
    public function deleteEmployeeDependents(int $empNumber, array $entriesToDelete): int
    {
        return $this->getEmployeeDependentDao()->deleteEmployeeDependents($empNumber, $entriesToDelete);
    }

    /**
     * @param EmployeeDependentSearchFilterParams $employeeDependentSearchParams
     * @return EmpDependent[]
     * @throws ServiceException
     */
    public function searchEmployeeDependent(EmployeeDependentSearchFilterParams $employeeDependentSearchParams): array
    {
        try {
            return $this->getEmployeeDependentDao()->searchEmployeeDependent($employeeDependentSearchParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeDependentSearchFilterParams $employeeDependentSearchParams
     * @return int
     * @throws ServiceException
     */
    public function getSearchEmployeeDependentsCount(
        EmployeeDependentSearchFilterParams $employeeDependentSearchParams
    ): int {
        try {
            return $this->getEmployeeDependentDao()->getSearchEmployeeDependentsCount($employeeDependentSearchParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
