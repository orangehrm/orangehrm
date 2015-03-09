<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BeaconNotificationDaoTest
 *
 * @group beacon
 */
class BeaconNotificationDaoTest extends PHPUnit_Framework_TestCase{
    private $beaconNotificationDao;
    
    public function setUp() {
        
        $this->beaconNotificationDao = new BeaconNotificationDao();
        TestDataService::populate(sfConfig::get('sf_plugins_dir'). '/orangehrmBeaconPlugin/test/fixtures/BeaconNotificationDao.yml');
    }
    
    public function testDeleteNotificationByName() {
        $name = 'link_notification';
        $count = Doctrine_Core::getTable('BeaconNotification')->count();
        $result = $this->beaconNotificationDao->deleteNotificationByName($name);
        
        $countAfter = Doctrine_Core::getTable('BeaconNotification')->count();
        $this->assertEquals($count,$countAfter+$result);
        $resultTest = Doctrine_Core::getTable('BeaconNotification')->findBy('name', $name);        
        $this->assertEquals(0,count($resultTest));
    }
    
    public function testGetNotificationByName() {
        $name= 'link_notification';
        $result = $this->beaconNotificationDao->getNotificationByName($name);   
        
        $this->assertEquals('link_notification',$result->getName());
    }
    
    public function testGetRandomNotification() {
        
        $result = $this->beaconNotificationDao->getRandomNotification();
        $notification = new BeaconNotification();
                $notification->fromArray($result);
                
        $this->assertTrue($notification instanceof BeaconNotification);
        $this->assertTrue(!is_null($notification->getDefinition()));
        
    }
}
