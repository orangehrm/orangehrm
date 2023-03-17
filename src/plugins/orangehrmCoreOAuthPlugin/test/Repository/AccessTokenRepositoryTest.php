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

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use OrangeHRM\OAuth\Dto\Entity\ClientEntity;
use OrangeHRM\OAuth\Repository\AccessTokenRepository;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group OAuth
 * @group Dao
 */
class AccessTokenRepositoryTest extends KernelTestCase
{
    private AccessTokenRepository $accessTokenRepository;

    protected function setUp(): void
    {
        $this->accessTokenRepository = new AccessTokenRepository();
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
        $clientEntity = new ClientEntity(
            1,
            '53b5a6391a832005027a51e50b8fb8c9',
            'https://example.com/callback',
            false,
            'Client App'
        );
        $token = $this->accessTokenRepository->getNewToken($clientEntity, [], 100);
        $this->expectErrorMessage(
            'Typed property OrangeHRM\OAuth\Dto\Entity\AccessTokenEntity::$expiryDateTime must not be accessed before initialization'
        );
        $token->getExpiryDateTime();
    }

    public function testGetNewTokenAndCallGetIdentifier(): void
    {
        $clientEntity = new ClientEntity(
            1,
            '53b5a6391a832005027a51e50b8fb8c9',
            'https://example.com/callback',
            false,
            'Client App'
        );
        $token = $this->accessTokenRepository->getNewToken($clientEntity, [], 100);
        $this->expectErrorMessage(
            'Typed property OrangeHRM\OAuth\Dto\Entity\AccessTokenEntity::$identifier must not be accessed before initialization'
        );
        $token->getIdentifier();
    }
}
