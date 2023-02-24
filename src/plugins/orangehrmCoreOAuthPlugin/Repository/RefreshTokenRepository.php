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

use Exception;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
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
        $this->persist($refreshToken);
    }

    /**
     * @inheritdoc
     */
    public function revokeRefreshToken($tokenId): void
    {
        throw new Exception(__METHOD__);
    }

    /**
     * @inheritdoc
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        throw new Exception(__METHOD__);
    }

    /**
     * @inheritdoc
     */
    public function getNewRefreshToken(): ?RefreshTokenEntityInterface
    {
        return new RefreshTokenEntity();
    }
}
