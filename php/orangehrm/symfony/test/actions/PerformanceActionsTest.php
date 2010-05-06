<?php

require_once 'PHPUnit/Framework.php';
define('SYMFONY_ROOT', dirname(__FILE__) . '/../../');

require_once SYMFONY_ROOT . 'test/util/MockContext.class.php';
require_once SYMFONY_ROOT . 'test/util/MockWebRequest.class.php';
require_once SYMFONY_ROOT . 'apps/orangehrm/modules/performance/actions/actions.class.php';

/**
 * PerformanceActionsTest class tests Performance Controller
 *
 * @author Sujith T
 */
class PerformanceActionsTest extends PHPUnit_Framework_TestCase {

   private $context;
   //private $deleteDefineKpi;

   /**
    * SetUp Function
    */
   protected function setUp() {
      $this->context = MockContext::getInstance();
      $request = new MockWebRequest();
      
      // In sfConfigCache, we just need checkConfig method
      $configCache = $this->getMock('sfConfigCache', array('checkConfig'), array(), '', false);

      // Mock of controller, with redirect method mocked.
      $controller = $this->getMock('sfController', array('redirect'), array(), '', false);

      $this->context->request       = $request;
      $this->context->configCache   = $configCache;
      $this->context->controller    = $controller;
      $this->context->user          = $this->getMock('sfUser', array('setFlash'), array(), '', false);
      //$this->deleteDefineKpi        = false;
   }

   /**
    * Test ListDefineKpi
    */
   public function testListDefineKpi() {
      $kpiService = $this->getMock('KpiService');
      /*$kpiService->expects($this->once())
               ->method('getKpiList')
               ->will($this->returnValue($kpi));*/

      //create action class
      $performanceAction = new performanceActions($this->context, "performance", "listDefineKpi");
      //$performanceAction->setJobService($jobService);
      $performanceAction->setKpiService($kpiService);

      $request = $this->context->request;
      $request->setMethod(sfRequest::GET);
      $performanceAction->executeListDefineKpi($request);

      $this->assertTrue(is_object($performanceAction->kpiList));
   }

   /**
    * Test SaveKpi
    */
   public function testSaveKpi() {
      $kpiService = $this->getMock('KpiService');

      //mock the User class -> expects 'SUCCESS' flash to be set -> see action class
      $user = $this->getMock('sfUser', array('setFlash'), array(), '', false);
      $user->expects($this->exactly(2))
          ->method('setFlash')
          ->will($this->returnCallback(array(&$this, 'setFlash')));
      $this->context->user = $user;

      //finally should redirect to list page
      $this->context->controller->expects($this->once())
                         ->method('redirect')
                         ->with($this->equalTo('performance/saveKpi'));

      //create action class
      $performanceAction = new performanceActions($this->context, "performance", "saveKpi");
      $performanceAction->setKpiService($kpiService);

      $postParam = array(
         'txtJobTitle' => 'JOB001',
         'txtDescription' => 'Sample Test',
         'txtMinRate' => 10,
         'txtMaxRate' => 100,
         'chkDefaultScale' => 1
      );

     $request = $this->context->request;
     $request->setPostParameters($postParam);

     // Set request to POST method
     $request->setMethod(sfRequest::POST);

     try{
        $performanceAction->executeSaveKpi($request);
     } catch(Exception $e) {
        
     }
     //make assertion
     $this->assertTrue(isset($this->flashMessages['message']));
     $this->assertEquals('SUCCESS', $this->flashMessages['messageType']);
   }

   /**
    * Test UpdateKpi
    */
   public function testUpdateKpi() {
      $kpiService = $this->getMock('KpiService');

      $kpi	=	new DefineKpi();
      $kpi->setId(1);
      $kpi->setJobtitlecode ('JOB001');
      $kpi->setDesc ("desc");
      $kpi->setMin (1);
      $kpi->setMax (10);
      $kpi->setDefault (2);
      $kpi->setIsactive (1);

      $kpiService->expects($this->once())
               ->method('saveKpi')
               ->will($this->returnValue($kpi));

      $kpi 	=	$kpiService->saveKpi( $kpi );

      //mock the User class -> expects 'SUCCESS' flash to be set -> see action class
      $user = $this->getMock('sfUser', array('setFlash'), array(), '', false);
      $this->context->user = $user;

      //create action class
      $performanceAction = new performanceActions($this->context, "performance", "updateKpi");
      $performanceAction->setKpiService($kpiService);

      $postParam = array('txtJobTitle' => 'JOB001', 'txtDescription' => 'Sample Test', 'txtMinRate' => 10,
      'txtMaxRate' => 100, 'chkDefaultScale' => 1);
      $request = $this->context->request;
      $request->setGetParameters($postParam);

      $request->setMethod(sfRequest::GET);

      try{
        $performanceAction->executeUpdateKpi($request);
      } catch(Exception $e) {

      }

      $this->assertFalse(isset($this->flashMessages['message']));
      $this->assertFalse(isset($this->flashMessages['messageType']));
   }

