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
use OrangeHRM\Entity\EnforcePasswordRequest;

class EnforcePasswordDao extends BaseDao
{
    /**
     * @param EnforcePasswordRequest $enforcePasswordRequest
     * @return EnforcePasswordRequest
     */
    public function saveEnforcedPasswordRequest(EnforcePasswordRequest $enforcePasswordRequest): EnforcePasswordRequest
    {
        $this->persist($enforcePasswordRequest);
        return $enforcePasswordRequest;
    }

    /**
     * @param string $resetCode
     * @return EnforcePasswordRequest|null
     */
    public function getEnforcedPasswordLogByResetCode(string $resetCode): ?EnforcePasswordRequest
    {
        $q = $this->createQueryBuilder(EnforcePasswordRequest::class, 'request');
        $q->leftJoin('request.user', 'user');
        $q->andWhere('request.resetCode = :code');
        $q->andWhere('user.deleted = :deleted');
        $q->setParameter('deleted', false);
        $q->setParameter('code', $resetCode);
        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $userId
     * @param bool   $expired
     * @return bool
     */
    public function updateEnforcedPasswordValid(string $userId, bool $expired): bool
    {
        $q = $this->createQueryBuilder(EnforcePasswordRequest::class, 'request');
        $q->update()
            ->set('request.expired', ':expired')
            ->setParameter('expired', $expired)
            ->where('request.user = :userId')
            ->setParameter('userId', $userId);
        $result = $q->getQuery()->execute();
        return $result > 0;
    }
}
