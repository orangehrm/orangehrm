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
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Pim\Dao\EmployeeReportingMethodDao;
use OrangeHRM\Pim\Dto\EmployeeSubordinateSearchFilterParams;
use OrangeHRM\Pim\Dto\EmployeeSupervisorSearchFilterParams;

class EmployeeReportingMethodService
{
    use NormalizerServiceTrait;

    /**
     * @var EmployeeReportingMethodDao|null
     */
    protected ?EmployeeReportingMethodDao $employeeReportingMethodDao = null;

    /**
     * @return EmployeeReportingMethodDao
     */
    public function getEmployeeReportingMethodDao(): EmployeeReportingMethodDao
    {
        if (!$this->employeeReportingMethodDao instanceof EmployeeReportingMethodDao) {
            $this->employeeReportingMethodDao = new EmployeeReportingMethodDao();
        }
        return $this->employeeReportingMethodDao;
    }

    /**
     * @param EmployeeReportingMethodDao|null $employeeReportingMethodDao
     */
    public function setEmployeeReportingMethodDao(?EmployeeReportingMethodDao $employeeReportingMethodDao): void
    {
        $this->employeeReportingMethodDao = $employeeReportingMethodDao;
    }

    /**
     * @param EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams
     * @return array
     * @throws DaoException
     */
    public function getImmediateSupervisorListForEmployee(EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams): array
    {
        return $this->getEmployeeReportingMethodDao()->searchImmediateEmployeeSupervisors($employeeSupervisorSearchFilterParams);
    }

    /**
     * @param EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getImmediateSupervisorListCountForEmployee(EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams): int
    {
        return $this->getEmployeeReportingMethodDao()->getSearchImmediateEmployeeSupervisorsCount($employeeSupervisorSearchFilterParams);
    }

    /**
     * @param EmployeeSubordinateSearchFilterParams $employeeSubordinateSearchFilterParams
     * @return array
     * @throws DaoException
     */
    public function getSubordinateListForEmployee(EmployeeSubordinateSearchFilterParams $employeeSubordinateSearchFilterParams): array
    {
        return $this->getEmployeeReportingMethodDao()->searchEmployeeSubordinates($employeeSubordinateSearchFilterParams);
    }

    /**
     * @param EmployeeSubordinateSearchFilterParams $employeeSubordinateSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getSubordinateListCountForEmployee(EmployeeSubordinateSearchFilterParams $employeeSubordinateSearchFilterParams): int
    {
        return $this->getEmployeeReportingMethodDao()->getSearchEmployeeSubordinatesCount($employeeSubordinateSearchFilterParams);
    }

    public function getAccessibleAndAvailableSupervisorsIdCombinedList(array $accessibleEmpNumbers, array $alreadyAssignedEmpNumbers): array
    {
        return array_values(array_diff($accessibleEmpNumbers, $alreadyAssignedEmpNumbers));
    }

    public function getAlreadyAssignedSupervisorsSubordinatesAndSelfIdCombinedList(array $supervisors, array $subordinates, int $empNumber): array
    {
        return array_merge($supervisors, $subordinates, [$empNumber]);
    }
}
