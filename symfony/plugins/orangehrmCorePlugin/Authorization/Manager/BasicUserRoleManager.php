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

use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Core\Authorization\Dao\HomePageDao;
use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionCollection;
use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionFilterParams;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Authorization\Exception\AuthorizationException;
use OrangeHRM\Core\Authorization\Service\DataGroupService;
use OrangeHRM\Core\Authorization\Service\ScreenPermissionService;
use OrangeHRM\Core\Authorization\UserRole\AbstractUserRole;
use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\HomePage\HomePageEnablerInterface;
use OrangeHRM\Core\Service\AccessFlowStateMachineService;
use OrangeHRM\Core\Service\MenuService;
use OrangeHRM\Core\Traits\ClassHelperTrait;
use OrangeHRM\Core\Traits\Service\MenuServiceTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;
use OrangeHRM\Time\Traits\Service\ProjectServiceTrait;

class BasicUserRoleManager extends AbstractUserRoleManager
{
    use EmployeeServiceTrait;
    use ClassHelperTrait;
    use ProjectServiceTrait;
    use UserServiceTrait;
    use MenuServiceTrait;

    public const PERMISSION_TYPE_DATA_GROUP = 'data_group';
    public const PERMISSION_TYPE_ACTION = 'action';
    public const PERMISSION_TYPE_WORKFLOW_ACTION = 'workflow_action';
    public const PERMISSION_TYPE_USER_ROLE_SPECIFIC = 'user_role_specific';

    public const OPERATION_VIEW = 'view';
    public const OPERATION_EDIT = 'edit';
    public const OPERATION_DELETE = 'delete';

    protected ?ScreenPermissionService $screenPermissionService = null;
    protected ?DataGroupService $dataGroupService = null;
    protected $subordinates = null;
    protected ?MenuService $menuService = null;
    protected ?HomePageDao $homePageDao = null;
    protected ?AccessFlowStateMachineService $accessFlowStateMachineService = null;

    /**
     * @var AbstractUserRole[]
     */
    protected array $userRoleClasses = [];

    public function __construct()
    {
        $this->_init();
    }

    private function _init(): void
    {
        // TODO:: move to yaml or database
        $configurations = [
            'Admin' => [
                'class' => 'AdminUserRole',
            ],
            'Supervisor' => [
                'class' => 'SupervisorUserRole',
            ],
            'ESS' => [
                'class' => 'EssUserRole',
            ],
            'ProjectAdmin' => [
                'class' => 'ProjectAdminUserRole',
            ],
            'HiringManager' => [
                'class' => 'HiringManagerUserRole',
            ],
            'Interviewer' => [
                'class' => 'InterviewerUserRole',
            ],
        ];

        foreach ($configurations as $roleName => $roleObj) {
            $className = $this->getClassHelper()->getClass($roleObj['class'], 'OrangeHRM\\Core\\Authorization\\UserRole\\');
            $this->userRoleClasses[$roleName] = new $className($roleName, $this);
        }
    }

    /**
     * @param string $roleName
     * @return AbstractUserRole|null
     */
    protected function getUserRoleClass(string $roleName): ?AbstractUserRole
    {
        return $this->userRoleClasses[$roleName] ?? null;
    }

    /**
     * @return ScreenPermissionService
     */
    protected function getScreenPermissionService(): ScreenPermissionService
    {
        if (!$this->screenPermissionService instanceof ScreenPermissionService) {
            $this->screenPermissionService = new ScreenPermissionService();
        }
        return $this->screenPermissionService;
    }

    /**
     * @param ScreenPermissionService $screenPermissionService
     */
    public function setScreenPermissionService(ScreenPermissionService $screenPermissionService): void
    {
        $this->screenPermissionService = $screenPermissionService;
    }

    /**
     * @return HomePageDao
     */
    protected function getHomePageDao(): HomePageDao
    {
        if (!$this->homePageDao instanceof HomePageDao) {
            $this->homePageDao = new HomePageDao();
        }
        return $this->homePageDao;
    }

    /**
     * @param HomePageDao $homePageDao
     */
    public function setHomePageDao(HomePageDao $homePageDao): void
    {
        $this->homePageDao = $homePageDao;
    }

