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

namespace OrangeHRM\Tests\OAuth\Repository;

use DateTime;
use DateTimeImmutable;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\OAuthAccessToken;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\Framework\Services;
use OrangeHRM\OAuth\Dto\Entity\ClientEntity;
use OrangeHRM\OAuth\Repository\AccessTokenRepository;
use OrangeHRM\OAuth\Server\OAuthServer;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group OAuth
 * @group Dao
 */
class AccessTokenRepositoryTest extends KernelTestCase
{
    use CryptTrait;

    private AccessTokenRepository $accessTokenRepository;

    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmCoreOAuthPlugin/test/fixtures/AccessTokenRepositoryTest.yaml';
        TestDataService::populate($fixture);
        $this->accessTokenRepository = new AccessTokenRepository();
        $this->encryptionKey = 'pPE/4/7tPIsha317zoTA+945UjJzdRFbCBtYUNrR7x8=';
        $this->accessTokenRepository->setEncryptionKey($this->encryptionKey);
    }

    public function testGetNewToken(): void
    {
        $clientEntity = new ClientEntity(
            1,
            '53b5a6391a832005027a51e50b8fb8c9',
            'https://example.com/callback',
            false,
            'Client App'
        );
        $token = $this->accessTokenRepository->getNewToken($clientEntity, [], 100);
        $this->assertEquals(100, $token->getUserIdentifier());
        $this->assertCount(0, $token->getScopes());
        $this->assertEquals(1, $token->getClient()->getIdentifier());
        $this->assertEquals('53b5a6391a832005027a51e50b8fb8c9', $token->getClient()->getName());
        $this->assertEquals('https://example.com/callback', $token->getClient()->getRedirectUri());
        $this->assertFalse($token->getClient()->isConfidential());

        $token->setIdentifier('388b9ae63e03fafa079ef891142828dc869e5b5f67c6094614492259ce95794e3f18a9057b667eae');
        $this->assertEquals(
            '388b9ae63e03fafa079ef891142828dc869e5b5f67c6094614492259ce95794e3f18a9057b667eae',
            $this->decrypt($token->__toString())
        );
    }

    public function testGetNewTokenWithScopes(): void
    {
        $clientEntity = new ClientEntity(
            1,
            '53b5a6391a832005027a51e50b8fb8c9',
            'https://example.com/callback',
            false,
            'Client App'
        );
        $token = $this->accessTokenRepository->getNewToken($clientEntity, [
            new class () implements ScopeEntityInterface {
                /**
                 * @inheritDoc
                 */
                public function getIdentifier(): string
                {
                    return 'profile';
                }

                /**
                 * @inheritDoc
                 */
                public function jsonSerialize()
                {
                    return 'profile';
                }
            }
        ], 100);
        $this->assertEquals(100, $token->getUserIdentifier());
        $this->assertEquals('["profile"]', json_encode($token->getScopes()));
        $this->assertEquals('53b5a6391a832005027a51e50b8fb8c9', $token->getClient()->getName());
    }

    public function testGetNewTokenAndCallGetExpiryDateTime(): void
    {
        $clientEntity = ClientEntity::createFromEntity($this->getEntityReference(OAuthClient::class, 1));
        $token = $this->accessTokenRepository->getNewToken($clientEntity, [], 100);
        $this->expectErrorMessage(
            'Typed property OrangeHRM\OAuth\Dto\Entity\AccessTokenEntity::$expiryDateTime must not be accessed before initialization'
        );
        $token->getExpiryDateTime();
    }

    public function testGetNewTokenAndCallGetIdentifier(): void
    {
        $clientEntity = ClientEntity::createFromEntity($this->getEntityReference(OAuthClient::class, 1));
        $token = $this->accessTokenRepository->getNewToken($clientEntity, [], 100);
        $this->expectErrorMessage(
            'Typed property OrangeHRM\OAuth\Dto\Entity\AccessTokenEntity::$identifier must not be accessed before initialization'
        );
        $token->getIdentifier();
    }

    public function testPersistNewAccessToken(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->once())
            ->method('getNow')
            ->willReturn(new DateTime('2023-03-16 10:16:14'));
        $this->createKernelWithMockServices([
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::OAUTH_SERVER => new OAuthServer(),
            Services::CONFIG_SERVICE => new ConfigService(),
        ]);

        $clientEntity = ClientEntity::createFromEntity($this->getEntityReference(OAuthClient::class, 4));
        $token = $this->accessTokenRepository->getNewToken($clientEntity, [], 100);
        $token->setIdentifier('388b9ae63e03f');
        $token->setExpiryDateTime(new DateTimeImmutable());

        $this->accessTokenRepository->persistNewAccessToken($token);

        $lastId = 3;
        $accessToken = $this->getEntityReference(OAuthAccessToken::class, $lastId);
        $this->assertEquals($lastId, $accessToken->getId());
        $this->assertEquals('388b9ae63e03f', $accessToken->getAccessToken());
        $this->assertEquals('2023-03-16 10:31:14', $accessToken->getExpiryDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(100, $accessToken->getUserId());
        $this->assertEquals(4, $accessToken->getClientId());
        $this->assertFalse($accessToken->isRevoked());
    }

    public function testPersistNewAccessTokenWithExistingAccessToken(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->once())
            ->method('getNow')
            ->willReturn(new DateTime('2023-03-16 10:16:14'));
        $this->createKernelWithMockServices([
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::OAUTH_SERVER => new OAuthServer(),
            Services::CONFIG_SERVICE => new ConfigService(),
        ]);

        $clientEntity = ClientEntity::createFromEntity($this->getEntityReference(OAuthClient::class, 4));
        $token = $this->accessTokenRepository->getNewToken($clientEntity, [], 100);
        $token->setIdentifier('05a81084f60f6440c8bd2555200836584e365210aee54ffee8e9dc04c7ec0068a6cde45ef5999e47');
        $token->setExpiryDateTime(new DateTimeImmutable());

        $this->expectException(UniqueTokenIdentifierConstraintViolationException::class);
        $this->accessTokenRepository->persistNewAccessToken($token);
    }

    public function testRevokeAccessToken(): void
    {
        $id = 2;
        $token = '388b9ae63e03fafa079ef891142828dc869e5b5f67c6094614492259ce95794e3f18a9057b667eae';
        $accessToken = $this->getEntityReference(OAuthAccessToken::class, $id);
        $this->assertEquals($token, $accessToken->getAccessToken());
        $this->assertFalse($accessToken->isRevoked()); // validate state before revoke the code

        $this->accessTokenRepository->revokeAccessToken($token);

        $this->getEntityManager()->clear();
        $accessToken = $this->getEntityReference(OAuthAccessToken::class, $id);
        $this->assertEquals($token, $accessToken->getAccessToken());
        $this->assertTrue($accessToken->isRevoked());
    }

    public function testRevokeAccessTokenWhichAlreadyRevoked(): void
    {
        $id = 1;
        $token = '05a81084f60f6440c8bd2555200836584e365210aee54ffee8e9dc04c7ec0068a6cde45ef5999e47';
        $accessToken = $this->getEntityReference(OAuthAccessToken::class, $id);
        $this->assertEquals($token, $accessToken->getAccessToken());
        $this->assertTrue($accessToken->isRevoked()); // validate state before revoke the code

        $this->accessTokenRepository->revokeAccessToken($token); // should not change revoked state after execution

        $this->getEntityManager()->clear();
        $accessToken = $this->getEntityReference(OAuthAccessToken::class, $id);
        $this->assertEquals($token, $accessToken->getAccessToken());
        $this->assertTrue($accessToken->isRevoked());
    }

    public function testRevokeNonExistingAccessToken(): void
    {
        $token = 'this-access-token-not-exist';
        $accessToken = $this->getEntityManager()
            ->getRepository(OAuthAccessToken::class)
            ->findOneBy(['accessToken' => $token]);
        $this->assertNull($accessToken);

        $this->accessTokenRepository->revokeAccessToken($token);
        // silently continue even if no row affected
    }

    public function testIsAccessTokenRevoked(): void
    {
        $revoked = $this->accessTokenRepository->isAccessTokenRevoked(
            '05a81084f60f6440c8bd2555200836584e365210aee54ffee8e9dc04c7ec0068a6cde45ef5999e47'
        );
        $this->assertTrue($revoked);

        $revoked = $this->accessTokenRepository->isAccessTokenRevoked(
            '388b9ae63e03fafa079ef891142828dc869e5b5f67c6094614492259ce95794e3f18a9057b667eae'
        );
        $this->assertFalse($revoked);
    }

    public function testIsAccessTokenRevokedForNonExistingAccessToken(): void
    {
        $revoked = $this->accessTokenRepository->isAccessTokenRevoked('this-access-token-not-exist');
        $this->assertFalse($revoked);
        // Invalid state, should check whether access token exist before execute this method
    }
}
