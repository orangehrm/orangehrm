<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BeaconCommunicationServiceTest
 *
 * @group beacon
 */
class BeaconCommunicationServiceTest extends PHPUnit_Framework_TestCase {

    protected $beaconCommunicationService;
    protected $beaconConfigService;
    protected $fixture;

    public function setUp() {
        $this->beaconCommunicationService = new BeaconCommunicationsService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmBeaconPlugin/test/fixtures/BeaconDatapointService.yml';
        $this->beaconConfigService = $this->getMock('BeaconConfigService', array('setBeaconLock', 'getBeaconLock', 'getBeaconActivationAcceptanceStatus', 'getBeaconActivationStatus', 'getBeaconNextFlashTime', 'changeConfigTable', 'resolveNotificationMessages'));
    }

    public function testCheckFlashTimeExpiry() {
        
        $this->beaconConfigService->expects($this->exactly(2))
                ->method('getBeaconActivationAcceptanceStatus')
                ->will($this->onConsecutiveCalls('on','off'));
        $this->beaconConfigService->expects($this->exactly(1))
                ->method('getBeaconActivationStatus')
                ->will($this->returnValue('on'));

        $time = strtotime('28 Mar 15 16:37:34');
        
        $this->beaconConfigService->expects($this->exactly(1))
                ->method('getBeaconNextFlashTime')
                ->will($this->returnValue($time));

        $this->beaconCommunicationService->setBeaconConfigurationService($this->beaconConfigService);
        $result = $this->beaconCommunicationService->checkFlashTimeExpiry(strtotime('28 Apr 15 16:37:34'));
        $this->assertTrue($result);
         $result = $this->beaconCommunicationService->checkFlashTimeExpiry(strtotime('28 Apr 15 16:37:34'));
            $this->assertTrue(!$result);
    }

//    public function testAcquireLock() {
//        
//        $configService = 
//    }
}
