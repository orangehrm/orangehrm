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
 * Description of AuthenticationProviderServiceTest
 * 
 * @group AuthenticationProvider
 * @group openidauth
 */
class AuthenticationProviderServiceTest extends PHPUnit_Framework_TestCase {

    private $authService;

    protected function setUp() {
        $this->authService = new AuthProviderExtraDetailsService();
    }

    public function testGetJVProviderByProviderId() {
        $authProvider = new AuthProviderExtraDetails();

        $authProvider->setProviderId(5);
        $authProvider->setProviderType(2);
        $authProvider->setClientId('Test_client_id_4');
        $authProvider->setClientSecret('Test_secret_4');
        $authProvider->setDeveloperKey('Test_developer_key');


        $mockDao = $this->getMockBuilder('AuthProviderExtraDetailsDao')->getMock();
        $mockDao->expects($this->once())
                ->method('getAuthProviderDetailsByProviderId')
                ->with(2)
                ->will($this->returnValue($authProvider));

        $this->authService->setAuthProviderExtraDetailsDao($mockDao);
        $result = $this->authService->getAuthProviderDetailsByProviderId(2);
        $this->assertTrue($result instanceof AuthProviderExtraDetails);
    }

    public function testSaveJVAuthProviderOpenId() {
        $authProvider = new AuthProviderExtraDetails();

        $authProvider->setProviderId(3);
        $authProvider->setProviderType(1);

        $mockDao = $this->getMockBuilder('AuthProviderExtraDetailsDao')->getMock();
        $mockDao->expects($this->once())
                ->method('saveAuthProviderExtraDetails')
                ->with($authProvider)
                ->will($this->returnValue($authProvider));

        $this->authService->setAuthProviderExtraDetailsDao($mockDao);
        $result = $this->authService->saveAuthProviderExtraDetails($authProvider);
        $this->assertTrue($result instanceof AuthProviderExtraDetails);
    }

    public function testSaveJVAuthProviderNotOpenId(){
        $authProvider = new AuthProviderExtraDetails();
        
        $authProvider->setProviderId(5);
        $authProvider->setProviderType(2);
        $authProvider->setClientId('Test_client_id_4');
        $authProvider->setClientSecret('Test_secret_4');
        $authProvider->setDeveloperKey('Test_developer_key');
        
        $mockDao = $this->getMockBuilder('AuthProviderExtraDetailsDao')->getMock();
        $mockDao->expects($this->once())
                ->method('saveAuthProviderExtraDetails')
                ->with($authProvider)
                ->will($this->returnValue($authProvider));

        $this->authService->setAuthProviderExtraDetailsDao($mockDao);
        $result = $this->authService->saveAuthProviderExtraDetails($authProvider);
        $this->assertTrue($result instanceof AuthProviderExtraDetails);
    }
}
