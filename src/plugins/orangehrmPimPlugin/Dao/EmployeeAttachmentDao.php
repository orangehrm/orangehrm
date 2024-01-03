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

namespace OrangeHRM\Pim\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\Pim\Dto\PartialEmployeeAttachment;

class EmployeeAttachmentDao extends BaseDao
{
    /**
     * @param int $empNumber
     * @param string $screen
     * @return PartialEmployeeAttachment[]
     */
    public function getEmployeeAttachments(int $empNumber, string $screen): array
    {
        $select = 'NEW ' . PartialEmployeeAttachment::class . "(a.attachId,a.description,a.filename,a.size,a.fileType,a.attachedBy,a.attachedByName,a.attachedTime)";
        $q = $this->createQueryBuilder(EmployeeAttachment::class, 'a');
        $q->select($select);
        $q->andWhere('a.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);
        $q->andWhere('a.screen = :screen')
            ->setParameter('screen', $screen);
        $q->addOrderBy('a.attachId', ListSorter::ASCENDING);

        return $q->getQuery()->execute();
    }

    /**
     * @param int $empNumber
     * @param int $attachId
     * @param string|null $screen
     * @return EmployeeAttachment|null
     */
    public function getEmployeeAttachment(int $empNumber, int $attachId, ?string $screen = null): ?EmployeeAttachment
    {
        $criteria = ['employee' => $empNumber, 'attachId' => $attachId];
        if ($screen) {
            $criteria['screen'] = $screen;
        }
        $employeeAttachment = $this->getRepository(EmployeeAttachment::class)->findOneBy($criteria);
        if ($employeeAttachment instanceof EmployeeAttachment) {
            return $employeeAttachment;
        }
        return null;
    }

    /**
     * @param int $empNumber
     * @param int $attachId
     * @param string|null $screen
     * @return PartialEmployeeAttachment|null
     */
    public function getPartialEmployeeAttachment(int $empNumber, int $attachId, ?string $screen): ?PartialEmployeeAttachment
    {
        $select = 'NEW ' . PartialEmployeeAttachment::class . "(a.attachId,a.description,a.filename,a.size,a.fileType,a.attachedBy,a.attachedByName,a.attachedTime)";
        $q = $this->createQueryBuilder(EmployeeAttachment::class, 'a');
        $q->select($select);
        $q->andWhere('a.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);
        $q->andWhere('a.screen = :screen')
            ->setParameter('screen', $screen);
        $q->andWhere('a.attachId = :attachId')
            ->setParameter('attachId', $attachId);
        $q->addOrderBy('a.attachId', ListSorter::ASCENDING);
        return $q->getQuery()->getOneOrNullResult();
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
     */
    public function deleteEmployeeAttachments(int $empNumber, string $screen, array $toBeDeletedIds): int
    {
        $q = $this->createQueryBuilder(EmployeeAttachment::class, 'a');
        $q->delete();
        $q->andWhere('a.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);
        $q->andWhere('a.screen = :screen')
            ->setParameter('screen', $screen);
        $q->andWhere($q->expr()->in('a.attachId', ':ids'))
            ->setParameter('ids', $toBeDeletedIds);

        return $q->getQuery()->execute();
    }

    /**
     * @param EmployeeAttachment $employeeAttachment
     */
    public function deleteEmployeeAttachment(EmployeeAttachment $employeeAttachment): void
    {
        $this->remove($employeeAttachment);
    }
}
