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

namespace OrangeHRM\OpenidAuthentication\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\AuthProviderExtraDetails;
use OrangeHRM\Entity\OpenIdProvider;

class AuthProviderDao extends BaseDao
{
    /**
     * @param int $providerId
     * @return AuthProviderExtraDetails|null
     */
    public function getAuthProviderById(int $providerId): ?OpenIdProvider
    {
        $q = $this->createQueryBuilder(OpenIdProvider::class, 'authProvider');
        $q->andWhere('authProvider.id = :providerId');
        $q->setParameter('providerId', $providerId);
        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param int $providerId
     * @return AuthProviderExtraDetails|null
     */
    public function getAuthProviderDetailsByProviderId(int $providerId): ?AuthProviderExtraDetails
    {
        $q = $this->createQueryBuilder(AuthProviderExtraDetails::class, 'providerDetails');
        $q->leftJoin('providerDetails.openIdProvider', 'openIdProvider');
        $q->andWhere('providerDetails.openIdProvider = :id');
        $q->setParameter('id', $providerId);
        return $q->getQuery()->getOneOrNullResult();
    }
}
