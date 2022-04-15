<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of KpiDaoTest
 *
 * @author nadeera
 */

/**
 * @group performance
 */
class KpiDaoTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        TestDataService::truncateTables(array('Kpi'));
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmPerformancePlugin/test/fixtures/kpi.yml');
    }
    
    public function testSaveKpi() {

        $dao = new KpiDao();

        $kpi = new Kpi();
        $kpi->setJobTitleCode(1);
        $kpi->setId(1);
        $kpi->setKpiIndicators('new kpi');
        $kpi->setMinRating(1);
        $kpi->setMaxRating(2);
        $kpi->setDefaultKpi(1);

        $kpi = $dao->saveKpi($kpi);
        $this->assertEquals(1, $kpi->getId());
    }
    
    public function testSearcKpi1() {

        $dao = new KpiDao();
        $kpis = $dao->searchKpi();
        $this->assertEquals(3, sizeof($kpis));
    }
    
    public function testSearcKpi2() {

        $dao = new KpiDao();
        $kpis = $dao->searchKpi(array('jobCode' => 1));
        $this->assertEquals(count($kpis), sizeof(array('jobCode' => 1)));
    }
    
    public function testDeleteKpi() {

        $dao = new KpiDao();
        $this->assertTrue($dao->deleteKpi(array("1")));
    }
    
    public function testSearcKpi3() {

        $dao = new KpiDao();
        $kpis = $dao->searchKpi(array('id' => 2));
        $this->assertEquals($kpis['id'], 2);
    }
    
    public function testSearcKpi4() {

        $dao = new KpiDao();
        $kpis = $dao->searchKpi(array('isDefault' => 1));
        $this->assertEquals(count($kpis), 1);
    }
    
    public function testSearchKpiByJobTitle() {

        $dao = new KpiDao();
        $kpis = $dao->searchKpiByJobTitle(array('jobCode' => 2));
        $this->assertEquals(count($kpis), 1);
    }

}
