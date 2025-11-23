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
use Error;
use DateTimeImmutable;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\OAuthAuthorizationCode;
use OrangeHRM\Framework\Services;
use OrangeHRM\OAuth\Dto\Entity\AuthCodeEntity;
use OrangeHRM\OAuth\Dto\Entity\ClientEntity;
use OrangeHRM\OAuth\Repository\AuthorizationCodeRepository;
use OrangeHRM\OAuth\Server\OAuthServer;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group OAuth
 * @group Dao
 */
class AuthorizationCodeRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmCoreOAuthPlugin/test/fixtures/AuthorizationCodeRepositoryTest.yaml';
        TestDataService::populate($fixture);
    }

    public function testGetNewAuthCode(): void
    {
        $authorizationCodeRepository = new AuthorizationCodeRepository();
        $authCodeEntity = $authorizationCodeRepository->getNewAuthCode();
        $this->assertInstanceOf(AuthCodeEntity::class, $authCodeEntity);
        $this->assertNull($authCodeEntity->getRedirectUri());
        $this->assertNull($authCodeEntity->getUserIdentifier());

        //https://github.com/sebastianbergmann/phpunit/issues/5062
        $this->expectException(Error::class);
        $this->expectExceptionMessage(
            'Typed property OrangeHRM\OAuth\Dto\Entity\AuthCodeEntity::$identifier must not be accessed before initialization'
        );
        $this->assertNull($authCodeEntity->getIdentifier());
    }

    public function testPersistNewAuthCode(): void
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

        $clientEntity = new ClientEntity(
            1,
            'orangehrm_mobile_app',
            'com.orangehrm.opensource://oauthCallback',
            false,
            'Mobile App'
        );

        $authCodeEntity = new AuthCodeEntity();
        $authCodeEntity->setIdentifier(
            '80316659039e40938f034098731a097029d9ef96b06b69a14763607f9f8b890c28022679c50365ec'
        );
        $authCodeEntity->setUserIdentifier(2);
        $authCodeEntity->setClient($clientEntity);
        $authCodeEntity->setExpiryDateTime(new DateTimeImmutable());
        $authCodeEntity->setRedirectUri('com.orangehrm.opensource://oauthCallback');
        $authorizationCodeRepository = new AuthorizationCodeRepository();
        $authorizationCodeRepository->persistNewAuthCode($authCodeEntity);

        $lastId = 3;
        $oauthAuthCode = $this->getEntityReference(OAuthAuthorizationCode::class, $lastId);
        $this->assertEquals($authCodeEntity->getIdentifier(), $oauthAuthCode->getAuthCode());
        $this->assertEquals($authCodeEntity->getUserIdentifier(), $oauthAuthCode->getUserId());
        $this->assertEquals($authCodeEntity->getRedirectUri(), $oauthAuthCode->getRedirectUri());
        $this->assertEquals('2023-03-16 10:18:14', $oauthAuthCode->getExpiryDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals($lastId, $oauthAuthCode->getId());
        $this->assertEquals($clientEntity->getIdentifier(), $oauthAuthCode->getClientId());
        $this->assertFalse($oauthAuthCode->isRevoked());
    }

    public function testPersistNewAuthCodeWithExistingAuthCode(): void
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

        $clientEntity = new ClientEntity(
            1,
            'orangehrm_mobile_app',
            'com.orangehrm.opensource://oauthCallback',
            false,
            'Mobile App'
        );

        $authCodeEntity = new AuthCodeEntity();
        $authCodeEntity->setIdentifier(
            '4527715fe2b6a045bcf4036d6d55e6b3c9e0dd64aae3fcbc7ec50b1fe43d39fb0670fcbaf12d46c4'
        );
        $authCodeEntity->setUserIdentifier(4);
        $authCodeEntity->setClient($clientEntity);
        $authCodeEntity->setExpiryDateTime(new DateTimeImmutable());
        $authCodeEntity->setRedirectUri('com.orangehrm.opensource://oauthCallback');
        $authorizationCodeRepository = new AuthorizationCodeRepository();

        $this->expectException(UniqueTokenIdentifierConstraintViolationException::class);
        $authorizationCodeRepository->persistNewAuthCode($authCodeEntity);
    }

    public function testRevokeAuthCode(): void
    {
        $authCode = $this->getEntityReference(OAuthAuthorizationCode::class, 1);
        $this->assertEquals(
            '4527715fe2b6a045bcf4036d6d55e6b3c9e0dd64aae3fcbc7ec50b1fe43d39fb0670fcbaf12d46c4',
            $authCode->getAuthCode()
        );
        $this->assertFalse($authCode->isRevoked()); // validate state before revoke the code

        $authorizationCodeRepository = new AuthorizationCodeRepository();
        $authorizationCodeRepository->revokeAuthCode(
            '4527715fe2b6a045bcf4036d6d55e6b3c9e0dd64aae3fcbc7ec50b1fe43d39fb0670fcbaf12d46c4'
        );

        $this->getEntityManager()->clear();
        $authCode = $this->getEntityReference(OAuthAuthorizationCode::class, 1);
        $this->assertEquals(
            '4527715fe2b6a045bcf4036d6d55e6b3c9e0dd64aae3fcbc7ec50b1fe43d39fb0670fcbaf12d46c4',
            $authCode->getAuthCode()
        );
        $this->assertTrue($authCode->isRevoked());
    }

    public function testRevokeNonExistingAuthCode(): void
    {
        $authorizationCodeRepository = new AuthorizationCodeRepository();
        $authorizationCodeRepository->revokeAuthCode('this-auth-code-not-exist');
        $this->assertTrue(true); // silently continue even if no row affected
    }

    public function testIsAuthCodeRevoked(): void
    {
        $authorizationCodeRepository = new AuthorizationCodeRepository();
        $revoked = $authorizationCodeRepository->isAuthCodeRevoked(
            '4527715fe2b6a045bcf4036d6d55e6b3c9e0dd64aae3fcbc7ec50b1fe43d39fb0670fcbaf12d46c4'
        );
        $this->assertFalse($revoked);

        $revoked = $authorizationCodeRepository->isAuthCodeRevoked(
            'ebecfb310f7ea082cd8a256dfac7a1bf9ddb9d6774d4e653abccbc5fed955c07be8517c839b6b095'
        );
        $this->assertTrue($revoked);
    }

    public function testIsAuthCodeRevokedForNonExistingAuthCode(): void
    {
        $authorizationCodeRepository = new AuthorizationCodeRepository();
        $revoked = $authorizationCodeRepository->isAuthCodeRevoked('this-auth-code-not-exist');
        $this->assertTrue($revoked);
    }
}
