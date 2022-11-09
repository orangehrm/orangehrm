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

namespace OrangeHRM\OAuth\Service;

use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Dao\OAuthClientDao;

class OAuthService
{
    public const PUBLIC_MOBILE_CLIENT_ID = 'orangehrm_mobile_app';
    /**
     * @var OAuthClientDao|null
     */
    private ?OAuthClientDao $oAuthClientDao = null;

    /**
     * @return OAuthClientDao
     */
    public function getOAuthClientDao(): OAuthClientDao
    {
        if (!($this->oAuthClientDao instanceof OAuthClientDao)) {
            $this->oAuthClientDao = new OAuthClientDao();
        }
        return $this->oAuthClientDao;
    }

    /**
     * @param OAuthClientDao $oAuthClientDao
     */
    public function setOAuthClientDao(OAuthClientDao $oAuthClientDao): void
    {
        $this->oAuthClientDao = $oAuthClientDao;
    }

    /**
     * Will return the OAuthClient doctrine object for a particular id
     *
     * @param string $oAuthClientId
     * @return OAuthClient|null
     * @throws DaoException
     */
    public function getOAuthClientByClientId(string $oAuthClientId): ?OAuthClient
    {
        return $this->getOAuthClientDao()->getOAuthClientByClientId($oAuthClientId);
    }

    /**
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteOAuthClients(array $toDeleteIds): int
    {
        return $this->getOAuthClientDao()->deleteOAuthClients($toDeleteIds);
    }

    /**
     * @param OAuthClient $authClient
     * @return OAuthClient
     * @throws DaoException
     */
    public function saveOAuthClient(OAuthClient $authClient): OAuthClient
    {
        return $this->getOAuthClientDao()->saveOAuthClient($authClient);
    }

    /**
     * Create OAuth mobile client
     *
     * @return OAuthClient
     */
    public function createMobileClient()
    {
        return $this->getOAuthClientDao()->createMobileClient();
    }
}
