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
 * @group buzz
 */
class BuzzConfigServiceTest extends PHPUnit\Framework\TestCase {

    private $buzzConfigService;

    /**
     * Set up method
     */
    protected function setUp(): void {

        $this->buzzConfigService = new BuzzConfigService();
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmBuzzPlugin/test/fixtures/OrangeBuzz.yml');
    }
    
    
    /**
     * test ititial share count onfiguration
     */
    public function testgetAllBuzzValues(){
        $returnValue =$this->buzzConfigService->setAllBuzzValues();
        
        $this->assertEquals(TRUE,$returnValue);
    }
    
    /**
     * test ititial share count onfiguration
     */
    public function testGetInitialShareCount(){
        $count =$this->buzzConfigService->getBuzzShareCount();
        
        $this->assertEquals(10,$count);
    }
    
    /**
     * test ititial comment count onfiguration
     */
    public function testGetInitialCommentCount(){
        $count =$this->buzzConfigService->getBuzzInitialCommentCount();
        
        $this->assertEquals(5,$count);
    }
    
    /**
     * test ititial comment count onfiguration
     */
    public function testGetInitialLikeCount(){
        $count =$this->buzzConfigService->getBuzzLikeCount();
        
        $this->assertEquals(5,$count);
    }
    
    /**
     * test ititial refresh Configuration
     */
    public function testGetRefreshTime(){
        $count =$this->buzzConfigService->getRefreshTime();
        
        $this->assertEquals(60000,$count);
    }
    
     /**
     * test ititial comment count onfiguration
     */
    public function testGetInitialTextLenth(){
        $count =$this->buzzConfigService->getBuzzPostTextLenth();
        
        $this->assertEquals(500,$count);
    }
    
    /**
     * Test getting comment length 
     */
    public function testGetCommentLength(){
        $count =$this->buzzConfigService->getBuzzCommentTextLenth();
        
        $this->assertEquals(250,$count);
    }
    
    /**
     * test ititial refresh Configuration
     */
    public function testGetTextLines(){
        $count =$this->buzzConfigService->getBuzzPostTextLines();
        
        $this->assertEquals(5,$count);
    }
    
    /**
     * test ititial more comment Configuration
     */
    public function testGetViewMoreComment(){
        $count =$this->buzzConfigService->getBuzzViewCommentCount();
        
        $this->assertEquals(5,$count);
    }
    
    /**
     * test ititial more comment Configuration
     */
    public function testGetTimeFormal(){
        $result =$this->buzzConfigService->getTimeFormat();
        
        $this->assertEquals('h:i',$result);
    }
    
    /**
     * test ititial more comment Configuration
     */
    public function testGetMostLikePostCount(){
        $count =$this->buzzConfigService->getMostLikePostCount();
        
        $this->assertEquals(5,$count);
    }
    
    /**
     * test ititial more comment Configuration
     */
    public function testGetMostLikeShareCount(){
        $count =$this->buzzConfigService->getMostLikeShareCount();
        
        $this->assertEquals(5,$count);
    }
    
     /**
     * test ititial more comment Configuration
     */
    public function testGetPostShareCount(){
        $count =$this->buzzConfigService->getPostShareCount();
        
        $this->assertEquals(5,$count);
    }
    
    /**
     * test cookie valid time Configuration
     */
    public function testGetCookieValidTime(){
        $cookieTime =$this->buzzConfigService->getCookieValidTime();
        
        $this->assertEquals(500098,$cookieTime);
    }

    public function testGetMaxImageDimension() {
        $imageDim =$this->buzzConfigService->getMaxImageDimension();
        $this->assertEquals(1080, $imageDim);
    }

    public function testGetMaxNotificationPeriod() {
        $maxNotificationPeriod =$this->buzzConfigService->getMaxNotificationPeriod();
        $this->assertEquals('-4 weeks', $maxNotificationPeriod);
    }
}
