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

namespace OrangeHRM\Tests\OAuth\Dto\Entity;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Framework\Services;
use OrangeHRM\OAuth\Dto\Entity\AuthCodeEntity;
use OrangeHRM\OAuth\Dto\Entity\ClientEntity;
use OrangeHRM\OAuth\Dto\Entity\ScopeEntity;
use OrangeHRM\OAuth\Server\OAuthServer;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group OAuth
 * @group Entity
 */
class AuthCodeEntityTest extends KernelTestCase
{
    public function testEntity(): void
    {
        $oauthServer = $this->getMockBuilder(OAuthServer::class)
            ->onlyMethods(['getAuthCodeTTL'])
            ->getMock();
        $oauthServer->expects($this->atLeastOnce())
            ->method('getAuthCodeTTL')
            ->willReturnCallback(fn () => new DateInterval('PT5M'));
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->atLeastOnce())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2024-02-29 23:59:59'));
        $this->createKernelWithMockServices([
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::OAUTH_SERVER => $oauthServer
        ]);
        $clientEntity = new ClientEntity(5, 'client-1', 'https://example.org/callback', false, 'Client');
        $scopeEntity = new ScopeEntity('root');

        $authCodeEntity = new AuthCodeEntity();
        $authCodeEntity->setUserIdentifier(10);
        $authCodeEntity->setExpiryDateTime(new DateTimeImmutable('2023-04-28 23:59:59'));
        $authCodeEntity->setIdentifier('qwertyui');
        $authCodeEntity->setRedirectUri('https://example.org/callback');
        $authCodeEntity->setClient($clientEntity);
        $authCodeEntity->addScope($scopeEntity);

        $this->assertEquals(10, $authCodeEntity->getUserIdentifier());
        $this->assertEquals('2024-03-01 00:04:59', $authCodeEntity->getExpiryDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals('qwertyui', $authCodeEntity->getIdentifier());
        $this->assertEquals('https://example.org/callback', $authCodeEntity->getRedirectUri());
        $this->assertEquals(5, $authCodeEntity->getClient()->getIdentifier());
        $this->assertEquals('client-1', $authCodeEntity->getClient()->getName());
        $this->assertEquals('root', $authCodeEntity->getScopes()[0]->getIdentifier());
    }
}
