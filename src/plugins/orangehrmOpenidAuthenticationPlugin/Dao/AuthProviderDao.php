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

namespace OrangeHRM\OpenidAuthentication\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\AuthProviderExtraDetails;
use OrangeHRM\Entity\OpenIdProvider;
use OrangeHRM\Entity\OpenIdUserIdentity;
use OrangeHRM\OpenidAuthentication\Dto\ProviderSearchFilterParams;
use OrangeHRM\ORM\Paginator;

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

    /**
     * @param ProviderSearchFilterParams $providerSearchFilterParams
     * @return array
     */
    public function getAuthProviders(ProviderSearchFilterParams $providerSearchFilterParams): array
    {
        $q = $this->getAuthProvidersPaginator($providerSearchFilterParams);
        return $q->getQuery()->execute();
    }

    /**
     * @param ProviderSearchFilterParams $providerSearchFilterParams
     * @return Paginator
     */
    private function getAuthProvidersPaginator(ProviderSearchFilterParams $providerSearchFilterParams): Paginator
    {
        $qb = $this->createQueryBuilder(AuthProviderExtraDetails::class, 'providerDetails');
        $this->setSortingAndPaginationParams($qb, $providerSearchFilterParams);
        $qb->leftJoin('providerDetails.openIdProvider', 'provider');

        if (!is_null($providerSearchFilterParams->getStatus())) {
            $qb->andWhere('provider.status = :status');
            $qb->setParameter('status', $providerSearchFilterParams->getStatus());
        }
        if (!is_null($providerSearchFilterParams->getName())) {
            $qb->andWhere($qb->expr()->like('provider.providerName', ':name'));
            $qb->setParameter('name', '%' . $providerSearchFilterParams->getName() . '%');
        }

        return $this->getPaginator($qb);
    }

    /**
     * @param ProviderSearchFilterParams $providerSearchFilterParams
     * @return int
     */
    public function getAuthProviderCount(ProviderSearchFilterParams $providerSearchFilterParams): int
    {
        return $this->getAuthProvidersPaginator($providerSearchFilterParams)->count();
    }

    /**
     * @param int[] $ids
     * @return int
     */
    public function deleteProviders(array $ids): int
    {
        $q = $this->createQueryBuilder(OpenIdProvider::class, 'openIdProvider');
        $q->update()
            ->set('openIdProvider.status', ':status')
            ->where($q->expr()->in('openIdProvider.id', ':ids'))
            ->setParameter('ids', $ids)
            ->setParameter('status', false);
        return $q->getQuery()->execute();
    }

    /**
     * @param OpenIdProvider $openIdProvider
     * @return OpenIdProvider
     */
    public function saveProvider(OpenIdProvider $openIdProvider): OpenIdProvider
    {
        $this->persist($openIdProvider);
        return $openIdProvider;
    }

    /**
     * @param AuthProviderExtraDetails $authProviderExtraDetails
     * @return AuthProviderExtraDetails
     */
    public function saveAuthProviderExtraDetails(AuthProviderExtraDetails $authProviderExtraDetails): AuthProviderExtraDetails
    {
        $this->persist($authProviderExtraDetails);
        return $authProviderExtraDetails;
    }

    /**
     * @return OpenIdProvider[]
     */
    public function getAuthProvidersForLoginPage(): array
    {
        $q = $this->createQueryBuilder(OpenIdProvider::class, 'authProvider');
        $q->andWhere('authProvider.status = :status');
        $q->setParameter('status', true);

        return $q->getQuery()->execute();
    }

    /**
     * @param OpenIdUserIdentity $openIdUserIdentity
     * @return OpenIdUserIdentity
     */
    public function saveUserIdentity(OpenIdUserIdentity $openIdUserIdentity): OpenIdUserIdentity
    {
        $this->persist($openIdUserIdentity);
        return $openIdUserIdentity;
    }
}
