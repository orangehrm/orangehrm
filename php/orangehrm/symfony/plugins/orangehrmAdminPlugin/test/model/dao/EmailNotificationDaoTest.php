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
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

class EmailNotificationDaoTest extends PHPUnit_Framework_TestCase {

    private $emailNotificationDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->emailNotificationDao = new EmailNotificationDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/EmailNotificationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetEmailNotificationList() {
        $result = $this->emailNotificationDao->getEmailNotificationList();
        $this->assertEquals(count($result), 3);
    }

    public function testUpdateEmailNotification(){
         $result = $this->emailNotificationDao->updateEmailNotification(array(1,2));
         $this->assertTrue($result);
    }

    public function testGetEnabledEmailNotificationIdList(){
        $result = $this->emailNotificationDao->getEnabledEmailNotificationIdList();
        $this->assertEquals(count($result), 1);
    }

    public function testGetSubscribersByNotificationId(){
        $result = $this->emailNotificationDao->getSubscribersByNotificationId(1);
        $this->assertEquals(count($result), 2);
    }

    public function testGetSubscriberById(){
        $result = $this->emailNotificationDao->getSubscriberById(1);
        $this->assertEquals($result->getName(), 'Kayla Abbey');
    }

    public function testDeleteSubscribers(){
       $result = $this->emailNotificationDao->deleteSubscribers(array(1, 2, 3));
        $this->assertEquals($result, 3);
    }
    
    public function testGetEmailNotification() {
        $notification = $this->emailNotificationDao->getEmailNotification(1);
        $this->assertTrue($notification instanceof EmailNotification);
        $this->assertEquals('Leave Applications', $notification->getName());
        
        $notification = $this->emailNotificationDao->getEmailNotification(3);
        $this->assertTrue($notification instanceof EmailNotification);
        $this->assertEquals('Leave Approvals', $notification->getName());        
        
        $notification = $this->emailNotificationDao->getEmailNotification(113);
        $this->assertTrue($notification === false);
    }

}

