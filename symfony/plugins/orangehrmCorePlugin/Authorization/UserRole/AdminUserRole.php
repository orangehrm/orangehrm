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
use OrangeHRM\Entity\Customer;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Location;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;
use OrangeHRM\Time\Traits\Service\CustomerServiceTrait;
use OrangeHRM\Time\Traits\Service\ProjectServiceTrait;

class AdminUserRole extends AbstractUserRole
{
    use EmployeeServiceTrait;
    use ProjectServiceTrait;
    use CustomerServiceTrait;

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
            case User::class:
                return $this->getAccessibleSystemUserIds($requiredPermissions);
            case UserRole::class:
                return $this->getAccessibleUserRoleIds($requiredPermissions);
            case Location::class:
                return $this->getAccessibleLocationIds($requiredPermissions);
            case Project::class:
                return $this->getAccessibleProjectIds($requiredPermissions);
            case Customer::class:
                return $this->getAccessibleCustomerIds($requiredPermissions);
            case 'Vacancy':
                // TODO:: implement and remove below line
                throw AuthorizationException::entityNotImplemented($entityType, __METHOD__);
                return $this->getAccessibleVacancyIds($requiredPermissions);
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
        return $this->getEmployeeService()->getEmployeeDao()->getEmpNumberList(false);
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleLocationIds(array $requiredPermissions = []): array
    {
        return $this->getLocationService()->getLocationDao()->getLocationsIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleSystemUserIds(array $requiredPermissions = []): array
    {
        return $this->getUserService()
            ->getSystemUserDao()
            ->getSystemUserIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleUserRoleIds(array $requiredPermissions = []): array
    {
        $userRoles = $this->getUserService()
            ->getSystemUserDao()
            ->getAssignableUserRoles();

        $ids = [];

        foreach ($userRoles as $role) {
            $ids[] = $role->getId();
        }

        return $ids;
    }

    /**
     * @param array $entities
     * @return Employee[]
     */
    public function getEmployeesWithRole(array $entities = []): array
    {
        return $this->getUserService()->getEmployeesByUserRole($this->roleName);
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleProjectIds(array $requiredPermissions = []): array
    {
        return $this->getProjectService()
            ->getProjectDao()
            ->getProjectIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleCustomerIds(array $requiredPermissions): array
    {
        return $this->getCustomerService()
            ->getCustomerDao()
            ->getCustomerIdList();
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessibleVacancyIds(array $requiredPermissions = []): array
    {
        return $this->getVacancyService()->getVacancyIdList();
    }
}
