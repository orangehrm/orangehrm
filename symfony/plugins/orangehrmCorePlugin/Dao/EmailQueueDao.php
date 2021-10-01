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

use Exception;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Mail;

class EmailQueueDao extends BaseDao
{
    /**
     * @param int $id
     * @return Mail|null|object
     */
    public function getEmail(int $id): ?Mail
    {
        return $this->getRepository(Mail::class)->find($id);
    }

    /**
     * @param Mail $mail
     * @return Mail
     * @throws DaoException
     */
    public function saveEmail(Mail $mail): Mail
    {
        try {
            $this->persist($mail);
            return $mail;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function removeFromQueue(array $toDeleteIds): int
    {
        try {
            $q = $this->createQueryBuilder(Mail::class, 'm');
            $q->delete()
                ->where($q->expr()->in('m.id', ':ids'))
                ->setParameter('ids', $toDeleteIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @return array
     * @throws DaoException
     */
    public function getAllPendingMails(): array
    {
        try {
            $q = $this->createQueryBuilder(Mail::class, 'm');
            $q->select()
                ->where('m.status = :status')
                ->setParameter('status', Mail::STATUS_PENDING);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}
