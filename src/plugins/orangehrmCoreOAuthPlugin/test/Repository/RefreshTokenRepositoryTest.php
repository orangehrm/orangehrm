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

namespace OrangeHRM\Tests\OAuth\Repository;

use DateTime;
use DateTimeImmutable;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\OAuthAccessToken;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\Entity\OAuthRefreshToken;
use OrangeHRM\Framework\Services;
use OrangeHRM\OAuth\Dto\Entity\AccessTokenEntity;
use OrangeHRM\OAuth\Dto\Entity\ClientEntity;
use OrangeHRM\OAuth\Dto\Entity\RefreshTokenEntity;
use OrangeHRM\OAuth\Repository\RefreshTokenRepository;
use OrangeHRM\OAuth\Server\OAuthServer;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group OAuth
 * @group Dao
 */
class RefreshTokenRepositoryTest extends KernelTestCase
{
    private RefreshTokenRepository $refreshTokenRepository;

    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmCoreOAuthPlugin/test/fixtures/RefreshTokenRepositoryTest.yaml';
        TestDataService::populate($fixture);
        $this->refreshTokenRepository = new RefreshTokenRepository();
    }

    public function testGetNewRefreshToken(): void
    {
        $this->assertInstanceOf(RefreshTokenEntity::class, $this->refreshTokenRepository->getNewRefreshToken());
    }

    public function testPersistNewAccessToken(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->exactly(2))
            ->method('getNow')
            ->willReturn(new DateTime('2023-03-16 10:16:14'));
        $this->createKernelWithMockServices([
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::OAUTH_SERVER => new OAuthServer(),
            Services::CONFIG_SERVICE => new ConfigService(),
        ]);

        $clientEntity = ClientEntity::createFromEntity($this->getEntityReference(OAuthClient::class, 1));

        $accessTokenEntity = new AccessTokenEntity();
        $accessTokenEntity->setIdentifier('random_id');
        $accessTokenEntity->setUserIdentifier(5);
        $accessTokenEntity->setClient($clientEntity);
        $accessTokenEntity->setExpiryDateTime(new DateTimeImmutable());

        $refreshToken = $this->refreshTokenRepository->getNewRefreshToken();
        $refreshToken->setAccessToken($accessTokenEntity);
        $refreshToken->setIdentifier('388b9ae63e03f');
        $refreshToken->setExpiryDateTime(new DateTimeImmutable());

        $this->refreshTokenRepository->persistNewRefreshToken($refreshToken);

        $lastId = 3;
        $token = $this->getEntityReference(OAuthRefreshToken::class, $lastId);
        $this->assertEquals($lastId, $token->getId());
        $this->assertEquals('388b9ae63e03f', $token->getRefreshToken());
        $this->assertEquals('random_id', $token->getAccessToken());
        $this->assertEquals('2023-05-16 10:16:14', $token->getExpiryDateTime()->format('Y-m-d H:i:s'));
        $this->assertFalse($token->isRevoked());
    }

    public function testPersistNewAccessTokenWithExistingAccessToken(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->exactly(2))
            ->method('getNow')
            ->willReturn(new DateTime('2023-03-16 10:16:14'));
        $this->createKernelWithMockServices([
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::OAUTH_SERVER => new OAuthServer(),
            Services::CONFIG_SERVICE => new ConfigService(),
        ]);

        $accessTokenEntity = new AccessTokenEntity();
        $accessTokenEntity->setIdentifier('random_id');
        $accessTokenEntity->setExpiryDateTime(new DateTimeImmutable());

        $token = $this->refreshTokenRepository->getNewRefreshToken();
        $token->setAccessToken($accessTokenEntity);
        $token->setIdentifier('9fac7adebd19b01f53a69ba5c9898d283dacda7296b8063899312ff599b4ccf240fd92dd033dc768');
        $token->setExpiryDateTime(new DateTimeImmutable());

        $this->expectException(UniqueTokenIdentifierConstraintViolationException::class);
        $this->refreshTokenRepository->persistNewRefreshToken($token);
    }

    public function testRevokeAccessToken(): void
    {
        $id = 2;
        $token = '9fac7adebd19b01f53a69ba5c9898d283dacda7296b8063899312ff599b4ccf240fd92dd033dc768';
        $refreshToken = $this->getEntityReference(OAuthRefreshToken::class, $id);
        $this->assertEquals($token, $refreshToken->getRefreshToken());
        $this->assertFalse($refreshToken->isRevoked()); // validate state before revoke the code

        $this->refreshTokenRepository->revokeRefreshToken($token);

        $this->getEntityManager()->clear();
        $refreshToken = $this->getEntityReference(OAuthRefreshToken::class, $id);
        $this->assertEquals($token, $refreshToken->getRefreshToken());
        $this->assertTrue($refreshToken->isRevoked());
    }

    public function testRevokeAccessTokenWhichAlreadyRevoked(): void
    {
        $id = 1;
        $token = '571420fd098500e2e14023c1a088f7cecc4ad285a35c1efbae96bfb1e76630c4e56c244b61b52825';
        $refreshToken = $this->getEntityReference(OAuthRefreshToken::class, $id);
        $this->assertEquals($token, $refreshToken->getRefreshToken());
        $this->assertTrue($refreshToken->isRevoked()); // validate state before revoke the code

        $this->refreshTokenRepository->revokeRefreshToken($token); // should not change revoked state after execution

        $this->getEntityManager()->clear();
        $refreshToken = $this->getEntityReference(OAuthRefreshToken::class, $id);
        $this->assertEquals($token, $refreshToken->getRefreshToken());
        $this->assertTrue($refreshToken->isRevoked());
    }

    public function testRevokeNonExistingAccessToken(): void
    {
        $token = 'this-refresh-token-not-exist';
        $accessToken = $this->getEntityManager()
            ->getRepository(OAuthAccessToken::class)
            ->findOneBy(['accessToken' => $token]);
        $this->assertNull($accessToken);

        $this->refreshTokenRepository->revokeRefreshToken($token);
        // silently continue even if no row affected
    }

    public function testIsAccessTokenRevoked(): void
    {
        $revoked = $this->refreshTokenRepository->isRefreshTokenRevoked(
            '571420fd098500e2e14023c1a088f7cecc4ad285a35c1efbae96bfb1e76630c4e56c244b61b52825'
        );
        $this->assertTrue($revoked);

        $revoked = $this->refreshTokenRepository->isRefreshTokenRevoked(
            '9fac7adebd19b01f53a69ba5c9898d283dacda7296b8063899312ff599b4ccf240fd92dd033dc768'
        );
        $this->assertFalse($revoked);
    }

    public function testIsAccessTokenRevokedForNonExistingAccessToken(): void
    {
        $revoked = $this->refreshTokenRepository->isRefreshTokenRevoked('this-refresh-token-not-exist');
        $this->assertTrue($revoked);
    }
}
