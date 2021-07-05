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

use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\EmpDependent;
use OrangeHRM\Pim\Dao\EmployeeReportingMethodDao;
use OrangeHRM\Pim\Dao\EmployeeTerminationDao;
use OrangeHRM\Pim\Dto\EmployeeDependentSearchFilterParams;
use OrangeHRM\Pim\Dto\EmployeeSupervisorSearchFilterParams;
use OrangeHRM\Pim\Service\Model\TerminationReasonModel;
use Exception;

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
     * @param EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams
     * @return EmpDependent[]
     * @throws ServiceException
     */
    public function getImmediateSupervisorListForEmployee(EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams): array
    {
        try {
            return $this->getEmployeeReportingMethodDao()->searchImmediateEmployeeSupervisors($employeeSupervisorSearchFilterParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams
     * @return int
     * @throws ServiceException
     */
    public function getImmediateSupervisorListCountForEmployee(EmployeeSupervisorSearchFilterParams $employeeSupervisorSearchFilterParams
    ): int {
        try {
            return $this->getEmployeeReportingMethodDao()->getSearchImmediateEmployeeSupervisorsCount($employeeSupervisorSearchFilterParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
