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

namespace OrangeHRM\Core\Authorization\UserRole;

use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Entity\Employee;

/**
 * Description of SupervisorUserRole
 *
 * @author Chameera Senarathna
 */
class SupervisorUserRole extends AbstractUserRole {

    public function getAccessibleEmployeeIds($operation = null, $returnType = null, $requiredPermissions = []) {

        $employeeIdArray = [];

        $empNumber = $this->getEmployeeNumber();
        if (!empty($empNumber)) {
            $employeeIdArray = $this->getEmployeeService()->getSubordinateIdListBySupervisorId($empNumber);
        }

        return $employeeIdArray;
    }

    public function getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions = []) {

        $employeeProperties = [];

        $empNumber = $this->getEmployeeNumber();
        if (!empty($empNumber)) {
            $employeeProperties = $this->getEmployeeService()->getSubordinatePropertyListBySupervisorId($empNumber, $properties, $orderField, $orderBy, true);
        }

        return $employeeProperties;
    }

    /**
     * @param null $operation
     * @param null $returnType
     * @param array $requiredPermissions
     * @return array|Employee[]
     */
    public function getAccessibleEmployees($operation = null, $returnType = null, $requiredPermissions = []): array {

        $employees = [];

        $empNumber = $this->getEmployeeNumber();
        if (!empty($empNumber)) {
            $employees = $this->getEmployeeService()->getSubordinateList($empNumber, true);
        }

        $employeesWithIds = [];

        foreach ($employees as $employee) {
            $employeesWithIds[$employee->getEmpNumber()] = $employee;
        }

        return $employeesWithIds;
    }

    public function getAccessibleLocationIds($operation = null, $returnType = null, $requiredPermissions = []) {

        // TODO
        $locationIds = [];
        
        if ($operation == BasicUserRoleManager::OPERATION_VIEW) {
            // Return locations of subordinates
            $empNumbers = $this->getAccessibleEmployeeIds();
            $locationIds = $this->getLocationService()->getLocationIdsForEmployees($empNumbers);
            
        }
        return $locationIds;
    }

    public function getAccessibleOperationalCountryIds($operation = null, $returnType = null, $requiredPermissions = []) {

        return [];
    }

    public function getAccessibleSystemUserIds($operation = null, $returnType = null, $requiredPermissions = []) {

        return [];
    }

    public function getAccessibleUserRoleIds($operation = null, $returnType = null, $requiredPermissions = []) {

        return [];
    }

}
