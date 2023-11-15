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

/**
 * Description of AuthenticationProviderDaoTest
 * 
 * @group AuthenticationProvider
 * @group openidauth
 */
class AuthenticationProviderDaoTest extends PHPUnit_Framework_TestCase {

    private $authenticationDao;
    
    protected function setUp() {
        $this->authenticationDao = new AuthProviderExtraDetailsDao();
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmOpenidAuthenticationPlugin/test/fixtures/AuthenticationProviders.yml');
    }
    
    public function testGetJVProviderByProviderId(){
        $providerId = 1;
        $result = $this->authenticationDao->getAuthProviderDetailsByProviderId($providerId);
        $this->assertTrue($result instanceof AuthProviderExtraDetails);
    }
    
    public function testSaveJVAuthProviderOpenId(){
        $authProvider = new AuthProviderExtraDetails();
        
        $authProvider->setProviderId(3);
        $authProvider->setProviderType(1);
        $result = $this->authenticationDao->saveAuthProviderExtraDetails($authProvider);
        $this->assertTrue($result instanceof AuthProviderExtraDetails);
    }
    
    public function testSaveJVAuthProviderNotOpenId(){
        $authProvider = new AuthProviderExtraDetails();
        
        $authProvider->setProviderId(5);
        $authProvider->setProviderType(2);
        $authProvider->setClientId('Test_client_id_4');
        $authProvider->setClientSecret('Test_secret_4');
        $authProvider->setDeveloperKey('Test_developer_key');
        
        $result = $this->authenticationDao->saveAuthProviderExtraDetails($authProvider);
        $this->assertTrue($result instanceof AuthProviderExtraDetails);
        
    }

}
