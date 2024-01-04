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

namespace OrangeHRM\Core\Authorization\Dao;

use OrangeHRM\Core\Authorization\Dto\DataGroupPermissionFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\DataGroup;
use OrangeHRM\Entity\DataGroupPermission;
use OrangeHRM\Entity\UserRole;

class DataGroupDao extends BaseDao
{
    /**
     * @param string $name
     * @return DataGroup|null
     */
    public function getDataGroup(string $name): ?DataGroup
    {
        $q = $this->createQueryBuilder(DataGroup::class, 'd');
        $q->where('d.name = :name');
        $q->setParameter('name', $name);

        return $q->getQuery()->getOneOrNullResult();
    }


    /**
     * @param string[]|string $dataGroups
     * @param int $userRoleId
     * @param bool $selfPermission
     * @return DataGroupPermission[]
     */
    public function getDataGroupPermission($dataGroups, int $userRoleId, bool $selfPermission = false): array
    {
        if (!is_array($dataGroups) && $dataGroups != null) {
            $dataGroups = [$dataGroups];
        }

        $q = $this->createQueryBuilder(DataGroupPermission::class, 'p');
        $q->leftJoin('p.dataGroup', 'd');
        $q->leftJoin('p.userRole', 'ur');
        $q->andWhere('ur.id = :userRoleId');
        $q->setParameter('userRoleId', $userRoleId);
        $q->andWhere($q->expr()->in('d.name', ':dataGroups'))
            ->setParameter('dataGroups', $dataGroups);
        $q->andWhere('p.self = :self')
            ->setParameter('self', $selfPermission);

        return $q->getQuery()->execute();
    }

    /**
     * @param string $apiClassName
     * @param string[]|UserRole[] $roles Array of UserRole objects or user role names
     * @return DataGroupPermission[]
     */
    public function getApiPermissions(string $apiClassName, array $roles): array
    {
        $roleNames = [];

        foreach ($roles as $role) {
            if ($role instanceof UserRole) {
                $roleNames[] = $role->getName();
            } elseif (is_string($role)) {
                $roleNames[] = $role;
            }
        }

        $q = $this->createQueryBuilder(DataGroupPermission::class, 'dgp');
        $q->leftJoin('dgp.dataGroup', 'dg');
        $q->leftJoin('dgp.userRole', 'ur');
        $q->leftJoin('dg.apiPermissions', 'ap');
        $q->andWhere($q->expr()->in('ur.name', ':userRoleNames'))
            ->setParameter('userRoleNames', $roleNames);
        $q->andWhere('ap.apiName = :apiClassName')
            ->setParameter('apiClassName', $apiClassName);

        return $q->getQuery()->execute();
    }

    /**
     * @param DataGroupPermissionFilterParams $dataGroupPermissionFilterParams
     * @return DataGroupPermission[]
     */
    public function getDataGroupPermissions(DataGroupPermissionFilterParams $dataGroupPermissionFilterParams): array
    {
        $roleIds = [];
        foreach ($dataGroupPermissionFilterParams->getUserRoles() as $role) {
            if ($role instanceof UserRole) {
                $roleIds[] = $role->getId();
            }
        }

        $q = $this->createQueryBuilder(DataGroupPermission::class, 'p');
        $q->leftJoin('p.dataGroup', 'dg');
        $q->leftJoin('p.userRole', 'ur');

        $q->andWhere($q->expr()->in('ur.id', ':userRoleIds'))
            ->setParameter('userRoleIds', $roleIds);

        if (!$dataGroupPermissionFilterParams->isWithApiDataGroups()) {
            $q->leftJoin('dg.apiPermissions', 'ap');
            $q->andWhere($q->expr()->isNull('ap.id'));
        }

        if ($dataGroupPermissionFilterParams->isOnlyAccessible()) {
            $or = $q->expr()->orX();
            $or->add($q->expr()->eq('p.canRead', ':canRead'));
            $or->add($q->expr()->eq('p.canCreate', ':canCreate'));
            $or->add($q->expr()->eq('p.canUpdate', ':canUpdate'));
            $or->add($q->expr()->eq('p.canDelete', ':canDelete'));
            $q->andWhere($or)
                ->setParameter('canRead', true)
                ->setParameter('canCreate', true)
                ->setParameter('canUpdate', true)
                ->setParameter('canDelete', true);
        }
        $q->andWhere('p.self = :self')
            ->setParameter('self', $dataGroupPermissionFilterParams->isSelfPermissions());

        if (!empty($dataGroupPermissionFilterParams->getDataGroups())) {
            $q->andWhere($q->expr()->in('dg.name', ':dataGroups'))
                ->setParameter('dataGroups', $dataGroupPermissionFilterParams->getDataGroups());
        }

        return $q->getQuery()->execute();
    }

    /**
     * @return DataGroup[]
     */
    public function getDataGroups(): array
    {
        $q = $this->createQueryBuilder(DataGroup::class, 'd');
        $q->addOrderBy('d.description');
        return $q->getQuery()->execute();
    }
}
