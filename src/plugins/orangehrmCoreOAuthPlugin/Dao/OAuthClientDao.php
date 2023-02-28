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

namespace OrangeHRM\OAuth\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Dto\OAuthClientSearchFilterParams;
use OrangeHRM\ORM\QueryBuilderWrapper;

class OAuthClientDao extends BaseDao
{
    /**
     * @param OAuthClient $oAuthClient
     * @return OAuthClient
     */
    public function saveOAuthClient(OAuthClient $oAuthClient): OAuthClient
    {
        $this->persist($oAuthClient);
        return $oAuthClient;
    }

    /**
     * @param OAuthClientSearchFilterParams $oAuthClientSearchFilterParams
     * @return array
     */
    public function getOAuthClientList(OAuthClientSearchFilterParams $oAuthClientSearchFilterParams): array
    {
        $qb = $this->getOAuthClientQueryBuilderWrapper($oAuthClientSearchFilterParams)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param OAuthClientSearchFilterParams $oAuthClientSearchFilterParams
     * @return int
     */
    public function getOAuthClientCount(OAuthClientSearchFilterParams $oAuthClientSearchFilterParams): int
    {
        $qb = $this->getOAuthClientQueryBuilderWrapper($oAuthClientSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param OAuthClientSearchFilterParams $oAuthClientSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getOAuthClientQueryBuilderWrapper(OAuthClientSearchFilterParams $oAuthClientSearchFilterParams): QueryBuilderWrapper
    {
        $q = $this->createQueryBuilder(OAuthClient::class, 'oAuthClient');
        $this->setSortingAndPaginationParams($q, $oAuthClientSearchFilterParams);
        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param int $id
     * @return OAuthClient|null
     */
    public function getOAuthClientById(int $id): ?OAuthClient
    {
        $oAuthClient = $this->getRepository(OAuthClient::class)->find($id);
        if ($oAuthClient instanceof OAuthClient) {
            return $oAuthClient;
        }
        return null;
    }

    /**
     * @param array $ids
     * @return int
     */
    public function deleteOAuthClients(array $ids): int
    {
        $q = $this->createQueryBuilder(OAuthClient::class, 'oAuthClient');
        $q->delete()
            ->where($q->expr()->in('oAuthClient.id', ':ids'))
            ->setParameter('ids', $ids);
        return $q->getQuery()->execute();
    }

    /**
     * Create Mobile OAuth Client
     *
     * @return OAuthClient
     */
    public function createMobileClient()
    {
        $client = new OAuthClient();
        $client->setName(OAuthService::PUBLIC_MOBILE_CLIENT_ID);
        $client->setClientSecret('');
        $client->setRedirectUri('');
        $client->setConfidential(sprintf("%s %s", GrantType::USER_CREDENTIALS, GrantType::REFRESH_TOKEN));
        //$client->setScope(Scope::SCOPE_USER); // TODO
        return $this->saveOAuthClient($client);
    }
}
