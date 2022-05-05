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

namespace OrangeHRM\Performance\Service;

use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Performance\Dao\EmployeeTrackerDao;
use OrangeHRM\Performance\Dto\EmployeeTrackerSearchFilterParams;

class EmployeeTrackerService
{
    use AuthUserTrait;

    private ?EmployeeTrackerDao $employeeTrackerDao = null;

    /**
     * @return EmployeeTrackerDao
     */
    public function getEmployeeTrackerDao(): EmployeeTrackerDao
    {
        if (!$this->employeeTrackerDao instanceof EmployeeTrackerDao) {
            $this->employeeTrackerDao = new EmployeeTrackerDao();
        }
        return $this->employeeTrackerDao;
    }

    /**
     * @param EmployeeTrackerSearchFilterParams $employeeTrackerSearchFilterParams
     * @return PerformanceTracker[]
     */
    public function getEmployeeTrackerList(EmployeeTrackerSearchFilterParams $employeeTrackerSearchFilterParams): array
    {
        if ($this->getAuthUser()->getUserRoleId() !== 1) {
            $empNumber = $this->getAuthUser()->getEmpNumber();
            return $this->getEmployeeTrackerDao()->getEmployeeTrackerListForESS($employeeTrackerSearchFilterParams, $empNumber);
        }
        return $this->getEmployeeTrackerDao()->getEmployeeTrackerListForAdmin($employeeTrackerSearchFilterParams);
    }

    /**
     * @param EmployeeTrackerSearchFilterParams $employeeTrackerSearchFilterParams
     * @return int
     */
    public function getEmployeeTrackerCount(EmployeeTrackerSearchFilterParams $employeeTrackerSearchFilterParams): int
    {
        if ($this->getAuthUser()->getUserRoleId() !== 1) {
            $empNumber = $this->getAuthUser()->getEmpNumber();
            return $this->getEmployeeTrackerDao()->getEmployeeTrackerCountForESS($employeeTrackerSearchFilterParams, $empNumber);
        }
        return $this->getEmployeeTrackerDao()->getEmployeeTrackerCountForAdmin($employeeTrackerSearchFilterParams);
    }
}
