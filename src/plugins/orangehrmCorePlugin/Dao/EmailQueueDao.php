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

namespace OrangeHRM\Core\Dao;

use OrangeHRM\Entity\Mail;

class EmailQueueDao extends BaseDao
{
    /**
     * @param int $id
     * @return Mail|null
     */
    public function getEmail(int $id): ?Mail
    {
        return $this->getRepository(Mail::class)->find($id);
    }

    /**
     * @param Mail $mail
     * @return Mail
     */
    public function saveEmail(Mail $mail): Mail
    {
        $this->persist($mail);
        return $mail;
    }

    /**
     * @param int[] $toDeleteIds
     * @return int
     */
    public function removeFromQueue(array $toDeleteIds): int
    {
        $q = $this->createQueryBuilder(Mail::class, 'm');
        $q->delete()
            ->where($q->expr()->in('m.id', ':ids'))
            ->setParameter('ids', $toDeleteIds);
        return $q->getQuery()->execute();
    }

    /**
     * @return int[]
     */
    public function getAllPendingMailIds(): array
    {
        $q = $this->createQueryBuilder(Mail::class, 'm');
        $q->select('m.id')
            ->where('m.status = :status')
            ->setParameter('status', Mail::STATUS_PENDING);
        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'id');
    }
}
