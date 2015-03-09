<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BeaconConfigurationServiceTest
 *
 * @group beacon
 */
class BeaconConfigurationServiceTest extends PHPUnit_Framework_TestCase{
    
    protected $fixture;
    protected $beaconConfigurationService;
    
    public function setUp() {
        $this->beaconConfigurationService = new BeaconConfigurationService();
        $this->fixture = sfConfig::get('sf_plugins_dir').'/orangehrmBeaconPlugin/test/fixtures/BeaconConfigurationService.yml';
        
    }
    
    public function testChangeConfigTable() {
        
        $configData = array('<change type = "changeConfig">
       <operation>
            UPDATE
        </operation>
        <key>
            beacon.flash_period
        </key>
        <value>604800</value>
    </change>',
//            '    <change type = "changeConfig">
//       <operation>
//            ADD
//        </operation>
//        <key>
//            beacon.flash_period_2
//        </key>
//        <value>604800</value>
//    </change>'
    );
        $configDao = $this->getMock('ConfigDao',  array('setValue'));
        $configDao->expects($this->exactly(1))
                ->method('setValue')
                ->with($this->stringContains('beacon.flash_period'),  $this->equalTo('604800'));
//                ->withConsecutive(
//                        array($this->equalTo('beacon.flash_period'),  $this->equalTo('604800')),
//                        array($this->equalTo('beacon.flash_period_2'),  $this->equalTo('604800'))
//                        );
                
        $this->beaconConfigurationService->setConfigDao($configDao);
        foreach ($configData as $definition) {
            $this->beaconConfigurationService->changeConfigTable($definition);
        }        
    }    
    
}
