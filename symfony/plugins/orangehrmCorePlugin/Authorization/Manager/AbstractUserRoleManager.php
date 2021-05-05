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

use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;

abstract class AbstractUserRoleManager
{

    protected ?User $user = null;
    protected $userRoles = [];

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->userRoles = $this->getUserRoles($user);
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function userHasNonPredefinedRole(): bool
    {
        $nonPredefined = false;

        foreach ($this->userRoles as $role) {
            if (!$role->getIsPredefined()) {
                $nonPredefined = true;
                break;
            }
        }

        return $nonPredefined;
    }

    abstract public function getAccessibleEntities(
        $entityType,
        $operation = null,
        $returnType = null,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $requestedPermissions = []
    );

    abstract public function getAccessibleEntityIds(
        $entityType,
        $operation = null,
        $returnType = null,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $requiredPermissions = []
    );

    abstract public function isEntityAccessible(
        $entityType,
        $entityId,
        $operation = null,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $requiredPermissions = []
    );

    abstract public function areEntitiesAccessible(
        $entityType,
        $entityIds,
        $operation = null,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $requiredPermissions = []
    );

    abstract public function getAccessibleEntityProperties(
        $entityType,
        $properties = [],
        $orderField = null,
        $orderBy = null,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $requiredPermissions = []
    );

    abstract public function getAccessibleModules();

    abstract public function getAccessibleMenuItemDetails(): array;

    abstract public function isModuleAccessible($module);

    abstract public function isScreenAccessible($module, $screen, $field);

    /**
     * @param string $module
     * @param string $screen
     * @return ResourcePermission
     */
    abstract public function getScreenPermissions(string $module, string $screen): ResourcePermission;

    abstract public function isFieldAccessible($module, $screen, $field);

    abstract public function getEmployeesWithRole($roleName, $entities = []);

    /**
     * @param User $user
     * @return UserRole[]
     */
    abstract protected function getUserRoles(User $user): array;

    abstract protected function isActionAllowed(
        $workFlowId,
        $state,
        $action,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $entities = []
    );

    abstract protected function getAllowedActions(
        $workFlowId,
        $state,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $entities = []
    );

    abstract public function getActionableStates(
        $workflow,
        $actions,
        $rolesToExclude = [],
        $rolesToInclude = [],
        $entities = []
    );

    abstract public function getModuleDefaultPage(string $module);

    abstract public function getHomePage(): ?string;

    public function essRightsToOwnWorkflow(): bool
    {
        return true;
    }
}

