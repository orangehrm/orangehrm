<?php
require_once 'PHPUnit/Framework.php';

class KpiServiceTest extends PHPUnit_Framework_TestCase{
	
	
    private $testCases;
    private $kpiService;
    private $jobTitle ;
    
	/**
     * PHPUnit setup function
     */
    public function setup() {

       $configuration = ProjectConfiguration::getApplicationConfiguration('orangehrm', 'test', true);       
       $this->testCases = sfYaml::load(sfConfig::get('sf_test_dir') . '/fixtures/performance/kpi.yml');
       $this->kpiService = new DefineKpiService();
       $this->setupJobTitle();
       
    }
    
    /**
     * Set up job title if not avaliable
     *
     */
     
    private function setupJobTitle( )
    {
    	$jobService	=	new JobService();
    	$jobTitle	=	$jobService->readJobTitle( $this->testCases['JobTitle']['jobtitlecode'] );
    	if(!($jobTitle instanceof JobTitle))
    	{
    		$jobTitle1	=	new JobTitle();
    		$jobTitle1->setId( $this->testCases['JobTitle']['jobtitlecode']);
    		$jobTitle1->setName( $this->testCases['JobTitle']['jobtitlename']);
    		$jobService->saveJobTitle($jobTitle1);
    	}
    	
    	$copyJobTitle	=	$jobService->readJobTitle( $this->testCases['JobTitle']['copyJobtitlecode'] );
    	if(!($copyJobTitle instanceof JobTitle))
    	{
    		$jobTitle2	=	new JobTitle();
    		$jobTitle2->setId( $this->testCases['JobTitle']['copyJobtitlecode']);
    		$jobTitle2->setName( $this->testCases['JobTitle']['copyJobtitlecodename']);
    		$jobService->saveJobTitle($jobTitle2);
    	}
    }
    
    
    /**
     * Test Save Kpi function
     *
     */
    public function testSaveKpi()
    {
    	foreach ($this->testCases['Kpi'] as $testCase) {
			$kpi	=	new DefineKpi();
			$kpi->setJobtitlecode ($this->testCases['JobTitle']['jobtitlecode']);
			$kpi->setDesc ( $testCase['desc']);
			$kpi->setMin ( $testCase['min'] );	
			$kpi->setMax ( $testCase['max'] );
			$kpi->setDefault ( $testCase['default'] );
			$kpi->setIsactive ( $testCase['isactive'] );
			
			$kpi 	= $this->kpiService->saveDefineKpi( $kpi );
			$result	=	($kpi instanceof DefineKpi)?true:false;
			$this->assertTrue($result);
			
    	}
    	  
    }
    
    /**
     * Read Kpi
     *
     */
    public function testGetKpiForJobTitle()
    {
    	$kpiList	=	$this->kpiService->getKpiForJobTitle($this->testCases['JobTitle']['jobtitlecode']);
    	foreach( $kpiList  as $kpi)
    	{
    		$result	=	($kpi instanceof DefineKpi)?true:false;
			$this->assertTrue($result);
    	}
    }
    
    /**
     * Get Kpi List 
     *
     */
     
    public function testGetKpiList( )
    {
   	 	$kpiList	=	$this->kpiService->getKpiList();
    	foreach( $kpiList  as $kpi)
    	{
    		$result	=	($kpi instanceof DefineKpi)?true:false;
			$this->assertTrue($result);
    	}
    }
   
	/**
     * Test Copy Kpi 
     *
     */
    public function testCopyKpi()
    {
    	$result	= $this->kpiService->copyKpi( $this->testCases['JobTitle']['copyJobtitlecode'], $this->testCases['JobTitle']['jobtitlecode']);
    	$this->assertTrue($result);
    }
    
    /**
     * Test delete Kpi 
     *
     */
     
 	public function testDeleteKpi( )
    {
    	$deleteList	=	array();
    	$kpiList	=	$this->kpiService->getKpiForJobTitle($this->testCases['JobTitle']['jobtitlecode']);
    	foreach( $kpiList  as $kpi)
    	{
    		
    		array_push($deleteList,$kpi->getId());
    	}
    	
    	$result	=	$this->kpiService->deleteDefineKpi( $deleteList );
    	$this->assertTrue($result);
    }
    
    
}
?>