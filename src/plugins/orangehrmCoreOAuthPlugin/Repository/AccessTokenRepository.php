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

namespace OrangeHRM\OAuth\Repository;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\OAuthAccessToken;
use OrangeHRM\OAuth\Dto\Entity\AccessTokenEntity;

class AccessTokenRepository extends BaseDao implements AccessTokenRepositoryInterface
{
    use CryptTrait;

    /**
     * @inheritdoc
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $accessCode = new OAuthAccessToken();
        $accessCode->setAccessToken($accessTokenEntity->getIdentifier());
        $accessCode->setClientId($accessTokenEntity->getClient()->getIdentifier());
        $accessCode->setUserId($accessTokenEntity->getUserIdentifier());
        $accessCode->setExpiryDateTime($accessTokenEntity->getExpiryDateTime());

        try {
            $this->persist($accessCode);
        } catch (UniqueConstraintViolationException $e) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }
    }

    /**
     * @inheritdoc
     */
    public function revokeAccessToken($tokenId): void
    {
        $this->createQueryBuilder(OAuthAccessToken::class, 'accessToken')
            ->update()
            ->set('accessToken.revoked', ':revoked')
            ->setParameter('revoked', true)
            ->andWhere('accessToken.accessToken = :accessToken')
            ->setParameter('accessToken', $tokenId)
            ->getQuery()
            ->execute();
    }

    /**
     * @inheritdoc
     */
    public function isAccessTokenRevoked($tokenId): bool
    {
        $q = $this->createQueryBuilder(OAuthAccessToken::class, 'accessToken')
            ->andWhere('accessToken.revoked = :revoked')
            ->setParameter('revoked', true)
            ->andWhere('accessToken.accessToken = :accessToken')
            ->setParameter('accessToken', $tokenId);
        return $this->getPaginator($q)->count() > 0;
    }

    /**
     * @inheritdoc
     */
    public function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        $userIdentifier = null
    ): AccessTokenEntityInterface {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        $accessToken->setUserIdentifier($userIdentifier);
        $accessToken->setEncryptionKey($this->encryptionKey);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        return $accessToken;
    }

    /**
     * @param string $tokenId
     * @return OAuthAccessToken|null
     */
    public function getAccessToken(string $tokenId): ?OAuthAccessToken
    {
        return $this->getEntityManager()
            ->getRepository(OAuthAccessToken::class)
            ->findOneBy(['accessToken' => $tokenId]);
    }
}
