<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerformanceReviewTemplateServiceTest
 *
 * @author nadeera
 */

/**
 * @group performance 
 */
class KpiServiceTest extends PHPUnit_Framework_TestCase {


    public function testSaveKpi() {

        $kpi360 = new Kpi();
        $daoMock = $this->getMock("KpiDao", array("saveKpi"));
        $daoMock->expects($this->any())
                ->method('saveKpi')
                ->will($this->returnValue($kpi360));

        $service = new KpiService();
        $service->setDao($daoMock);

        $kpi = $service->saveKpi($kpi360);
        $this->assertTrue(is_object($kpi));
    }

    public function testSearchKpi1() {

        $kpi360 = new Kpi();
        $daoMock = $this->getMock("KpiDao", array("searchKpi"));
        $daoMock->expects($this->any())
                ->method('searchKpi')
                ->will($this->returnValue(array($kpi360)));

        $service = new KpiService();
        $service->setDao($daoMock);

        $kpis = $service->searchKpi(array("jobTitle" => '1'));
        $this->assertEquals(1, sizeof($kpis));
    }


    public function testDeleteKpi() {

        $daoMock = $this->getMock("KpiDao", array("deleteKpi"));
        $daoMock->expects($this->any())
                ->method('deleteKpi')
                ->with($this->equalTo(array('1', '2')))
                ->will($this->returnValue(true));

        $service = new KpiService();
        $service->setDao($daoMock);

        $this->assertTrue($service->deleteKpi(array('1', '2')));
    }

    public function testSearchKpi() {

        $daoMock = $this->getMock("KpiDao", array("searchKpi"));
        $daoMock->expects($this->any())
                ->method('searchKpi')
                ->will($this->returnValue(array(1)));

        $service = new KpiService();
        $service->setDao($daoMock);

        $this->assertEquals(1, sizeof($service->searchKpi(array('1', '2'))));
    }
    
    public function testSearchKpiByJobTitle(){
        $daoMock = $this->getMock("KpiDao", array("searchKpiByJobTitle"));
        $daoMock->expects($this->any())
                ->method('searchKpiByJobTitle')
                ->will($this->returnValue(array(1)));

        $service = new KpiService();
        $service->setDao($daoMock);

        $this->assertEquals(1, sizeof($service->searchKpiByJobTitle(array('jobCode'=>'1'))));
    }

}
