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

namespace OrangeHRM\Authentication\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\ResetPasswordRequest;

class ResetPasswordDao extends BaseDao
{
    /**
     * @param ResetPasswordRequest $resetPassword
     * @return ResetPasswordRequest
     */
    public function saveResetPasswordRequest(ResetPasswordRequest $resetPassword): ResetPasswordRequest
    {
        $this->persist($resetPassword);
        return $resetPassword;
    }


    /**
     * @param string $email
     * @return ResetPasswordRequest|null
     */
    public function getResetPasswordLogByEmail(string $email): ?ResetPasswordRequest
    {
        $q = $this->createQueryBuilder(ResetPasswordRequest::class, 'r');
        $q->andWhere('r.resetEmail = :email')
            ->setParameter('email', $email)
            ->orderBy('r.resetRequestDate', 'DESC')
            ->setMaxResults(1);
        return $q->getQuery()->execute()[0];
    }

    /**
     * @param string $resetCode
     * @return ResetPasswordRequest|null
     */
    public function getResetPasswordLogByResetCode(string $resetCode): ?ResetPasswordRequest
    {
        $q = $this->createQueryBuilder(ResetPasswordRequest::class, 'r');
        $q->andWhere('r.resetCode = :code');
        $q->setParameter('code', $resetCode);
        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $email
     * @param int $value
     * @return bool
     */
    public function updateResetPasswordValid(string $email, int $value): bool
    {
        $q = $this->createQueryBuilder(ResetPasswordRequest::class, 'r');
        $q->update()
            ->set('r.expired', ':value')
            ->setParameter('value', $value)
            ->andWhere('r.resetEmail = :email')
            ->setParameter('email', $email);
        $result = $q->getQuery()->execute();
        return $result > 0;
    }
}
