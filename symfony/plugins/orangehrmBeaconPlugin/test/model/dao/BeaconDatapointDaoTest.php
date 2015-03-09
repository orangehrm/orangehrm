<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BeaconDatapointDaoTest
 *
 * @group beacon
 * 
 */
class BeaconDatapointDaoTest extends PHPUnit_Framework_TestCase{
    
    private $beaconDatapointDao;
    
    protected function setUp() {

        $this->beaconDatapointDao = new BeaconDatapointDao();
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmBeaconPlugin/test/fixtures/BeaconDatapointDao.yml');
    }
    
    public function testGetAllDatapoints() {
        $results = $this->beaconDatapointDao->getAllDatapoints();
        $resultsTest = Doctrine_Core::getTable('Datapoint')->findAll();
        $this->assertEquals(count($resultsTest),  count($results));
        $this->assertTrue($results->getFirst() instanceof DataPoint);
    }
    
    public function testGetDatapointTypeByName() {
        $name = 'config';
        $result = $this->beaconDatapointDao->getDatapointTypeByName($name);      
        $this->assertEquals(1,count($result));
        $this->assertTrue($result->getFirst() instanceof DataPointType);
        $this->assertEquals($name,$result->getFirst()->getName());
    }
    
    public function testGetDatapointByName() {
        $name = 'company_name';
        $result = $this->beaconDatapointDao->getDatapointByName($name);
        
        $this->assertTrue($result instanceof DataPoint);
        $this->assertEquals($name,$result->getName());
    }
    
    public function testDeleteDatapointByName() {
        $name = 'company_name';
        $resultTest = Doctrine_Core::getTable('Datapoint')->findBy('name', $name);
        $this->assertTrue($resultTest instanceof Doctrine_Collection);
        $result = $this->beaconDatapointDao->deleteDatapointByName($name);        
        $this->assertEquals(1,$result);
        $resultTest = Doctrine_Core::getTable('Datapoint')->findBy('name', $name);        
        $this->assertTrue(!count($resultTest->toArray()));
    }
    
    public function testGetTableNames() {
        $tableList = $this->beaconDatapointDao->getTableNames();
        $tables = Doctrine_Manager::connection()->getTables();
       
        foreach ($tables as $table) {
            $this->assertTrue(is_numeric(array_search($table->getTableName(), $tableList)));            
        }
        
        
    }
}
