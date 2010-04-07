<?php
require_once 'PHPUnit/Framework.php';

class IDGeneratorServiceTest extends PHPUnit_Framework_TestCase
{
	private $testCases;
	private $idGeneratorService;
		
	/**
     * PHPUnit setup function
     */
    public function setup() {
    	$this->testCases = sfYaml::load(sfConfig::get('sf_test_dir') . '/fixtures/core/idgen.yml');
    	$this->idGeneratorService = new IDGeneratorService();
    }
    
  	/**
     * Test Set Entity
     * @return unknown_type
     */
    public function testSetEntity(){	
    	foreach ($this->testCases['IDGenerator'] as $testCase) {
    		$entity	=	new $testCase['entity'];
    		$this->idGeneratorService->setEntity($entity);
    		$result	=	$this->idGeneratorService->getEntity();
    		$this->assertEquals($entity,$result);

    	}
    	
    }
    
    /**
     * Get next Id
     * @return unknown_type
     */
    public function testGetNextID( )
    {
    	foreach ($this->testCases['IDGenerator'] as $testCase) {
    		$entity	=	new $testCase['entity'];
    		$this->idGeneratorService->setEntity($entity);
    		$nextId	=	$this->idGeneratorService->getNextID();
    		$result	=	(is_numeric(count($nextId))===TRUE)?true:false ;
    		$this->assertTrue($result);
    	}
    }
    
}