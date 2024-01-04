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

namespace OrangeHRM\Core\Dao;

use OrangeHRM\Entity\UniqueId;

/**
 * @group Core
 * @group Dao
 */
class IDGeneratorDao extends BaseDao
{
    /**
     * @param string $entityClass
     * @return int
     */
    public function getCurrentID(string $entityClass): int
    {
        $q = $this->createQueryBuilder(UniqueId::class, 'u');
        $q->where('u.tableName = :tableName')
            ->setParameter('tableName', $this->getTableName($entityClass));

        $uniqueId = $q->getQuery()->getOneOrNullResult();
        if ($uniqueId instanceof UniqueId) {
            return $uniqueId->getLastId();
        } else {
            return 0;
        }
    }

    /**
     * @param string $entityClass
     * @param int $nextId
     * @return int
     */
    public function updateNextId(string $entityClass, int $nextId): int
    {
        $q = $this->createQueryBuilder(UniqueId::class, 'u');
        $q->update()
            ->set('u.lastId', ':lastId')
            ->setParameter('lastId', $nextId);
        $q->where('u.tableName = :tableName')
            ->setParameter('tableName', $this->getTableName($entityClass));
        return $q->getQuery()->execute();
    }

    /**
     * @param string $entityClass
     * @return string
     */
    private function getTableName(string $entityClass): string
    {
        return $this->getEntityManager()->getClassMetadata($entityClass)->getTableName();
    }
}
