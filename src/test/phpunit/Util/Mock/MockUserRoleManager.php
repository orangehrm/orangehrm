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

namespace OrangeHRM\Tests\Util\Mock;

use Exception;
use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionCollection;
use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionFilterParams;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Authorization\Manager\AbstractUserRoleManager;
use OrangeHRM\Entity\User;

class MockUserRoleManager extends AbstractUserRoleManager
{
    /**
     * @inheritDoc
     */
    public function getAccessibleEntities(
        string $entityType,
        ?string $operation = null,
        ?string $returnType = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requestedPermissions = []
    ): array {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getAccessibleEntityIds(
        string $entityType,
        ?string $operation = null,
        $returnType = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): array {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function isEntityAccessible(
        string $entityType,
        $entityId,
        ?string $operation = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): bool {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function areEntitiesAccessible(
        string $entityType,
        array $entityIds,
        ?string $operation = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): bool {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getAccessibleEntityProperties(
        string $entityType,
        array $properties = [],
        ?string $orderField = null,
        ?string $orderBy = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): array {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getAccessibleModules(): array
    {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function isModuleAccessible(string $module): bool
    {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function isScreenAccessible(string $module, string $screen, string $field): bool
    {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getScreenPermissions(string $module, string $screen): ResourcePermission
    {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getApiPermissions(string $apiClassName): ResourcePermission
    {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function isFieldAccessible(string $module, string $screen, string $field): bool
    {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getEmployeesWithRole(string $roleName, array $entities = []): array
    {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    protected function computeUserRoles(User $user): array
    {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function isActionAllowed(
        string $workFlowId,
        string $state,
        string $action,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ): bool {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getAllowedActions(
        string $workflow,
        string $state,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ): array {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getActionableStates(
        string $workflow,
        array $actions,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ): array {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getDataGroupPermissions(
        $dataGroupName,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        bool $selfPermission = false,
        array $entities = []
    ): ResourcePermission {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getDataGroupPermissionCollection(
        ?DataGroupPermissionFilterParams $dataGroupPermissionFilterParams = null
    ): DataGroupPermissionCollection {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getModuleDefaultPage(string $module): ?string
    {
        throw $this->getException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getHomePage(): ?string
    {
        throw $this->getException(__METHOD__);
    }

    /**
     * @param string $method
     * @return Exception
     */
    private function getException(string $method): Exception
    {
        return new Exception("This $method should not call. Hint: Mock this method");
    }
}
