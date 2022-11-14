<?php

use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\OAuth2AccessToken;
use OrangeHRM\Entity\OAuth2Client;
use OrangeHRM\Entity\OAuth2RefreshToken;
use OrangeHRM\Entity\OAuth2Scope;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

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
 * Boston, MA 02110-1301, USA
 */

class OAuth2RefreshTokenTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([
            Employee::class,
            User::class,
            UserRole::class,
            OAuth2Scope::class,
            OAuth2Client::class,
            OAuth2AccessToken::class,
            OAuth2RefreshToken::class
        ]);
    }

    public function testOAuth2AccessTokenEntityTest(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Devi');
        $employee->setLastName('DS');
        $this->persist($employee);

        $userRole = new UserRole();
        $userRole->setName('Admin');
        $userRole->setDisplayName('Admin');
        $this->persist($userRole);

        $user = new User();
        $user->setUserName('devi');
        $user->setUserPassword('devi');
        $user->setUserRole($userRole);
        $user->setEmployee($employee);
        $this->persist($user);

        $client = new OAuth2Client();
        $client->setName('Client Name');
        $client->setClientSecret('Client Secret');
        $client->setRedirectUri('redirect.com');
        $client->setIsConfidential(false);
        $this->persist($client);

        $scope = new OAuth2Scope();
        $scope->setScope('Scope 1');
        $scope->setIsDefault(true);
        $this->persist($scope);

        $date = new DateTimeImmutable('2022-11-14');

        $accessToken = new OAuth2AccessToken();
        $accessToken->setAccessToken('Access Token');
        $accessToken->setScopes([$scope]);
        $accessToken->setExpiryDateTime($date);
        $accessToken->setClient($client);
        $accessToken->setUserIdentifier($user->getId());
        $this->persist($accessToken);

        $refreshToken = new OAuth2RefreshToken();
        $refreshToken->setRefreshToken('Refresh Token');
        $refreshToken->setExpiryDateTime($date);
        $refreshToken->setAccessToken($accessToken);
        $this->persist($refreshToken);

        /** @var OAuth2RefreshToken $refreshToken */
        $refreshToken = $this->getRepository(OAuth2RefreshToken::class)->find(1);
        $this->assertEquals('Refresh Token', $refreshToken->getRefreshToken());
        $this->assertEquals($accessToken->getAccessToken(), $refreshToken->getAccessToken()->getAccessToken());
        $this->assertEquals($date, $refreshToken->getExpiryDateTime());
    }
}
