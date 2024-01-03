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

namespace OrangeHRM\Core\Authorization\UserRole;

use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Core\Authorization\Exception\AuthorizationException;
use OrangeHRM\Core\Authorization\Manager\AbstractUserRoleManager;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Project;

abstract class AbstractUserRole
{
    use AuthUserTrait;
    use UserServiceTrait;

    /**
     * @var AbstractUserRoleManager
     */
    protected AbstractUserRoleManager $userRoleManager;

    /**
     * @var string
     */
    protected string $roleName;

    /**
     * @param string $roleName
     * @param AbstractUserRoleManager $userRoleManager
     */
    final public function __construct(string $roleName, AbstractUserRoleManager $userRoleManager)
    {
        $this->userRoleManager = $userRoleManager;
        $this->roleName = $roleName;
    }

    /**
     * @return int|null
     */
    public function getEmployeeNumber(): ?int
    {
        return $this->getAuthUser()->getEmpNumber();
    }

    /**
     * @deprecated
     * @todo should remove
     */
    public function getAccessibleEntities($entityType, $operation = null, $returnType = null, $requiredPermissions = [])
    {
        // TODO
        throw AuthorizationException::methodNotImplemented(__METHOD__);

        $permitted = $this->areRequiredPermissionsAvailable($requiredPermissions);

        if ($permitted) {
            switch ($entityType) {
                case Employee::class:
                    $entities = $this->getAccessibleEmployees($operation, $returnType, $requiredPermissions);
                    break;
                case Project::class:
                    // TODO:: implement and remove below line
                    throw AuthorizationException::entityNotImplemented($entityType, __METHOD__);
                    $entities = $this->getAccessibleProjects($operation, $returnType, $requiredPermissions);
                    break;
                case 'Vacancy':
                    // TODO:: implement and remove below line
                    throw AuthorizationException::entityNotImplemented($entityType, __METHOD__);
                    $entities = $this->getAccessibleVacancies($operation, $returnType, $requiredPermissions);
                    break;
                default:
                    throw AuthorizationException::entityNotSupported($entityType, __METHOD__);
            }
        } else {
            $entities = [];
        }
        return $entities;
    }

    /**
     * @deprecated
     * @todo should remove
     */
    public function getAccessibleEntityProperties(
        $entityType,
        $properties = [],
        $orderField = null,
        $orderBy = null,
        $requiredPermissions = []
    ) {
        // TODO
        throw AuthorizationException::methodNotImplemented(__METHOD__);

        $permitted = $this->areRequiredPermissionsAvailable($requiredPermissions);
        if ($permitted) {
            switch ($entityType) {
                case Employee::class:
                    $propertyList = $this->getAccessibleEmployeePropertyList(
                        $properties,
                        $orderField,
                        $orderBy,
                        $requiredPermissions
                    );
                    break;
                default:
                    throw AuthorizationException::entityNotSupported($entityType, __METHOD__);
            }
        } else {
            $propertyList = [];
        }
        return $propertyList;
    }

    /**
     * @param string $entityType
     * @param string|null $operation
     * @param null $returnType
     * @param array $requiredPermissions
     * @return int[]
     */
    public function getAccessibleEntityIds(
        string $entityType,
        ?string $operation = null,
        $returnType = null,
        array $requiredPermissions = []
    ): array {
        $permitted = $this->areRequiredPermissionsAvailable($requiredPermissions);
        $ids = [];
        if ($permitted) {
            $ids = $this->getAccessibleIdsForEntity($entityType, $requiredPermissions);
        }
        return $ids;
    }

    /**
     * @param string $entityType
     * @param array $requiredPermissions
     * @return int[]
     */
    abstract protected function getAccessibleIdsForEntity(
        string $entityType,
        array $requiredPermissions = []
    ): array;

    /**
     * @param array $entities
     * @return Employee[]
     */
    public function getEmployeesWithRole(array $entities = []): array
    {
        return [];
    }

    /**
     * @deprecated
     * @todo should remove
     */
    public function getAccessibleProjects($operation = null, $returnType = null, $requiredPermissions = [])
    {
        return [];
    }

    /**
     * @deprecated
     * @todo should remove
     */
    public function getAccessibleVacancies($operation = null, $returnType = null, $requiredPermissions = [])
    {
        return [];
    }

    /**
     * @param array $requiredPermissions
     * @return bool
     */
    protected function areRequiredPermissionsAvailable(array $requiredPermissions = []): bool
    {
        $permitted = true;

        foreach ($requiredPermissions as $permissionType => $permissions) {
            if ($permissionType == BasicUserRoleManager::PERMISSION_TYPE_DATA_GROUP) {
                foreach ($permissions as $dataGroupName => $requestedResourcePermission) {
                    $dataGroupPermissions = $this->userRoleManager->getDataGroupPermissions(
                        $dataGroupName,
                        [],
                        [$this->roleName]
                    );

                    if ($permitted && $requestedResourcePermission->canRead()) {
                        $permitted = $permitted && $dataGroupPermissions->canRead();
                    }

                    if ($permitted && $requestedResourcePermission->canCreate()) {
                        $permitted = $dataGroupPermissions->canCreate();
                    }

                    if ($permitted && $requestedResourcePermission->canUpdate()) {
                        $permitted = $dataGroupPermissions->canUpdate();
                    }

                    if ($permitted && $requestedResourcePermission->canDelete()) {
                        $permitted = $dataGroupPermissions->canDelete();
                    }
                }
            } elseif ($permissionType == BasicUserRoleManager::PERMISSION_TYPE_ACTION) {
                $permitted = true;
            }
        }

        return $permitted;
    }

    /**
     * @param array $requiredPermissions
     * @return string[]
     */
    public function getAccessibleQuickLaunchList(array $requiredPermissions): array
    {
        return [];
    }
}
