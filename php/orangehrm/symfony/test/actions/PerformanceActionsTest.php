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
   }

   /**
    * Test ListDefineKpi
    */
   public function testListDefineKpi() {
      $kpi        = array('test1', 'kpi1', 'kpi2');

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
}
?>
