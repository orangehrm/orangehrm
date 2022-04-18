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

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Constant\GrantType;
use OrangeHRM\OAuth\Constant\Scope;
use OrangeHRM\OAuth\Dto\OAuthClientSearchFilterParams;
use OrangeHRM\OAuth\Service\OAuthService;
use OrangeHRM\ORM\Paginator;

class OAuthClientDao extends BaseDao
{
    /**
     * @param OAuthClient $oAuthClient
     * @return OAuthClient
     * @throws DaoException
     */
    public function saveOAuthClient(OAuthClient $oAuthClient): OAuthClient
    {
        try {
            $this->persist($oAuthClient);
            return $oAuthClient;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param OAuthClientSearchFilterParams $authClientSearchFilterParams
     * @return OAuthClient[]
     * @throws DaoException
     */
    public function getOAuthClients(OAuthClientSearchFilterParams $authClientSearchFilterParams): array
    {
        try {
            return $this->getOAuthClientsPaginator($authClientSearchFilterParams)->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param OAuthClientSearchFilterParams $authClientSearchFilterParams
     * @return int
     * @throws DaoException
     */
    public function getOAuthClientsCount(OAuthClientSearchFilterParams $authClientSearchFilterParams): int
    {
        try {
            return $this->getOAuthClientsPaginator($authClientSearchFilterParams)->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param OAuthClientSearchFilterParams $authClientSearchFilterParams
     * @return Paginator
     */
    private function getOAuthClientsPaginator(
        OAuthClientSearchFilterParams $authClientSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(OAuthClient::class, 'oc');
        $this->setSortingAndPaginationParams($q, $authClientSearchFilterParams);
        return $this->getPaginator($q);
    }

    /**
     * @param string $oAuthClientId
     * @return OAuthClient|null
     * @throws DaoException
     */
    public function getOAuthClientByClientId(string $oAuthClientId): ?OAuthClient
    {
        try {
            $oAuthClient = $this->getRepository(OAuthClient::class)->find($oAuthClientId);
            if ($oAuthClient instanceof OAuthClient) {
                return $oAuthClient;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteOAuthClients(array $toDeleteIds): int
    {
        try {
            $q = $this->createQueryBuilder(OAuthClient::class, 'oc');
            $q->delete()
                ->where($q->expr()->in('oc.clientId', ':ids'))
                ->setParameter('ids', $toDeleteIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Create Mobile OAuth Client
     *
     * @return OAuthClient
     */
    public function createMobileClient()
    {
        $client = new OAuthClient();
        $client->setClientId(OAuthService::PUBLIC_MOBILE_CLIENT_ID);
        $client->setClientSecret('');
        $client->setRedirectUri('');
        $client->setGrantTypes(sprintf("%s %s", GrantType::USER_CREDENTIALS, GrantType::REFRESH_TOKEN));
        $client->setScope(Scope::SCOPE_USER);
        return $this->saveOAuthClient($client);
    }
}
