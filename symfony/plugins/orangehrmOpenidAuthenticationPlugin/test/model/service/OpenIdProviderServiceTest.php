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
 * Description of OpenIdProviderServiceTest
 * @group openidauth
 * @author orangehrm
 */
class OpenIdProviderServiceTest extends PHPUnit_Framework_TestCase {
    private $openIdProviderService;
     protected function setUp() {
        $this->openIdProviderService = new OpenIdProviderService();
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
        
        $openIdProviderDaoMock = $this->getMockBuilder('OpenIdProviderDao')
			->setMethods( array('saveOpenIdProvider'))
			->getMock();

        $openIdProviderDaoMock->expects($this->once())
                ->method('saveOpenIdProvider')
                ->with($openIdProvider)
                ->will($this->returnValue($openIdProvider));
        
          $this->openIdProviderService->setOpenIdProviderDao($openIdProviderDaoMock);
        
          $result=$this->openIdProviderService->saveOpenIdProvider($openIdProvider);
          
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
        
        $openIdProvider1 = new OpenidProvider();
        $openIdProvider1->setProviderName('New Open ID Provider1');
        $openIdProvider1->setProviderUrl('http://new.com/id1');
        $openIdProvider1->setStatus(1);
        
        $openIdProvider2 = new OpenidProvider();
        $openIdProvider2->setProviderName('New Open ID Provider2');
        $openIdProvider2->setProviderUrl('http://new.com/id2');
        $openIdProvider2->setStatus(1);
        
        $openIdProviderList = array();
        
        $openIdProviderList[0] =$openIdProvider1;
        $openIdProviderList[1] =$openIdProvider2;
        
        $openIdProviderDaoMock = $this->getMockBuilder('OpenIdProviderDao')
			->setMethods( array('listOpenIdProviders'))
			->getMock();

        $openIdProviderDaoMock->expects($this->once())
                ->method('listOpenIdProviders')
                ->will($this->returnValue($openIdProviderList));
        
          $this->openIdProviderService->setOpenIdProviderDao($openIdProviderDaoMock);
        
         $result1=$this->openIdProviderService->listOpenIdProviders();
         $this->assertEquals(count($result1),2);
          

    }
    /**
     *
     * @param int $id 
     * @return mix 
     */
    public function testRemoveOpenIdProvider(){
        
        $openIdProvider = new OpenidProvider();
        $openIdProvider->setId(1);
        $openIdProvider->setProviderName('New Open ID Provider1');
        $openIdProvider->setProviderUrl('http://new.com/id1');
        $openIdProvider->setStatus(0);
        
         
        
        $openIdProviderDaoMock = $this->getMockBuilder('OpenIdProviderDao')
			->setMethods( array('removeOpenIdProvider','getOpenIdProvider'))
			->getMock();

        $openIdProviderDaoMock->expects($this->once())
                ->method('removeOpenIdProvider')
                ->will($this->returnValue(1));
        $openIdProviderDaoMock->expects($this->once())
                ->method('getOpenIdProvider')
                ->will($this->returnValue($openIdProvider));
        
          $this->openIdProviderService->setOpenIdProviderDao($openIdProviderDaoMock);
        
          $row=$this->openIdProviderService->removeOpenIdProvider(1);
          $provider=$this->openIdProviderService->getOpenIdProvider(1);
          $this->assertEquals($provider->getStatus(), 0);
          $this->assertEquals($row, 1);
          
    }
    /**
     * Get Open Id Provider by ID
     * @return OpenidProvider
     */
    public function testGetOpenIdProvider() {
        
        $openIdProvider = new OpenidProvider();
        $openIdProvider->setId(1);
        $openIdProvider->setProviderName('Google');
        $openIdProvider->setProviderUrl('https://google.com/o/8/');
        $openIdProvider->setStatus(1);
        
        $openIdProviderDaoMock = $this->getMockBuilder('OpenIdProviderDao')
			->setMethods( array('getOpenIdProvider'))
			->getMock();

        $openIdProviderDaoMock->expects($this->once())
                ->method('getOpenIdProvider')
                ->will($this->returnValue($openIdProvider));
        
          $this->openIdProviderService->setOpenIdProviderDao($openIdProviderDaoMock);
        
        $result=$this->openIdProviderService->getOpenIdProvider(1);
        
        $this->assertTrue($result instanceof OpenidProvider);
        $this->assertEquals($result->getId(), 1);
        $this->assertEquals($result->getStatus(), 1);
        $this->assertEquals($result->getProviderName(), 'Google');
        $this->assertEquals($result->getProviderUrl(), 'https://google.com/o/8/');
        
    }   
}

?>
