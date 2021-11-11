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
 * Description of OpenIdProviderDaoTest
 * @group openidauth
 * @author orangehrm
 */
class OpenIdProviderDaoTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
        $this->dao = new OpenIdProviderDao();
        $fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmOpenidAuthenticationPlugin/test/fixtures/openiduser.yml';
        TestDataService::truncateTables(array('OpenidProvider'));    
        
        TestDataService::populate($fixture);
    }
    /**
     *
     * @param OpenidProvider $openIdProvider
     * @return OpenidProvider 
     */
    public function testSaveOpenIdProvider(){
          $openIdProvider = new OpenidProvider();
          $openIdProvider->setProviderName('New Open ID Provider');
          $openIdProvider->setProviderUrl('http://new.com/id');
          $openIdProvider->setStatus(1);
          
          $result=$this->dao->saveOpenIdProvider($openIdProvider);
          
          $this->assertTrue($result instanceof OpenidProvider);
          $this->assertEquals($result->getStatus(), 1);
          $this->assertEquals($result->getProviderName(), 'New Open ID Provider');
          $this->assertEquals($result->getProviderUrl(), 'http://new.com/id');
         
        
    }
    /**
     *
     * @param bool $isActive 
     * @return OpenidProvider 
     */
    public function testListOpenIdProviders(){
         $result1=$this->dao->listOpenIdProviders();
         $this->assertEquals(count($result1),3);
         
         $result2=$this->dao->listOpenIdProviders(false);
         $this->assertEquals(count($result2),4);

    }
    /**
     *
     * @param int $id 
     * @return mix 
     */
    public function testRemoveOpenIdProvider(){
          $row=$this->dao->removeOpenIdProvider(1);
          $provider=$this->dao->getOpenIdProvider(1);
          $this->assertEquals($provider->getStatus(), 0);
          $this->assertEquals($row, 1);
          
    }
    /**
     * Get Open Id Provider by ID
     * @return OpenidProvider
     */
    public function testGetOpenIdProvider() {
        $result=$this->dao->getOpenIdProvider(1);
        
        $this->assertTrue($result instanceof OpenidProvider);
        $this->assertEquals($result->getStatus(), 1);
        $this->assertEquals($result->getProviderName(), 'Google');
        $this->assertEquals($result->getProviderUrl(), 'https://google.com/o/8/');
        
    }   
}

?>
