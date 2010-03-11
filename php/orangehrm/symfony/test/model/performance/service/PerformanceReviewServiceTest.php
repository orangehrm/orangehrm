<?php
require_once 'PHPUnit/Framework.php';

class PerformanceReviewServiceTest extends PHPUnit_Framework_TestCase{
	
	private $testCases;
    private $performanceReviewService;
    
	/**
     * PHPUnit setup function
     */
    public function setup() {

       $configuration = ProjectConfiguration::getApplicationConfiguration('orangehrm', 'test', true);       
       $this->testCases = sfYaml::load(sfConfig::get('sf_test_dir') . '/fixtures/performance/performanceReview.yml');
       $this->performanceReviewService = new PerformanceReviewService();
       
    }
    
    /**
     * Test performance Save 
     * @return unknown_type
     */
    public function testSavePerformanceReview(){
    	$kpiService = new DefineKpiService();
    	$performanceKpiService	= new PerformanceKpiService();
    	$xmlStr					=	$performanceKpiService->getXmlFromKpi( $kpiService->getKpiForJobTitle('JOB002'));
    	
    	foreach ($this->testCases['PerformanceReview'] as $testCase) {
    		$performanceReview	=	new PerformanceReview();
    		$performanceReview->setEmployeeId( $testCase['employeeId']);
    		$performanceReview->setReviewerId( $testCase['reviewerId']);
    		//$performanceReview->setPeriodFrom( $testCase['periodFrom']);
    		//$performanceReview->setPeriodTo( date('YYYY-MM-DD',strtotime($testCase['periodTo'])));
    		$performanceReview->setKpis( $xmlStr);
    		
    		$performanceReview	=	$this->performanceReviewService->savePerformanceReview( $performanceReview );
    		$result	=	($performanceReview instanceof PerformanceReview)?true:false;
			$this->assertTrue($result);
    	}
    }
    
    /**
     * Test read performance review 
     * @return unknown_type
     */
    public function testReadPerformanceReview()
    {
    	foreach($this->performanceReviewService->getPerformanceReviewList() as $performanceReview)
    	{
    		$readPerformanceReview = $this->performanceReviewService->readPerformanceReview( $performanceReview->getId());
    		$result	=	($readPerformanceReview instanceof PerformanceReview)?true:false;
			$this->assertTrue($result);
    	}
    }
    
   /**
     * Test Change Performance Status 
     * @return unknown_type
     */
    public function testChangePerformanceStatus( )
    {
    	foreach($this->performanceReviewService->getPerformanceReviewList() as $performanceReview)
    	{
    		$result = $this->performanceReviewService->changePerformanceStatus( $performanceReview,PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED);
			$this->assertTrue($result);
    	}
    }
    
}