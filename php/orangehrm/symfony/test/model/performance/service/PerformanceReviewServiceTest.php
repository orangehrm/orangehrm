<?php
/**
 * Test Cases for PerformanceReviewServiceTest
 * @author Sujith T
 *
 */
require_once 'PHPUnit/Framework.php';

class PerformanceReviewServiceTest extends PHPUnit_Framework_TestCase {
   private $testCases;
   private $pReviewService;
   private $pReviewDao;

   public function setup() {
      $this->pReviewService   =  new PerformanceReviewService();
      $this->testCases        = 	sfYaml::load(sfConfig::get('sf_test_dir') . '/fixtures/performance/performanceReview.yml');
   }

   /**
    * Test PerformanceReview
    */
   public function testPerformanceReview() {
      foreach ($this->testCases['PerformanceReview'] as $k => $v) {
         $pReview = new PerformanceReview();
    		$pReview->setEmployeeId( $v['employeeId']);
    		$pReview->setReviewerId( $v['reviewerId']);
    		$pReview->setPeriodFrom( date('y-m-d',strtotime($v['periodFrom'])));
    		$pReview->setPeriodTo( date('y-m-d',strtotime($v['periodTo'])));
    		$pReview->setKpis( $v['kpis']);

         $this->pReviewDao       =	$this->getMock('PerformanceReviewDao');
         $this->pReviewDao->expects($this->once())
                 ->method('savePerformanceReview')
                 ->will($this->returnValue($pReview));
         $this->pReviewService->setPerformanceReviewDao($this->pReviewDao);

         $result = $this->pReviewService->savePerformanceReview($pReview);
         $this->assertEquals($result, $pReview);
      }
   }

   /**
    * Test readPerformanceReview
    */
   public function testReadPerformanceReview() {
      foreach ($this->testCases['PerformanceReview'] as $k => $v) {
         $pReview = new PerformanceReview();
    		$pReview->setEmployeeId( $v['employeeId']);
    		$pReview->setReviewerId( $v['reviewerId']);
    		$pReview->setPeriodFrom( date('y-m-d',strtotime($v['periodFrom'])));
    		$pReview->setPeriodTo( date('y-m-d',strtotime($v['periodTo'])));
    		$pReview->setKpis( $v['kpis']);

         $this->pReviewDao  =	$this->getMock('PerformanceReviewDao');
         $this->pReviewDao->expects($this->once())
                 ->method('readPerformanceReview')
                 ->will($this->returnValue($pReview));
         $this->pReviewService->setPerformanceReviewDao($this->pReviewDao);

         $result 	=	$this->pReviewService->readPerformanceReview(1);
         $this->assertEquals( $pReview , $result );
      }
   }

   /**
    * test searchPerformanceReview and countReviews
    */
   public function testSearchPerformanceReview() {
      foreach ($this->testCases['PerformanceReview'] as $k => $v) {
         $pReview = new PerformanceReview();
    		$pReview->setEmployeeId( $v['employeeId']);
    		$pReview->setReviewerId( $v['reviewerId']);
    		$pReview->setPeriodFrom( date('y-m-d',strtotime($v['periodFrom'])));
    		$pReview->setPeriodTo( date('y-m-d',strtotime($v['periodTo'])));
    		$pReview->setKpis( $v['kpis']);

         $this->pReviewDao  =	$this->getMock('PerformanceReviewDao');
         $this->pReviewDao->expects($this->once())
                 ->method('searchPerformanceReview')
                 ->will($this->returnValue($pReview));
         $this->pReviewService->setPerformanceReviewDao($this->pReviewDao);

         $result 	=	$this->pReviewService->searchPerformanceReview(array('empId' => $v['employeeId']));
         $this->assertEquals( $pReview , $result );
      }
   }

   /**
    * test countReviews
    */
   public function testCountReviews() {
      foreach ($this->testCases['PerformanceReview'] as $k => $v) {
         $pReview = new PerformanceReview();
    		$pReview->setEmployeeId( $v['employeeId']);
    		$pReview->setReviewerId( $v['reviewerId']);
    		$pReview->setPeriodFrom( date('y-m-d',strtotime($v['periodFrom'])));
    		$pReview->setPeriodTo( date('y-m-d',strtotime($v['periodTo'])));
    		$pReview->setKpis( $v['kpis']);

         $this->pReviewDao  =	$this->getMock('PerformanceReviewDao');
         $this->pReviewDao->expects($this->any())
                 ->method('countReviews')
                 ->will($this->returnValue(0));
         $this->pReviewService->setPerformanceReviewDao($this->pReviewDao);

         $result 	=	$this->pReviewService->countReviews(array('empId' => $v['employeeId']));
         $this->assertEquals(0, $result);
      }
   }

