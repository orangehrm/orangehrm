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

use Doctrine\ORM\NonUniqueResultException;
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
     *
     * @return EnforcePasswordRequest|null
     * @throws NonUniqueResultException
     */
    public function getEnforcedPasswordLogByResetCode(string $resetCode): ?EnforcePasswordRequest
    {
        $q = $this->createQueryBuilder(EnforcePasswordRequest::class, 'enforce');
        $q->andWhere('enforce.resetCode = :code');
        $q->setParameter('code', $resetCode);
        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $resetCode
     * @param int    $value
     * @return bool
     */
    public function updateEnforcedPasswordValid(string $resetCode, int $value): bool
    {
        $q = $this->createQueryBuilder(EnforcePasswordRequest::class, 'enforce');
        $q->update()
            ->set('enforce.expired', ':value')
            ->setParameter('value', $value)
            ->andWhere('enforce.resetCode = :resetCode')
            ->setParameter('resetCode', $resetCode);
        $result = $q->getQuery()->execute();
        return $result > 0;
    }
}
