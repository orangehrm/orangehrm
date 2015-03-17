<?php

/*
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM) 
 * System that captures all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com 
 * 
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any 
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc 
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the 
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain 
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property 
 * rights to any design, new software, new protocol, new interface, enhancement, update, 
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for 
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are 
 * reserved to OrangeHRM Inc. 
 * 
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software. 
 *  
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
