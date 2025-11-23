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

namespace OrangeHRM\Tests\Util\Fixture;

use OrangeHRM\Entity\DataGroup;
use OrangeHRM\Entity\DataGroupPermission;
use OrangeHRM\Entity\UserRole;

class DataGroupPermissionFixture extends AbstractFixture
{
    /**
     * @inheritDoc
     */
    protected function getContent(): array
    {
        /** @var UserRole[] $userRoles */
        $userRoles = $this->getEntityManager()->getRepository(UserRole::class)->findAll();
        $userRoleResults = [];
        foreach ($userRoles as $userRole) {
            $result = [];
            $result['id'] = $userRole->getId();
            $result['name'] = $userRole->getName();
            $result['displayName'] = $userRole->getDisplayName();
            $result['isAssignable'] = $userRole->isAssignable();
            $result['isPredefined'] = $userRole->isPredefined();
            $userRoleResults[] = $result;
        }

        /** @var DataGroup[] $dataGroups */
        $dataGroups = $this->getEntityManager()->getRepository(DataGroup::class)->findAll();
        $dataGroupResults = [];
        foreach ($dataGroups as $dataGroup) {
            $result = [];
            $result['id'] = $dataGroup->getId();
            $result['name'] = $dataGroup->getName();
            $result['description'] = $dataGroup->getDescription();
            $result['canRead'] = $dataGroup->canRead();
            $result['canCreate'] = $dataGroup->canCreate();
            $result['canUpdate'] = $dataGroup->canUpdate();
            $result['canDelete'] = $dataGroup->canDelete();
            $dataGroupResults[] = $result;
        }

        /** @var DataGroupPermission[] $dataGroupPermissions */
        $dataGroupPermissions = $this->getEntityManager()->getRepository(DataGroupPermission::class)->findAll();
        $results = [];
        foreach ($dataGroupPermissions as $dataGroupPermission) {
            $result = [];
            $result['id'] = $dataGroupPermission->getId();
            $result['user_role_id'] = $dataGroupPermission->getUserRole()->getId();
            $result['data_group_id'] = $dataGroupPermission->getDataGroup()->getId();
            $result['canRead'] = $dataGroupPermission->canRead();
            $result['canCreate'] = $dataGroupPermission->canCreate();
            $result['canUpdate'] = $dataGroupPermission->canUpdate();
            $result['canDelete'] = $dataGroupPermission->canDelete();
            $result['self'] = $dataGroupPermission->isSelf();
            $results[] = $result;
        }

        return ['UserRole' => $userRoleResults, 'DataGroup' => $dataGroupResults, 'DataGroupPermission' => $results];
    }

    /**
     * @inheritDoc
     */
    public static function getFileName(): string
    {
        return 'DataGroupPermission.yaml';
    }
}
