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

namespace OrangeHRM\Leave\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\LeaveType;

class LeaveTypeDao extends BaseDao
{
    /**
     * @return LeaveType[]
     * @throws DaoException
     */
    public function getLeaveTypeList(): array
    {
        try {
            $q = $this->createQueryBuilder(LeaveType::class, 'leaveType');
            $q->andWhere('leaveType.deleted = :deleted')
                ->setParameter('deleted', false);
            $q->orderBy('leaveType.name');

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param int $id
     * @return LeaveType|null
     * @throws DaoException
     */
    public function getLeaveTypeById(int $id): ?LeaveType
    {
        try {
            return $this->getRepository(LeaveType::class)->find($id);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param LeaveType $leaveType
     * @return LeaveType
     * @throws DaoException
     */
    public function saveLeaveType(LeaveType $leaveType): LeaveType
    {
        try {
            $this->persist($leaveType);
            return $leaveType;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param int[] $idsToDelete
     * @return int
     * @throws DaoException
     */
    public function deleteLeaveType(array $idsToDelete): int
    {
        try {
            $q = $this->createQueryBuilder(LeaveType::class, 'leaveType');
            $q->update();
            $q->where($q->expr()->in('leaveType.id', ':ids'))
                ->setParameter('ids', $idsToDelete);
            $q->set('leaveType.deleted', ':deleted')
                ->setParameter('deleted', true);

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @return LeaveType[]
     * @throws DaoException
     */
    public function getDeletedLeaveTypeList(): array
    {
        try {
            $q = $this->createQueryBuilder(LeaveType::class, 'leaveType');
            $q->andWhere('leaveType.deleted = :deleted')
                ->setParameter('deleted', true);
            $q->orderBy('leaveType.name');

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param $leaveTypeName
     * @return LeaveType|null
     * @throws DaoException
     */
    public function getLeaveTypeByName($leaveTypeName): ?LeaveType
    {
        try {
            $q = $this->createQueryBuilder(LeaveType::class, 'leaveType');
            $q->andWhere('leaveType.name = :name')
                ->setParameter('name', $leaveTypeName);
            $q->andWhere('leaveType.deleted = :deleted')
                ->setParameter('deleted', false);

            return $this->fetchOne($q);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param int $leaveTypeId
     * @return LeaveType|null
     * @throws DaoException
     */
    public function undeleteLeaveType(int $leaveTypeId): ?LeaveType
    {
        try {
            $leaveType = $this->getLeaveTypeById($leaveTypeId);
            if ($leaveType instanceof LeaveType) {
                $leaveType->setDeleted(false);
                $this->persist($leaveType);
            }
            return $leaveType;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}
