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

namespace OrangeHRM\OAuth\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Dto\UserCredentialInterface;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Dto\Entity\ClientEntity;

class ClientRepository extends BaseDao implements ClientRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        $oauthClient = $this->getRepository(OAuthClient::class)->findOneBy(['name' => $clientIdentifier]);
        if (!$oauthClient instanceof OAuthClient) {
            return null;
        }

        return ClientEntity::createFromEntity($oauthClient);
    }

    /**
     * @inheritdoc
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $oauthClient = $this->getRepository(OAuthClient::class)->findOneBy(['name' => $clientIdentifier]);
        if (!$oauthClient instanceof OAuthClient) {
            return false;
        }
        if ($oauthClient->isConfidential() === true && $oauthClient->getClientSecret() === null) {
            // Confidential client must have a secret
            return false;
        }

        if ($grantType === 'refresh_token' && $oauthClient->isConfidential() === false) {
            return true;
        }

        return $this->validateClientSecret($oauthClient, new UserCredential(null, $clientSecret));
    }

    /**
     * @param OAuthClient $client
     * @param UserCredentialInterface $givenCred
     * @return bool
     */
    private function validateClientSecret(OAuthClient $client, UserCredentialInterface $givenCred): bool
    {
        // TODO:: handle encryption or hashing
        return $client->getClientSecret() === $givenCred->getPassword();
    }
}
