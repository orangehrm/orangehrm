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

namespace OrangeHRM\Core\Authorization\Service;

use OrangeHRM\Core\Authorization\Dao\DataGroupDao;
use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionCollection;
use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionFilterParams;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Entity\DataGroup;
use OrangeHRM\Entity\DataGroupPermission;
use OrangeHRM\Entity\UserRole;

class DataGroupService
{
    /**
     * @var DataGroupDao|null
     */
    public ?DataGroupDao $dao = null;

    /**
     * Get the Data group dao
     * @return DataGroupDao dao instance
     */
    public function getDao(): DataGroupDao
    {
        if (!$this->dao instanceof DataGroupDao) {
            $this->dao = new DataGroupDao();
        }
        return $this->dao;
    }

    /**
     * Set the data group dao
     * @param DataGroupDao $dao
     */
    public function setDao(DataGroupDao $dao): void
    {
        $this->dao = $dao;
    }

    /**
     * Get Data Group permissions
     *
     * @param string[]|string $dataGroups A single data group name (string), an array of data group names or null (to return all data group permissions)
     * @param int $userRoleId User role id
     * @param bool $selfPermission If true, self permissions are returned. If false non-self permissions are returned
     *
     * @return DataGroupPermission[] Collection of DataGroupPermission objects
     */
    public function getDataGroupPermission($dataGroups, int $userRoleId, bool $selfPermission = false): array
    {
        return $this->getDao()->getDataGroupPermission($dataGroups, $userRoleId, $selfPermission);
    }

    /**
     * Get All defined data groups in the system
     *
     * @return DataGroup[]
     */
    public function getDataGroups(): array
    {
        return $this->getDao()->getDataGroups();
    }

    /**
     * Get Data Group with given name
     *
     * @param string $name Data Group name
     * @return DataGroup DataGroup or false if no match.
     */
    public function getDataGroup(string $name): ?DataGroup
    {
        return $this->getDao()->getDataGroup($name);
    }

    /**
     * @param string $apiClassName
     * @param string[]|UserRole[] $roles Array of Role names or Array of UserRole objects
     * @return ResourcePermission
     */
    public function getApiPermissions(string $apiClassName, array $roles): ResourcePermission
    {
        $apiPermissions = $this->getDao()->getApiPermissions($apiClassName, $roles);

        $read = false;
        $create = false;
        $update = false;
        $delete = false;

        foreach ($apiPermissions as $apiPermission) {
            if ($apiPermission->canRead()) {
                $read = true;
            }
            if ($apiPermission->canCreate()) {
                $create = true;
            }
            if ($apiPermission->canUpdate()) {
                $update = true;
            }
            if ($apiPermission->canDelete()) {
                $delete = true;
            }
        }

        return new ResourcePermission($read, $create, $update, $delete);
    }

    /**
     * @param DataGroupPermissionFilterParams $dataGroupPermissionFilterParams
     * @return DataGroupPermissionCollection|array<string, ResourcePermission>
     */
    public function getDataGroupPermissionCollection(DataGroupPermissionFilterParams $dataGroupPermissionFilterParams): DataGroupPermissionCollection
    {
        $dataGroupPermissions = $this->getDao()->getDataGroupPermissions($dataGroupPermissionFilterParams);
        $dataGroupPermissionCollection = new DataGroupPermissionCollection();

        foreach ($dataGroupPermissions as $dataGroupPermission) {
            $dataGroup = $dataGroupPermission->getDataGroup()->getName();
            $resourcePermission = new ResourcePermission(
                $dataGroupPermission->canRead(),
                $dataGroupPermission->canCreate(),
                $dataGroupPermission->canUpdate(),
                $dataGroupPermission->canDelete()
            );
            if ($dataGroupPermissionCollection->has($dataGroup)) {
                $currentResourcePermission = $dataGroupPermissionCollection->get($dataGroup);
                $dataGroupPermissionCollection->set(
                    $dataGroup,
                    $currentResourcePermission->orWith($resourcePermission)
                );
            } else {
                $dataGroupPermissionCollection->set($dataGroup, $resourcePermission);
            }
        }

        return $dataGroupPermissionCollection;
    }
}
