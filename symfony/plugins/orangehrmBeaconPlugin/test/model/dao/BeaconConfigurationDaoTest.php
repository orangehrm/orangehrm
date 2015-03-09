<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BeaconConfigurationDaoTest
 *
 *  @group beacon
 */
class BeaconConfigurationDaoTest extends PHPUnit_Framework_TestCase{
    
    private $beaconConfigDao;
    private $fixture;
    
    protected function setUp() {
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmBeaconPlugin/test/fixtures/BeaconConfigurationDao.yml';
        TestDataService::populate($this->fixture);
        $this->beaconConfigDao = new BeaconConfigurationDao();
        
    }
    
    public function testSetBeaconLock() {
        
        $this->assertEquals(1,  $this->beaconConfigDao->setBeaconLock(1000));
        $this->assertEquals(0,  $this->beaconConfigDao->setBeaconLock(1200));
        $this->assertEquals(1,  $this->beaconConfigDao->setBeaconLock(1800));      
        $this->assertEquals(1,  $this->beaconConfigDao->setBeaconLock('unlocked'));
    }
}
