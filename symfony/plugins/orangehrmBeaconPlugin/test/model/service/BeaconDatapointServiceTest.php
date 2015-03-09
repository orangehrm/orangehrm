<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BeaconDatapointServiceTest
 *
 * @group beacon
 */
class BeaconDatapointServiceTest extends PHPUnit_Framework_TestCase {

    protected $beaconDatapointService;
    protected $fixture;

    public function setUp() {
        $this->beaconDatapointService = new BeaconDatapointService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmBeaconPlugin/test/fixtures/BeaconDatapointService.yml';
    }

    public function testResolveAllDatapoints() {

        $datapointList = TestDataService::loadObjectList('DataPoint', $this->fixture, 'set1');

        $beaconDatapointDao = $this->getMock('BeaconDatapointDao', array('getAllDatapoints'));
        $beaconDatapointDao->expects($this->once())
                ->method('getAllDatapoints')
                ->will($this->returnValue($datapointList));

        $this->beaconDatapointService->setBeaconDatapointDao($beaconDatapointDao);
        $results = $this->beaconDatapointService->resolveAllDatapoints();
        $this->assertEquals(count($datapointList),  count($results));
        foreach ($datapointList as $datapoint) {
            $this->assertArrayHasKey($datapoint->getName(),$results);
        }
        
    }
    
    public function testCheckTableNameExists() {
        $tableName = 'hs_hr_employee';
        $tableNames = array('hs_hr_employee','ohrm_oauth_refresh_token','ohrm_nationality','ohrm_pay_grade');
        
         $beaconDatapointDao = $this->getMock('BeaconDatapointDao', array('getTableNames'));
        $beaconDatapointDao->expects($this->once())
                ->method('getTableNames')
                ->will($this->returnValue($tableNames));
        
        $this->beaconDatapointService->setBeaconDatapointDao($beaconDatapointDao);
        
        $this->assertTrue($this->beaconDatapointService->checkTableNameExists($tableName));
    }

}
