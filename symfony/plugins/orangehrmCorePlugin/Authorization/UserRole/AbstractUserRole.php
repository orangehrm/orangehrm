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
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Core\Authorization\Exception\AuthorizationException;
use OrangeHRM\Core\Authorization\Manager\AbstractUserRoleManager;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Location;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

/**
 * Description of UserRoleInterface
 *
 * @author Chameera Senarathna
 */
abstract class AbstractUserRole
{
    use AuthUserTrait;
    use EmployeeServiceTrait;

    protected $operationalCountryService;
    protected ?LocationService $locationService = null;
    protected $projectService;
    protected $vacancyService;

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
    public function __construct(string $roleName, AbstractUserRoleManager $userRoleManager)
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
     * @return UserService
     */
    public function getSystemUserService(): UserService
    {
        return $this->getContainer()->get(Services::USER_SERVICE);
    }

    /**
     * @return LocationService
     */
    public function getLocationService(): LocationService
    {
        if (!$this->locationService instanceof LocationService) {
            $this->locationService = new LocationService();
        }
        return $this->locationService;
    }

    /**
     * @param LocationService $locationService
     */
    public function setLocationService(LocationService $locationService): void
    {
        $this->locationService = $locationService;
    }

    public function getOperationalCountryService()
    {
        // TODO
        if (empty($this->operationalCountryService)) {
            $this->operationalCountryService = new OperationalCountryService();
        }
        return $this->operationalCountryService;
    }

    public function setOperationalCountryService($operationalCountryService)
    {
        // TODO
        $this->operationalCountryService = $operationalCountryService;
    }

    /**
     * Get the Project Data Access Object
     * @return ProjectService
     */
    public function getProjectService()
    {
        // TODO
        if (is_null($this->projectService)) {
            $this->projectService = new ProjectService();
        }
        return $this->projectService;
    }

    /**
     * Set Project Service Access Object
     * @param ProjectService $projectService
     * @return void
     */
    public function setProjectService(ProjectService $projectService)
    {
        // TODO
        $this->projectService = $projectService;
    }

    /**
     * Get VacancyService
     * @return VacancyService
     */
    public function getVacancyService()
    {
        // TODO
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
        }
        return $this->vacancyService;
    }

    /**
     * Set Vacancy Service
     * @param VacancyService $vacancyService
     */
    public function setVacancyService(VacancyService $vacancyService)
    {
        // TODO
        $this->vacancyService = $vacancyService;
    }


    public function getAccessibleEntities($entityType, $operation = null, $returnType = null, $requiredPermissions = [])
    {
        $permitted = $this->areRequiredPermissionsAvailable($requiredPermissions);

        if ($permitted) {
            switch ($entityType) {
                case Employee::class:
                    $entities = $this->getAccessibleEmployees($operation, $returnType, $requiredPermissions);
                    break;
                case 'Project':
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

    public function getAccessibleEntityProperties(
        $entityType,
        $properties = [],
        $orderField = null,
        $orderBy = null,
        $requiredPermissions = []
    ) {
        $permitted = $this->areRequiredPermissionsAvailable($requiredPermissions);
        if ($permitted) {
            switch ($entityType) {
                case Employee::class:
                    $propertyList = $this->getAccessibleEmployeePropertyList($properties, $orderField, $orderBy, $requiredPermissions);
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
            switch ($entityType) {
                case Employee::class:
                    $ids = $this->getAccessibleEmployeeIds($operation, $returnType, $requiredPermissions);
                    break;
                case User::class:
                    $ids = $this->getAccessibleSystemUserIds($operation, $returnType, $requiredPermissions);
                    break;
                case 'OperationalCountry':
                    // TODO:: implement and remove below line
                    throw AuthorizationException::entityNotImplemented($entityType, __METHOD__);
                    $ids = $this->getAccessibleOperationalCountryIds($operation, $returnType, $requiredPermissions);
                    break;
                case UserRole::class:
                    $ids = $this->getAccessibleUserRoleIds($operation, $returnType, $requiredPermissions);
                    break;
                case Location::class:
                    $ids = $this->getAccessibleLocationIds($operation, $returnType, $requiredPermissions);
                    break;
                case 'Project':
                    // TODO:: implement and remove below line
                    throw AuthorizationException::entityNotImplemented($entityType, __METHOD__);
                    $ids = $this->getAccessibleProjectIds($operation, $returnType, $requiredPermissions);
                    break;
                case 'Vacancy':
                    // TODO:: implement and remove below line
                    throw AuthorizationException::entityNotImplemented($entityType, __METHOD__);
                    $ids = $this->getAccessibleVacancyIds($operation, $returnType, $requiredPermissions);
                    break;
                default:
                    throw AuthorizationException::entityNotSupported($entityType, __METHOD__);
            }
        }
        return $ids;
    }

    public function getEmployeesWithRole($entities = [])
    {
        return [];
    }

    public function getAccessibleProjects($operation = null, $returnType = null, $requiredPermissions = [])
    {
        return [];
    }

    public function getAccessibleProjectIds($operation = null, $returnType = null, $requiredPermissions = [])
    {
        return [];
    }

    public function getAccessibleVacancies($operation = null, $returnType = null, $requiredPermissions = [])
    {
        return [];
    }

    public function getAccessibleVacancyIds($operation = null, $returnType = null, $requiredPermissions = [])
    {
        return [];
    }

    /**
     * @param null $operation
     * @param null $returnType
     * @param array $requiredPermissions
     * @return array|Employee[]
     */
    abstract public function getAccessibleEmployees($operation = null, $returnType = null, $requiredPermissions = []): array;

    abstract public function getAccessibleEmployeePropertyList(
        $properties,
        $orderField,
        $orderBy,
        $requiredPermissions = []
    );

    abstract public function getAccessibleEmployeeIds($operation = null, $returnType = null, $requiredPermissions = []);

    abstract public function getAccessibleSystemUserIds(
        $operation = null,
        $returnType = null,
        $requiredPermissions = []
    );

    abstract public function getAccessibleOperationalCountryIds(
        $operation = null,
        $returnType = null,
        $requiredPermissions = []
    );

    abstract public function getAccessibleUserRoleIds($operation = null, $returnType = null, $requiredPermissions = []);

    abstract public function getAccessibleLocationIds($operation = null, $returnType = null, $requiredPermissions = []);

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
}
