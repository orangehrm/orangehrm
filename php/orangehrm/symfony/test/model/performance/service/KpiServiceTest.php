<?php
require_once 'PHPUnit/Framework.php';

class KpiServiceTest extends PHPUnit_Framework_TestCase{
	
	
    private $testCases;
    private $kpiService;
    private $kpiDao	;
    
	/**
     * PHPUnit setup function
     */
    public function setup() {
			$this->kpiService	=	new KpiService();
			$this->testCases 	= 	sfYaml::load(sfConfig::get('sf_test_dir') . '/fixtures/performance/kpi.yml');
			
    }
    
    /**
     * Test Save Kpi function
     *
     */
    public function testSaveKpi(){
    	
    	foreach ($this->testCases['Kpi'] as $key=>$testCase) {
			$kpi	=	new DefineKpi();
			$kpi->setJobtitlecode ($this->testCases['JobTitle']['jobtitlecode']);
			$kpi->setDesc ( $testCase['desc']);
			$kpi->setMin ( $testCase['min'] );	
			$kpi->setMax ( $testCase['max'] );
			$kpi->setDefault ( $testCase['default'] );
			$kpi->setIsactive ( $testCase['isactive'] );
			
			$this->kpiDao		=	$this->getMock('KpiDao'); 
			$this->kpiDao->expects($this->once())
                       ->method('saveKpi')
                       ->will($this->returnValue($kpi));
                       
            $this->kpiService->setKpiDao($this->kpiDao );
            
            $result 	=	$this->kpiService->saveKpi( $kpi );
            $this->assertEquals( $kpi , $result );
            
    	}
    	
    }
    
    /**
     * Test Read Kpi
     * @return unknown_type
     */
    public function testReadKpi(){
    	
    	foreach ($this->testCases['Kpi'] as $key=>$testCase) {
    		$kpi	=	new DefineKpi();
			$kpi->setJobtitlecode ($this->testCases['JobTitle']['jobtitlecode']);
			$kpi->setDesc ( $testCase['desc']);
			$kpi->setMin ( $testCase['min'] );	
			$kpi->setMax ( $testCase['max'] );
			$kpi->setDefault ( $testCase['default'] );
			$kpi->setIsactive ( $testCase['isactive'] );
			
			$this->kpiDao		=	$this->getMock('KpiDao'); 
			$this->kpiDao->expects($this->once())
                       ->method('readKpi')
                       ->will($this->returnValue($kpi));
            $this->kpiService->setKpiDao($this->kpiDao );
            $result 	=	$this->kpiService->readKpi( 1 );
            $this->assertEquals( $kpi , $result ); 
            
    	}
    	
    	
    }
    
    
}
?>