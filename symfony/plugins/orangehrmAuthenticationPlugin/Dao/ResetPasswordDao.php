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

namespace OrangeHRM\Authentication\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use Exception;
use OrangeHRM\Entity\ResetPassword;

class ResetPasswordDao extends BaseDao
{
    /**
     * @param ResetPassword $resetPassword
     * @return ResetPassword
     */
    public function saveResetPassword(ResetPassword $resetPassword): ResetPassword
    {
        $this->persist($resetPassword);
        return $resetPassword;
    }


    /**
     * @param string $email
     * @return ResetPassword|null
     * @throws DaoException
     */
    public function getResetPasswordLogByEmail(string $email): ?ResetPassword
    {
        try {
            $q = $this->createQueryBuilder(ResetPassword::class, 'r');
            $q->andWhere('r.reset_email = :email')
                ->setParameter('email', $email)
                ->orderBy('r.reset_request_date DESC');
            return $q->getQuery()->getOneOrNullResult();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param string $resetCode
     * @return ResetPassword|null
     * @throws DaoException
     */
    public function getResetPasswordLogByResetCode(string $resetCode): ?ResetPassword
    {
        $q = $this->createQueryBuilder(ResetPassword::class, 'r');
        $q->andWhere('r.resetCode = :code');
        $q->setParameter('code', $resetCode);
        return $q->getQuery()->getOneOrNullResult();
    }
}
