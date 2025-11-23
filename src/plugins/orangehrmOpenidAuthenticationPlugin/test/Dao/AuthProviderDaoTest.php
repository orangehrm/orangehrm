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

namespace OrangeHRM\Tests\OpenidAuthentication\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\AuthProviderExtraDetails;
use OrangeHRM\Entity\OpenIdProvider;
use OrangeHRM\OpenidAuthentication\Dao\AuthProviderDao;
use OrangeHRM\OpenidAuthentication\Dto\ProviderSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class AuthProviderDaoTest extends KernelTestCase
{
    use EntityManagerHelperTrait;

    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmOpenidAuthenticationPlugin/test/fixtures/AuthProviderDao.yml';
        TestDataService::populate($fixture);
    }

    public function testGetAuthProviderById(): void
    {
        $authProviderDao = new AuthProviderDao();

        $provider = $authProviderDao->getAuthProviderById(1);
        $this->assertEquals('Google', $provider->getProviderName());
        $this->assertEquals('https://accounts.google.com', $provider->getProviderUrl());
        $this->assertTrue($provider->getStatus());
        $this->assertInstanceOf(OpenIdProvider::class, $provider);
    }

    public function testGetAuthProviderDetailsByProviderId(): void
    {
        $authProviderDao = new AuthProviderDao();

        $authProviderDetails = $authProviderDao->getAuthProviderDetailsByProviderId(2);
        $this->assertEquals('https://github.com/', $authProviderDetails->getOpenIdProvider()->getProviderUrl());
        $this->assertEquals('Github', $authProviderDetails->getOpenIdProvider()->getProviderName());
        $this->assertEquals('ba60e45e8180569fcf', $authProviderDetails->getClientId());
        $this->assertEquals('0f0CC6f2182cc5083a84083baf5f43c60952', $authProviderDetails->getClientSecret());
        $this->assertInstanceOf(AuthProviderExtraDetails::class, $authProviderDetails);
    }

    public function testGetAuthProviders(): void
    {
        $authProviderDao = new AuthProviderDao();
        $providerSearchFilterParams = new ProviderSearchFilterParams();

        $providerSearchFilterParams->setId(null);
        $providerSearchFilterParams->setName(null);
        $providerSearchFilterParams->setStatus(null);
        $providerSearchFilterParams->setLimit(1);
        $providerSearchFilterParams->setOffset(1);

        $authProviders = $authProviderDao->getAuthProviders($providerSearchFilterParams);
        $this->assertIsArray($authProviders);
        $this->assertEquals('ba60e45e8180569fcf', $authProviders[0]->getClientId());
        $this->assertEquals('0f0CC6f2182cc5083a84083baf5f43c60952', $authProviders[0]->getClientSecret());
    }

    public function testGetAuthProviderCount(): void
    {
        $authProviderDao = new AuthProviderDao();
        $providerSearchFilterParams = new ProviderSearchFilterParams();

        $providerSearchFilterParams->setId(null);
        $providerSearchFilterParams->setName(null);
        $providerSearchFilterParams->setStatus(null);
        $count = $authProviderDao->getAuthProviderCount($providerSearchFilterParams);

        $this->assertEquals('2', $count);
        $this->assertIsInt($count);
    }

    public function testDeleteProviders(): void
    {
        $authProviderDao = new AuthProviderDao();

        $id = [1];
        $authProviderDao->deleteProviders($id);
        $provider = $this->getRepository(OpenIdProvider::class)->find(1);
        $this->assertFalse($provider->getStatus());
    }

    public function testSaveProvider(): void
    {
        $authProviderDao = new AuthProviderDao();
        $openIdProvider = new OpenIdProvider();

        $openIdProvider->setProviderName('Keycloak');
        $openIdProvider->setProviderUrl('https://keycloak.auth.com');
        $openIdProvider->setStatus(1);
        $result = $authProviderDao->saveProvider($openIdProvider);
        $this->assertInstanceOf(OpenIdProvider::class, $result);
        $this->assertEquals('Keycloak', $result->getProviderName());
    }

    public function testSaveAuthProviderExtraDetails(): void
    {
        $openIdProvider = new OpenIdProvider();
        $authProviderDao = new AuthProviderDao();
        $providerDetails = new AuthProviderExtraDetails();

        $openIdProvider->setProviderName('Google Auth');
        $openIdProvider->setProviderUrl('https://google.auth.com');
        $openIdProvider->setStatus(1);
        $this->persist($openIdProvider);

        $providerDetails->setClientId('445659888050');
        $providerDetails->setClientSecret('a0n4aisrubg8l4gsb35si9gni9l6t0hn');
        $providerDetails->setOpenIdProvider($openIdProvider);

        $result = $authProviderDao->saveAuthProviderExtraDetails($providerDetails);
        $this->assertInstanceOf(AuthProviderExtraDetails::class, $result);
        $this->assertEquals('Google Auth', $result->getOpenIdProvider()->getProviderName());
    }

    public function testGetAuthProvidersForLoginPage(): void
    {
        $authProviderDao = new AuthProviderDao();
        $result = $authProviderDao->getAuthProvidersForLoginPage();

        $this->assertIsArray($result);
        $this->assertEquals('Google', $result[0]->getProviderName());
    }
}
