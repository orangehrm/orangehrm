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

namespace OrangeHRM\Pim\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\ORM\ListSorter;

class EmployeeAttachmentDao extends BaseDao
{
    /**
     * @param int $empNumber
     * @param string $screen
     * @return EmployeeAttachment[]
     * @throws DaoException
     */
    public function getEmployeeAttachments(int $empNumber, string $screen): array
    {
        try {
            $q = $this->createQueryBuilder(EmployeeAttachment::class, 'a');
            $q->andWhere('a.employee = :empNumber')
                ->setParameter('empNumber', $empNumber);
            $q->andWhere('a.screen = :screen')
                ->setParameter('screen', $screen);
            $q->addOrderBy('a.attachId', ListSorter::ASCENDING);

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @param int $attachId
     * @param string|null $screen
     * @return EmployeeAttachment|null
     * @throws DaoException
     */
    public function getEmployeeAttachment(int $empNumber, int $attachId, ?string $screen = null): ?EmployeeAttachment
    {
        try {
            $criteria = ['employee' => $empNumber, 'attachId' => $attachId];
            if ($screen) {
                $criteria['screen'] = $screen;
            }
            $employeeAttachment = $this->getRepository(EmployeeAttachment::class)->findOneBy($criteria);
            if ($employeeAttachment instanceof EmployeeAttachment) {
                return $employeeAttachment;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeAttachment $employeeAttachment
     * @return EmployeeAttachment
     */
    public function saveEmployeeAttachment(EmployeeAttachment $employeeAttachment): EmployeeAttachment
    {
        // increment seqNo if not set explicitly
        if ($employeeAttachment->getAttachId() === 0) {
            $q = $this->createQueryBuilder(EmployeeAttachment::class, 'a');
            $empNumber = $employeeAttachment->getEmployee()->getEmpNumber();
            $q->andWhere('a.employee = :empNumber')
                ->setParameter('empNumber', $empNumber);
            $q->select($q->expr()->max('a.attachId'));
            $maxAttachId = $q->getQuery()->getSingleScalarResult();
            $attachId = 1;
            if (!is_null($maxAttachId)) {
                $attachId += intval($maxAttachId);
            }
            $employeeAttachment->setAttachId($attachId);
        }

        $this->persist($employeeAttachment);
        return $employeeAttachment;
    }

    /**
     * @param int $empNumber
     * @param string $screen
     * @param array $toBeDeletedIds
     * @return int
     * @throws DaoException
     */
    public function deleteEmployeeAttachments(int $empNumber, string $screen, array $toBeDeletedIds): int
    {
        try {
            $q = $this->createQueryBuilder(EmployeeAttachment::class, 'a');
            $q->delete();
            $q->andWhere('a.employee = :empNumber')
                ->setParameter('empNumber', $empNumber);
            $q->andWhere('a.screen = :screen')
                ->setParameter('screen', $screen);
            $q->andWhere($q->expr()->in('a.attachId', ':ids'))
                ->setParameter('ids', $toBeDeletedIds);

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmployeeAttachment $employeeAttachment
     */
    public function deleteEmployeeAttachment(EmployeeAttachment $employeeAttachment): void
    {
        $this->remove($employeeAttachment);
    }
}