   /**
    * Test Copy KPI
    */
   public function testCopyKpi() {
      $kpiService = $this->getMock('KpiService');

      $user = $this->getMock('sfUser', array('setFlash'), array(), '', false);

      //create action class
      $performanceAction = new performanceActions($this->context, "performance", "copyKpi");
      $performanceAction->setKpiService($kpiService);

      $postParam = array('txtCopyJobTitle' => 'JOB001', 'txtJobTitle' => 'JOB002', 'txtConfirm' => 1);

      $request = $this->context->request;
      $request->setPostParameters($postParam);
      $request->setMethod(sfRequest::POST);

      try{
        $performanceAction->executeCopyKpi($request);
      } catch(Exception $e) {

      }
      
     //make assertion
     $this->assertEquals($postParam['txtCopyJobTitle'], $performanceAction->toJobTitle);
     $this->assertEquals($postParam['txtJobTitle'], $performanceAction->fromJobTitle);
   }

   /**
    * Test DeleteDefineKpi
    */
   public function testDeleteDefineKpi() {
      $kpiService = $this->getMock('KpiService');

      // Set up mock object to call callback method
      $kpiService->expects($this->once())
                    ->method('deleteKpi')
                    ->will($this->returnCallback(array(&$this, 'deleteDefineKpiCallback')));

      $kpiService->deleteKpi(1);
      
      $user = $this->getMock('sfUser', array('setFlash'), array(), '', false);
      $user->expects($this->exactly(2))
          ->method('setFlash')
          ->will($this->returnCallback(array(&$this, 'setFlash')));
      $this->context->user = $user;

      //finally should redirect to list page
      $this->context->controller->expects($this->once())
                         ->method('redirect')
                         ->with($this->equalTo('performance/listDefineKpi'));

      $performanceAction = new performanceActions($this->context, "performance", "deleteDefineKpi");
      $performanceAction->setKpiService($kpiService);

      $postParam = array('chkKpiID' => 1);
      $request = $this->context->request;
      $request->setPostParameters($postParam);
      $request->setMethod(sfRequest::POST);

      try {
         $performanceAction->executeDeleteDefineKpi($request);
         $this->fail("Expected to redirect");
      } catch(Exception $e) {
         
      }

      $this->assertTrue($this->deleteDefineKpi);
      $this->assertTrue(isset($this->flashMessages['message']));
   }

   /**
    * Test ViewReview
    */
   public function testViewReview() {
     $prService = $this->getMock('PerformanceReviewService');
     $arrReview = array('review1', 'review2', 'review3');

      //Set up mock object to call callback method
      $prService->expects($this->once())
                    ->method('searchPerformanceReview')
                    ->will($this->returnCallback(array(&$this, 'searchPerformanceReviewCallback')));

      $prService->searchPerformanceReview($arrReview);

      $performanceAction = new performanceActions($this->context, "performance", "viewReview");
      $performanceAction->setPerformanceReviewService($prService);



      try {
         $performanceAction->executeViewReview();
         $this->fail("Expected to redirect");
      } catch(Exception $e) {

      }

      $this->assertTrue(isset($this->performanceReviews));
   }

   /**
    * Test DeleteReview
    */
   public function testDeleteReview() {
      $prService = $this->getMock('PerformanceReviewService');

      $prService->expects($this->once())
              ->method('deletePerformanceReview')
              ->will($this->returnValue(true));
      $prService->deletePerformanceReview(array(1));

      $this->context->user->expects($this->exactly(2))
          ->method('setFlash')
          ->will($this->returnCallback(array(&$this, 'setFlash')));

      //finally should redirect to list page
      $this->context->controller->expects($this->once())
                         ->method('redirect')
                         ->with($this->equalTo('performance/viewReview'));

      $performanceAction = new performanceActions($this->context, "performance", "deleteReview");

      $postParam = array('chkReview' => 1);
      $request = $this->context->request;
      $request->setPostParameters($postParam);
      $request->setMethod(sfRequest::POST);

      try {
         $performanceAction->executeDeleteReview($request);
         $this->fail("Expected to redirect");
      } catch(Exception $e) {
         
      }
      $this->assertTrue(isset($this->flashMessages['templateMessage']));
   }

    /**
     * Call back method for setFlash
     * @return string
     */
   public function setFlash() {
      // verify 2 parameters
      $args = func_get_args();
      $this->assertEquals(2, count($args), 'flash should receive 2 parameters');
      $name = $args[0];
      $value = $args[1];

      $this->flashMessages[$name] = $value;
   }

   /**
    * Delete Define KPI Callback
    */
   public function deleteDefineKpiCallback() {
      $args = func_get_args();
      $this->assertEquals(1, count($args), 'deleteDefineKpi should receive only 1 parameter');
      $this->deleteDefineKpi = false;
      if(count($args)) {
         $this->deleteDefineKpi = true;
      }
   }

   /**
    * search PerformanceReviewCallback function
    */
   public function searchPerformanceReviewCallback() {
      $args = func_get_args();
      $this->performanceReviews = $args[0];
   }
}
?>
