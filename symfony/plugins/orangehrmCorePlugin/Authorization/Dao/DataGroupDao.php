<?php
/*
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

namespace OrangeHRM\Core\Authorization\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\DataGroup;
use OrangeHRM\Entity\DataGroupPermission;

class DataGroupDao extends BaseDao
{
    /**
     * @param string $name
     * @return DataGroup|null
     * @throws DaoException
     */
    public function getDataGroup(string $name): ?DataGroup
    {
        try {
            $q = $this->createQueryBuilder(DataGroup::class, 'd');
            $q->where('d.name = :name');
            $q->setParameter('name', $name);

            return $q->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param string[]|string $dataGroups
     * @param int $userRoleId
     * @param bool $selfPermission
     * @return DataGroupPermission[]
     * @throws DaoException
     */
    public function getDataGroupPermission($dataGroups, int $userRoleId, bool $selfPermission = false): array
    {
        if (!is_array($dataGroups) && $dataGroups != null) {
            $dataGroups = [$dataGroups];
        }

        try {
            $q = $this->createQueryBuilder(DataGroupPermission::class, 'p');
            $q->leftJoin('p.dataGroup', 'd');
            $q->leftJoin('p.userRole', 'ur');
            $q->andWhere('ur.id = :userRoleId');
            $q->setParameter('userRoleId', $userRoleId);
            if ($dataGroups != null) {
                $q->andWhere($q->expr()->in('d.name', ':dataGroups'))
                    ->setParameter('dataGroups', $dataGroups);
            }
            $q->andWhere('p.self = :self')
                ->setParameter('self', $selfPermission);

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return DataGroup[]
     * @throws DaoException
     */
    public function getDataGroups(): array
    {
        try {
            $q = $this->createQueryBuilder(DataGroup::class, 'd');
            $q->addOrderBy('d.description');
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
