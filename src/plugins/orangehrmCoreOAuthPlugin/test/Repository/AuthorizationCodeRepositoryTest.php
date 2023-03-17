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

        $this->expectErrorMessage(
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

        $oauthAuthCode = $this->getEntityReference(OAuthAuthorizationCode::class, 2);
        $this->assertEquals($authCodeEntity->getIdentifier(), $oauthAuthCode->getAuthCode());
        $this->assertEquals($authCodeEntity->getUserIdentifier(), $oauthAuthCode->getUserId());
        $this->assertEquals($authCodeEntity->getRedirectUri(), $oauthAuthCode->getRedirectUri());
        $this->assertEquals('2023-03-16 10:18:14', $oauthAuthCode->getExpiryDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(2, $oauthAuthCode->getId());
        $this->assertEquals($clientEntity->getIdentifier(), $oauthAuthCode->getClientId());
        $this->assertFalse($oauthAuthCode->isRevoked());
    }
}
