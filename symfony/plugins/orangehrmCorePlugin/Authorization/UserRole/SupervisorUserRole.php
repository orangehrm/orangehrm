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

use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Core\Authorization\Exception\AuthorizationException;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Location;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class SupervisorUserRole extends AbstractUserRole
{
    use EmployeeServiceTrait;

    protected ?LocationService $locationService = null;

    /**
     * @return LocationService
     */
    protected function getLocationService(): LocationService
    {
        if (!$this->locationService instanceof LocationService) {
            $this->locationService = new LocationService();
        }
        return $this->locationService;
    }

    /**
     * @inheritDoc
     */
    protected function getAccessibleIdsForEntity(string $entityType, array $requiredPermissions = []): array
    {
        switch ($entityType) {
            case Employee::class:
                return $this->getAccessibleEmployeeIds($requiredPermissions);
            case Location::class:
                // TODO:: implement and remove below line
                throw AuthorizationException::entityNotImplemented($entityType, __METHOD__);
                return $this->getAccessibleLocationIds($requiredPermissions);
            default:
                return [];
        }
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleEmployeeIds(array $requiredPermissions = []): array
    {
        $empNumbers = [];
        $empNumber = $this->getEmployeeNumber();
        if (!empty($empNumber)) {
            $empNumbers = $this->getEmployeeService()->getSubordinateIdListBySupervisorId($empNumber);
        }
        return $empNumbers;
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     * @todo
     */
    protected function getAccessibleLocationIds(array $requiredPermissions = []): array
    {
        $locationIds = [];

        if ($operation == BasicUserRoleManager::OPERATION_VIEW) {
            // Return locations of subordinates
            $empNumbers = $this->getAccessibleEmployeeIds();
            $locationIds = $this->getLocationService()->getLocationIdsForEmployees($empNumbers);
        }
        return $locationIds;
    }
}
