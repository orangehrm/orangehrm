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

namespace OrangeHRM\Core\Authorization\Manager;

use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionCollection;
use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionFilterParams;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Entity\WorkflowStateMachine;

abstract class AbstractUserRoleManager
{
    /**
     * @var User|null
     */
    protected ?User $user = null;

    /**
     * @var UserRole[]
     */
    protected array $userRoles = [];

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->userRoles = $this->computeUserRoles($user);
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return UserRole[]
     */
    public function getUserRolesForAuthUser(): array
    {
        return $this->userRoles;
    }

    /**
     * @return bool
     */
    public function userHasNonPredefinedRole(): bool
    {
        $nonPredefined = false;

        foreach ($this->userRoles as $role) {
            if (!$role->isPredefined()) {
                $nonPredefined = true;
                break;
            }
        }

        return $nonPredefined;
    }

    /**
     * @param string $entityType
     * @param string|null $operation
     * @param string|null $returnType
     * @param array $rolesToExclude
     * @param array $rolesToInclude
     * @param array $requestedPermissions
     * @return array
     * @deprecated
     */
    abstract public function getAccessibleEntities(
        string $entityType,
        ?string $operation = null,
        ?string $returnType = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requestedPermissions = []
    ): array;

    /**
     * @param string $entityType
     * @param string|null $operation
     * @param null $returnType
     * @param string[] $rolesToExclude
     * @param string[] $rolesToInclude
     * @param array $requiredPermissions
     * @return int[]
     */
    abstract public function getAccessibleEntityIds(
        string $entityType,
        ?string $operation = null,
        $returnType = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): array;

    /**
     * @param string $entityType
     * @param string|int $entityId
     * @param string|null $operation
     * @param array $rolesToExclude
     * @param array $rolesToInclude
     * @param array $requiredPermissions
     * @return bool
     */
    abstract public function isEntityAccessible(
        string $entityType,
        $entityId,
        ?string $operation = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): bool;

    /**
     * @param string $entityType
     * @param array $entityIds
     * @param string|null $operation
     * @param array $rolesToExclude
     * @param array $rolesToInclude
     * @param array $requiredPermissions
     * @return bool
     */
    abstract public function areEntitiesAccessible(
        string $entityType,
        array $entityIds,
        ?string $operation = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): bool;

    /**
     * Get Properties of Accessible Entities
     *
     * @param string $entityType
     * @param array $properties Properties of the entity which should return
     * @param string|null $orderField
     * @param string|null $orderBy
     * @param array $rolesToExclude
     * @param array $rolesToInclude
     * @param array $requiredPermissions
     * @return array
     * @deprecated
     */
    abstract public function getAccessibleEntityProperties(
        string $entityType,
        array $properties = [],
        ?string $orderField = null,
        ?string $orderBy = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): array;

    /**
     * @return array
     */
    abstract public function getAccessibleModules(): array;

    /**
     * @param string $module
     * @return bool
     */
    abstract public function isModuleAccessible(string $module): bool;

    /**
     * @param string $module
     * @param string $screen
     * @param string $field
     * @return bool
     */
    abstract public function isScreenAccessible(string $module, string $screen, string $field): bool;

    /**
     * @param string $module
     * @param string $screen
     * @return ResourcePermission
     */
    abstract public function getScreenPermissions(string $module, string $screen): ResourcePermission;

    /**
     * @param string $apiClassName
     * @return ResourcePermission
     * @since 5.0
     */
    abstract public function getApiPermissions(string $apiClassName): ResourcePermission;

    /**
     * @param string $module
     * @param string $screen
     * @param string $field
     * @return bool
     */
    abstract public function isFieldAccessible(string $module, string $screen, string $field): bool;

    /**
     * @param string $roleName
     * @param array $entities
     * @return Employee[]
     */
    abstract public function getEmployeesWithRole(string $roleName, array $entities = []): array;

    /**
     * @param User $user
     * @return UserRole[]
     */
    abstract protected function computeUserRoles(User $user): array;

    /**
     * Check State Transition possible for User
     *
     * @param string $workFlowId
     * @param string $state
     * @param string $action
     * @param array $rolesToExclude
     * @param array $rolesToInclude
     * @param array $entities
     * @return bool
     */
    abstract public function isActionAllowed(
        string $workFlowId,
        string $state,
        string $action,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ): bool;

    /**
     * Get allowed Workflow action items for User
     *
     * @param string $workflow Workflow Name
     * @param string $state Workflow state
     * @param array $rolesToExclude
     * @param array $rolesToInclude
     * @param array $entities
     * @return WorkflowStateMachine[] Array of workflow items with action name as array index
     */
    abstract public function getAllowedActions(
        string $workflow,
        string $state,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ): array;

    /**
     * Given an array of actions, returns the states for which those actions can be applied
     * by the current logged in user
     *
     * @param string $workflow Workflow
     * @param array $actions Array of Action names
     * @param array $rolesToExclude
     * @param array $rolesToInclude
     * @param array $entities
     *
     * @return array Array of states
     */
    abstract public function getActionableStates(
        string $workflow,
        array $actions,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ): array;

    /**
     * get data group permissions - if permissions not defined, should return object with all rights set to false.
     * merge the permissions
     * return merged data group permission object.
     *
     * @param string[]|string $dataGroupName
     * @param array $rolesToExclude
     * @param array $rolesToInclude
     * @param bool $selfPermission
     * @param array $entities
     * @return ResourcePermission
     * @throws DaoException
     */
    abstract public function getDataGroupPermissions(
        $dataGroupName,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        bool $selfPermission = false,
        array $entities = []
    ): ResourcePermission;

    /**
     * @param DataGroupPermissionFilterParams|null $dataGroupPermissionFilterParams
     * @return DataGroupPermissionCollection
     * @since 5.0
     */
    abstract public function getDataGroupPermissionCollection(
        DataGroupPermissionFilterParams $dataGroupPermissionFilterParams = null
    ): DataGroupPermissionCollection;

    /**
     * @param string $module
     * @return string|null
     */
    abstract public function getModuleDefaultPage(string $module): ?string;

    /**
     * @return string|null
     */
    abstract public function getHomePage(): ?string;

    /**
     * @return bool
     */
    public function essRightsToOwnWorkflow(): bool
    {
        return true;
    }
}
