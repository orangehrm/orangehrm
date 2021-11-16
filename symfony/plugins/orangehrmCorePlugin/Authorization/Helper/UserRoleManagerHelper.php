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

namespace OrangeHRM\Core\Authorization\Helper;

use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionCollection;
use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionFilterParams;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Authorization\Manager\AbstractUserRoleManager;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Framework\Services;

class UserRoleManagerHelper
{
    use ServiceContainerTrait;

    /**
     * @return AbstractUserRoleManager|BasicUserRoleManager
     */
    private function getUserRoleManager(): AbstractUserRoleManager
    {
        return $this->getContainer()->get(Services::USER_ROLE_MANAGER);
    }

    /**
     * Returns the data group permissions without considering any employee or other entity
     *
     * @param string[]|string $dataGroupNames
     *
     * @return ResourcePermission
     * @throws DaoException
     */
    public function getEntityIndependentDataGroupPermissions($dataGroupNames): ResourcePermission
    {
        return $this->getUserRoleManager()->getDataGroupPermissions($dataGroupNames, [], [], false, []);
    }

    /**
     * Returns the data group permission collection without considering any employee or other entity
     *
     * @param array $dataGroups
     * @return DataGroupPermissionCollection
     */
    public function geEntityIndependentDataGroupPermissionCollection(array $dataGroups): DataGroupPermissionCollection
    {
        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $dataGroupPermissionFilterParams->setDataGroups($dataGroups);
        return $this->getUserRoleManager()->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
    }

    /**
     * @param string|string[] $dataGroupName
     * @param int|null $empNumber
     * @return ResourcePermission
     */
    public function getDataGroupPermissionsForEmployee($dataGroupName, ?int $empNumber = null): ResourcePermission
    {
        return $this->getUserRoleManager()->getDataGroupPermissions(
            $dataGroupName,
            [],
            [],
            $this->isSelfByEmpNumber($empNumber),
            is_null($empNumber) ? [] : [Employee::class => $empNumber]
        );
    }

    /**
     * @param array $dataGroups
     * @param int|null $empNumber
     * @return DataGroupPermissionCollection
     */
    public function getDataGroupPermissionCollectionForEmployee(
        array $dataGroups,
        ?int $empNumber = null
    ): DataGroupPermissionCollection {
        $dataGroupPermissionFilterParams = new DataGroupPermissionFilterParams();
        $dataGroupPermissionFilterParams->setDataGroups($dataGroups);
        $dataGroupPermissionFilterParams->setEntities(is_null($empNumber) ? [] : [Employee::class => $empNumber]);
        $dataGroupPermissionFilterParams->setSelfPermissions($this->isSelfByEmpNumber($empNumber));
        return $this->getUserRoleManager()->getDataGroupPermissionCollection($dataGroupPermissionFilterParams);
    }

    /**
     * @param int|null $empNumber
     * @return bool
     */
    public function isSelfByEmpNumber(?int $empNumber = null): bool
    {
        $loggedInEmpNumber = $this->getUserRoleManager()->getUser()->getEmpNumber();
        return ($loggedInEmpNumber === $empNumber) && null !== $empNumber;
    }

    /**
     * @param int|null $empNumber
     * @return bool
     */
    public function isEmployeeAccessible(?int $empNumber): bool
    {
        return $this->getUserRoleManager()->isEntityAccessible(Employee::class, $empNumber) ||
            $this->isSelfByEmpNumber($empNumber);
    }
}