   /**
    * Test changePerformanceStatus
    */
   public function testChangePerformanceStatus() {
      foreach ($this->testCases['PerformanceReview'] as $k => $v) {
         $pReviewDao = new PerformanceReviewDao();

         $pReview = new PerformanceReview();
    		$pReview->setEmployeeId( $v['employeeId']);
    		$pReview->setReviewerId( $v['reviewerId']);
    		$pReview->setPeriodFrom( date('y-m-d',strtotime($v['periodFrom'])));
    		$pReview->setPeriodTo( date('y-m-d',strtotime($v['periodTo'])));
    		$pReview->setKpis( $v['kpis']);

         $pReviewDao->savePerformanceReview($pReview);

         $this->pReviewDao  =	$this->getMock('PerformanceReviewDao');
         $this->pReviewDao->expects($this->once())
              ->method('updatePerformanceReviewStatus')
              ->will($this->returnValue(true));
         $this->pReviewService->setPerformanceReviewDao($this->pReviewDao);

         $result 	=	$this->pReviewService->changePerformanceStatus($pReview, PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED);
         $this->assertTrue($result);
         $pReviewDao->deletePerformanceReview(array($pReview->getId()));
      }
   }

   /**
    * Test sendReviwerSubmitEmail
    */
   public function testSendReviwerSubmitEmail() {
      foreach ($this->testCases['PerformanceReview'] as $k => $v) {
         $pReview = new PerformanceReview();
    		$pReview->setEmployeeId( $v['employeeId']);
    		$pReview->setReviewerId( $v['reviewerId']);
    		$pReview->setPeriodFrom( date('y-m-d',strtotime($v['periodFrom'])));
    		$pReview->setPeriodTo( date('y-m-d',strtotime($v['periodTo'])));
    		$pReview->setKpis( $v['kpis']);

         $this->pReviewDao  =	$this->getMock('PerformanceReviewDao');
         $this->pReviewService->setPerformanceReviewDao($this->pReviewDao);
                  $this->pReviewDao->expects($this->any())
                 ->method('sendReviwerSubmitEmail')
                 ->will($this->returnValue(true));

         $result 	=	$this->pReviewService->sendReviwerSubmitEmail($pReview);
         $this->assertTrue($result);
      }
   }

   /**
    * Test sendReviwRejectEmail
    */
   public function testSendReviwRejectEmail() {
      foreach ($this->testCases['PerformanceReview'] as $k => $v) {
         $pReview = new PerformanceReview();
    		$pReview->setEmployeeId( $v['employeeId']);
    		$pReview->setReviewerId( $v['reviewerId']);
    		$pReview->setPeriodFrom( date('y-m-d',strtotime($v['periodFrom'])));
    		$pReview->setPeriodTo( date('y-m-d',strtotime($v['periodTo'])));
    		$pReview->setKpis( $v['kpis']);

         $this->pReviewDao  =	$this->getMock('PerformanceReviewDao');
         $this->pReviewService->setPerformanceReviewDao($this->pReviewDao);
                  $this->pReviewDao->expects($this->any())
                 ->method('sendReviwRejectEmail')
                 ->will($this->returnValue(true));

         $result 	=	$this->pReviewService->sendReviwRejectEmail($pReview);
         $this->assertTrue($result);
      }
   }

   /**
    * Test sendReviwApproveEmail
    */
   public function testSendReviwApproveEmail() {
      foreach ($this->testCases['PerformanceReview'] as $k => $v) {
         $pReview = new PerformanceReview();
    		$pReview->setEmployeeId( $v['employeeId']);
    		$pReview->setReviewerId( $v['reviewerId']);
    		$pReview->setPeriodFrom( date('y-m-d',strtotime($v['periodFrom'])));
    		$pReview->setPeriodTo( date('y-m-d',strtotime($v['periodTo'])));
    		$pReview->setKpis( $v['kpis']);

         $this->pReviewDao  =	$this->getMock('PerformanceReviewDao');
         $this->pReviewService->setPerformanceReviewDao($this->pReviewDao);
                  $this->pReviewDao->expects($this->any())
                 ->method('sendReviwApproveEmail')
                 ->will($this->returnValue(true));

         $result 	=	$this->pReviewService->sendReviwApproveEmail($pReview);
         $this->assertTrue($result);
      }
   }

   /**
    * Test informReviewer
    */
   public function testInformReviewer() {
      foreach ($this->testCases['PerformanceReview'] as $k => $v) {
         $pReview = new PerformanceReview();
    		$pReview->setEmployeeId( $v['employeeId']);
    		$pReview->setReviewerId( $v['reviewerId']);
    		$pReview->setPeriodFrom( date('y-m-d',strtotime($v['periodFrom'])));
    		$pReview->setPeriodTo( date('y-m-d',strtotime($v['periodTo'])));
    		$pReview->setKpis( $v['kpis']);

         $this->pReviewDao  =	$this->getMock('PerformanceReviewDao');
         $this->pReviewService->setPerformanceReviewDao($this->pReviewDao);
                  $this->pReviewDao->expects($this->any())
                 ->method('informReviewer')
                 ->will($this->returnValue(true));

         $result 	=	$this->pReviewService->informReviewer($pReview);
         $this->assertTrue($result);
      }
   }

   /**
    * Test deletePerformanceReview
    */
   public function testDeletePerformanceReview() {
      $this->pReviewDao  =	$this->getMock('PerformanceReviewDao');
      $this->pReviewDao->expects($this->once())
                 ->method('deletePerformanceReview')
                 ->will($this->returnValue(true));
      $this->pReviewService->setPerformanceReviewDao($this->pReviewDao);
      $result = $this->pReviewService->deletePerformanceReview(array(1));

      $this->assertTrue($result);
   }
}
?>