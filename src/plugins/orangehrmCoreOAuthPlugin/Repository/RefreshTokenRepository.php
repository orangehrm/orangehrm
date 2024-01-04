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
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\OAuthRefreshToken;
use OrangeHRM\OAuth\Dto\Entity\RefreshTokenEntity;

class RefreshTokenRepository extends BaseDao implements RefreshTokenRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $refreshToken = new OAuthRefreshToken();
        $refreshToken->setRefreshToken($refreshTokenEntity->getIdentifier());
        $refreshToken->setAccessToken($refreshTokenEntity->getAccessToken()->getIdentifier());
        $refreshToken->setExpiryDateTime($refreshTokenEntity->getExpiryDateTime());

        try {
            $this->persist($refreshToken);
        } catch (UniqueConstraintViolationException $e) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }
    }

    /**
     * @inheritdoc
     */
    public function revokeRefreshToken($tokenId): void
    {
        $this->createQueryBuilder(OAuthRefreshToken::class, 'refreshToken')
            ->update()
            ->set('refreshToken.revoked', ':revoked')
            ->setParameter('revoked', true)
            ->andWhere('refreshToken.refreshToken = :refreshToken')
            ->setParameter('refreshToken', $tokenId)
            ->getQuery()
            ->execute();
    }

    /**
     * @inheritdoc
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        /** @var OAuthRefreshToken|null $refreshToken */
        $refreshToken = $this->getRepository(OAuthRefreshToken::class)
            ->findOneBy(['refreshToken' => $tokenId]);
        if (!$refreshToken instanceof OAuthRefreshToken) {
            return true;
        }

        return $refreshToken->isRevoked();
    }

    /**
     * @inheritdoc
     */
    public function getNewRefreshToken(): ?RefreshTokenEntityInterface
    {
        return new RefreshTokenEntity();
    }
}