    /**
     * @return AccessFlowStateMachineService
     */
    protected function getAccessFlowStateMachineService(): AccessFlowStateMachineService
    {
        if (!$this->accessFlowStateMachineService instanceof AccessFlowStateMachineService) {
            $this->accessFlowStateMachineService = new AccessFlowStateMachineService();
        }
        return $this->accessFlowStateMachineService;
    }

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
        // TODO
        throw AuthorizationException::methodNotImplemented(__METHOD__);
        $allEmployees = [];

        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude);

        foreach ($filteredRoles as $role) {
            $employees = [];

            $roleClass = $this->getUserRoleClass($role->getName());

            if ($roleClass) {
                $employees = $roleClass->getAccessibleEntities(
                    $entityType,
                    $operation,
                    $returnType,
                    $requestedPermissions
                );
            }

            if (count($employees) > 0) {
                $allEmployees = $this->mergeEmployees($allEmployees, $employees);
            }
        }

        return $allEmployees;
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
        // TODO
        throw AuthorizationException::methodNotImplemented(__METHOD__);

        $allPropertyList = [];
        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude);

        foreach ($filteredRoles as $role) {
            $propertyList = [];

            $roleClass = $this->getUserRoleClass($role->getName());

            if ($roleClass) {
                $propertyList = $roleClass->getAccessibleEntityProperties(
                    $entityType,
                    $properties,
                    $orderField,
                    $orderBy,
                    $requiredPermissions
                );
            }

            if (count($propertyList) > 0) {
                foreach ($propertyList as $property) {
                    $allPropertyList[$property['empNumber']] = $property;
                }
            }
        }

        return $allPropertyList;
    }

    /**
     * TODO: 'locations', 'system users', 'operational countries',
     *       'user role' (only ess for regional admin),
     *
     * @param string $entityType
     * @param string|null $operation
     * @param null $returnType
     * @param string[] $rolesToExclude
     * @param string[] $rolesToInclude
     * @param array $requiredPermissions
     * @return int[]
     */
    public function getAccessibleEntityIds(
        string $entityType,
        ?string $operation = null,
        $returnType = null,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $requiredPermissions = []
    ): array {
        // TODO
        $allIds = [];
        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude);

        foreach ($filteredRoles as $role) {
            $ids = [];

            $roleClass = $this->getUserRoleClass($role->getName());

            if ($roleClass) {
                $ids = $roleClass->getAccessibleEntityIds($entityType, $operation, $returnType, $requiredPermissions);
            }

            if (count($ids) > 0) {
                $allIds = array_unique(array_merge($allIds, $ids));
            }
        }

        return $allIds;
    }

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
    public function isActionAllowed(
        string $workFlowId,
        string $state,
        string $action,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ): bool {
        $isAllowed = false;

        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude, $entities);

        foreach ($filteredRoles as $role) {
            $roleName = $this->fixUserRoleNameForWorkflowStateMachine($role->getName(), $workFlowId);

            $isAllowed = $this->getAccessFlowStateMachineService()->isActionAllowed(
                $workFlowId,
                $state,
                $roleName,
                $action
            );
            if ($isAllowed) {
                break;
            }
        }
        return $isAllowed;
    }

    /**
     * Get allowed Workflow action items for User
     *
     * @param string $workflow Workflow Name
     * @param string $state Workflow state
     * @param array $rolesToExclude
     * @param array $rolesToInclude
     * @param array $entities
     * @return array|WorkflowStateMachine[] Array of workflow items with action name as array index
     */
    public function getAllowedActions(
        string $workflow,
        string $state,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ): array {
        $allActions = [];

        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude, $entities);

        foreach ($filteredRoles as $role) {
            $roleName = $this->fixUserRoleNameForWorkflowStateMachine($role->getName(), $workflow);
            $workFlowItems = $this->getAccessFlowStateMachineService()->getAllowedWorkflowItems(
                $workflow,
                $state,
                $roleName
            );

            if (count($workFlowItems) > 0) {
                $allActions = $this->getUniqueActionsBasedOnPriority($allActions, $workFlowItems);
            }
        }
        return $allActions;
    }

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
    public function getActionableStates(
        string $workflow,
        array $actions,
        array $rolesToExclude = [],
        array $rolesToInclude = [],
        array $entities = []
    ): array {
        $actionableStates = [];

        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude, $entities);

        foreach ($filteredRoles as $role) {
            $roleName = $this->fixUserRoleNameForWorkflowStateMachine($role->getName(), $workflow);
            $states = $this->getAccessFlowStateMachineService()->getActionableStates($workflow, $roleName, $actions);

            if (!empty($states)) {
                $actionableStates = array_unique(array_merge($actionableStates, $states));
            }
        }
        return $actionableStates;
    }

    /**
     * @param WorkflowStateMachine[] $currentItems
     * @param WorkflowStateMachine[] $itemsToMerge
     * @return WorkflowStateMachine[]
     */
    protected function getUniqueActionsBasedOnPriority(array $currentItems, array $itemsToMerge): array
    {
        foreach ($itemsToMerge as $item) {
            $actionName = $item->getAction();
            if (!isset($currentItems[$actionName])) {
                $currentItems[$actionName] = $item;
            } else {
                $existing = $currentItems[$actionName];

                if ($item->getPriority() > $existing->getPriority()) {
                    $currentItems[$actionName] = $item;
                }
            }
        }

        return $currentItems;
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
        // TODO
        $entityIds = $this->getAccessibleEntityIds(
            $entityType,
            $operation,
            null,
            $rolesToExclude,
            $rolesToInclude,
            $requiredPermissions
        );

        $accessible = in_array($entityId, $entityIds);

        return $accessible;
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
        // TODO
        $accessibleIds = $this->getAccessibleEntityIds(
            $entityType,
            $operation,
            null,
            $rolesToExclude,
            $rolesToInclude,
            $requiredPermissions
        );

        $intersection = array_intersect($accessibleIds, $entityIds);

        $accessible = false;

        if (count($entityIds) == count($intersection)) {
            $diff = array_diff($entityIds, $intersection);
            if (count($diff) == 0) {
                $accessible = true;
            }
        }

        return $accessible;
    }

    /**
     * @inheritDoc
     */
    public function getEmployeesWithRole(string $roleName, array $entities = []): array
    {
        $employees = [];
        $roleClass = $this->getUserRoleClass($roleName);
        if (!empty($roleClass)) {
            $employees = $roleClass->getEmployeesWithRole($entities);
        }

        return $employees;
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function getAccessibleModules(): array
    {
        throw AuthorizationException::methodNotImplemented(__METHOD__);
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function isModuleAccessible(string $module): bool
    {
        throw AuthorizationException::methodNotImplemented(__METHOD__);
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function isScreenAccessible(string $module, string $screen, string $field): bool
    {
        throw AuthorizationException::methodNotImplemented(__METHOD__);
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function isFieldAccessible(string $module, string $screen, string $field): bool
    {
        throw AuthorizationException::methodNotImplemented(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getScreenPermissions(string $module, string $screen): ResourcePermission
    {
        return $this->getScreenPermissionService()->getScreenPermissions($module, $screen, $this->userRoles);
    }

    /**
     * @inheritDoc
     */
    public function getApiPermissions(string $apiClassName): ResourcePermission
    {
        return $this->getDataGroupService()->getApiPermissions($apiClassName, $this->userRoles);
    }

    /**
     * @inheritDoc
     */
    protected function computeUserRoles(User $user): array
    {
        $roles = [$user->getUserRole()];

        // Check for supervisor:
        $empNumber = $user->getEmpNumber();
        if (!empty($empNumber)) {
            if ($user->getUserRole()->getName() != 'ESS') {
                $roles[] = $this->getUserService()->getUserRole('ESS');
            }

            if ($this->isProjectAdmin($empNumber)) {
                $roles[] = $this->getUserService()->getUserRole('ProjectAdmin');
            }

            if ($this->isHiringManager($empNumber)) {
                $roles[] = $this->getUserService()->getUserRole('HiringManager');
            }

            if ($this->isInterviewer($empNumber)) {
                $roles[] = $this->getUserService()->getUserRole('Interviewer');
            }

            if ($this->getEmployeeService()->isSupervisor($empNumber)) {
                $supervisorRole = $this->getUserService()->getUserRole('Supervisor');
                if (!empty($supervisorRole)) {
                    $roles[] = $supervisorRole;
                }
            }
        }

        return $roles;
    }

    /**
     * @param UserRole $role
     * @param array $requiredPermissions
     * @return bool
     * @throws DaoException
     */
    protected function areRequiredPermissionsAvailable(UserRole $role, array $requiredPermissions = []): bool
    {
        // TODO
        $permitted = true;

        foreach ($requiredPermissions as $permissionType => $permissions) {
            if ($permissionType == self::PERMISSION_TYPE_DATA_GROUP) {
                foreach ($permissions as $dataGroupName => $requestedResourcePermission) {
                    $dataGroupPermissions = $this->getDataGroupPermissions($dataGroupName, [], [$role->getName()]);

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
            }
        }

        return $permitted;
    }

    protected function mergeEmployees($empList1, $empList2)
    {
        // TODO
        foreach ($empList2 as $id => $emp) {
            if (!isset($empList1[$id])) {
                $empList1[$id] = $emp;
            }
        }
        return $empList1;
    }

    /**
     * Filter the given $userRoles array according to the given parameters
     *
     * @param UserRole[] $userRoles Array of UserRole objects
     * @param string[] $rolesToExclude Array of User role names to exclude. These user roles will be removed from $userRoles
     * @param string[] $rolesToInclude Array of User role names to include. If not empty, only these user roles will be included.
     * @param array $entities Array of details relevant to deciding if a particular user role applies to this
     * @return UserRole[] $userRoles array filtered as described above.
     */
    protected function filterRoles(
        array $userRoles,
        array $rolesToExclude,
        array $rolesToInclude,
        array $entities = []
    ): array {
        if (!empty($rolesToExclude)) {
            $temp = [];

            foreach ($userRoles as $role) {
                if (!in_array($role->getName(), $rolesToExclude)) {
                    $temp[] = $role;
                }
            }

            $userRoles = $temp;
        }

        if (!empty($rolesToInclude)) {
            $temp = [];

            foreach ($userRoles as $role) {
                if (in_array($role->getName(), $rolesToInclude)) {
                    $temp[] = $role;
                }
            }

            $userRoles = $temp;
        }

        $temp = [];

        if (!empty($entities)) {
            foreach ($userRoles as $role) {
                $include = true;

                if ($role->getName() == 'Supervisor') {
                    // If Employee entity is given, supervisor role will only
                    // apply if current employee is the supervisor for the given employee
                    if (isset($entities[Employee::class])) {
                        if (!$this->isSupervisorFor($entities[Employee::class])) {
                            $include = false;
                        }
                    }
                } elseif ($role->getName() == 'ESS') {
                    // If Employee entity is given, the ESS role will only apply
                    // If current logged in employee is the same as the passed entity.
                    if (isset($entities[Employee::class])) {
                        if ($this->getUser()->getEmpNumber() != $entities[Employee::class]) {
                            $include = false;
                        }
                    }
                }

                if ($include) {
                    $temp[] = $role;
                }
            }

            $userRoles = $temp;
        }

        return $userRoles;
    }

    /**
     * @param int $empNumber
     * @return bool
     * @throws CoreServiceException
     */
    protected function isSupervisorFor(int $empNumber): bool
    {
        $supervisorId = $this->getUser()->getEmpNumber();
        if (is_null($this->subordinates) && !is_null($supervisorId)) {
            $this->subordinates = $this->getEmployeeService()->getSubordinateIdListBySupervisorId($supervisorId);
        }

        if (is_array($this->subordinates) && in_array($empNumber, $this->subordinates)) {
            return true;
        }

        return false;
    }

    /**
     * @param int|null $empNumber
     * @return bool
     */
    protected function isProjectAdmin(?int $empNumber): bool
    {
        return $this->getProjectService()
            ->getProjectDao()
            ->isProjectAdmin($empNumber);
    }

    private function isHiringManager($empNumber)
    {
        // TODO:: should remove this return
        return false;
        // TODO
        return $this->getVacancyService()->isHiringManager($empNumber);
    }

    private function isInterviewer($empNumber)
    {
        // TODO:: should remove this return
        return false;
        // TODO
        return $this->getVacancyService()->isInterviewer($empNumber);
    }

    /**
     * @return DataGroupService
     */
    protected function getDataGroupService(): DataGroupService
    {
        if (!$this->dataGroupService instanceof DataGroupService) {
            $this->dataGroupService = new DataGroupService();
        }
        return $this->dataGroupService;
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
        $filteredRoles = $this->filterRoles($this->userRoles, $rolesToExclude, $rolesToInclude, $entities);

        $resourcePermission = new ResourcePermission(false, false, false, false);

        foreach ($filteredRoles as $role) {
            $userRoleId = $role->getId();
            // TODO:: improve fetch matching data groups using single query
            $permissions = $this->getDataGroupService()->getDataGroupPermission(
                $dataGroupName,
                $userRoleId,
                $selfPermission
            );

            foreach ($permissions as $permission) {
                $resourcePermission = $resourcePermission->orWith(ResourcePermission::createFromDataGroupPermission($permission));
            }
        }

        return $resourcePermission;
    }

    /**
     * @inheritDoc
     */
    public function getDataGroupPermissionCollection(
        DataGroupPermissionFilterParams $dataGroupPermissionFilterParams = null
    ): DataGroupPermissionCollection {
        $dataGroupPermissionFilterParams = $dataGroupPermissionFilterParams ?? new DataGroupPermissionFilterParams();
        $filteredRoles = $this->filterRoles(
            $this->userRoles,
            $dataGroupPermissionFilterParams->getRolesToExclude(),
            $dataGroupPermissionFilterParams->getRolesToInclude(),
            $dataGroupPermissionFilterParams->getEntities()
        );
        $dataGroupPermissionFilterParams->setUserRoles($filteredRoles);
        return $this->getDataGroupService()->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
    }

    public function getModuleDefaultPage(string $module): ?string
    {
        $action = null;

        $userRoleIds = [];
        foreach ($this->userRoles as $role) {
            $userRoleIds[] = $role->getId();
        }
        $defaultPages = $this->getHomePageDao()->getModuleDefaultPagesInPriorityOrder($module, $userRoleIds);

        foreach ($defaultPages as $defaultPage) {
            $enabled = true;
            $enableClass = $defaultPage->getEnableClass();
            $fallbackNamespace = 'OrangeHRM\\Core\\HomePage\\';

            if (!empty($enableClass) && $this->getClassHelper()->classExists($enableClass, $fallbackNamespace)) {
                $enableClass = $this->getClassHelper()->getClass($enableClass, $fallbackNamespace);
                $enableClassInstance = new $enableClass();
                if ($enableClassInstance instanceof HomePageEnablerInterface) {
                    $enabled = $enableClassInstance->isEnabled($this->getUser());
                }
            }

            if ($enabled) {
                $action = $defaultPage->getAction();
                break;
            }
        }

        return $action;
    }

    public function getHomePage(): ?string
    {
        $action = null;

        $userRoleIds = [];
        foreach ($this->userRoles as $role) {
            $userRoleIds[] = $role->getId();
        }
        $defaultPages = $this->getHomePageDao()->getHomePagesInPriorityOrder($userRoleIds);

        foreach ($defaultPages as $defaultPage) {
            $enabled = true;
            $enableClass = $defaultPage->getEnableClass();
            $fallbackNamespace = 'OrangeHRM\\Core\\HomePage\\';

            if (!empty($enableClass) && $this->getClassHelper()->classExists($enableClass, $fallbackNamespace)) {
                $enableClass = $this->getClassHelper()->getClass($enableClass, $fallbackNamespace);
                $enableClassInstance = new $enableClass();
                if ($enableClassInstance instanceof HomePageEnablerInterface) {
                    $enabled = $enableClassInstance->isEnabled($this->getUser());
                }
            }
            if ($enabled) {
                $action = $defaultPage->getAction();
                break;
            }
        }

        return $action;
    }

    /**
     * @todo fix this in `ohrm_workflow_state_machine`
     * @param string $roleName
     * @param string $workflow
     * @return string
     */
    protected function fixUserRoleNameForWorkflowStateMachine(string $roleName, string $workflow): string
    {
        $fixedName = $roleName;
        if ($roleName == 'ESS' && $workflow != WorkflowStateMachine::FLOW_LEAVE) {
            $fixedName = 'ESS User';
        } elseif ($roleName == 'HiringManager' && $workflow == WorkflowStateMachine::FLOW_RECRUITMENT) {
            $fixedName = 'HIRING MANAGER';
        }

        return $fixedName;
    }
}
