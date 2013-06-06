<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2012 OrangeHRM Inc., http://www.orangehrm.com
 *
 * Please refer the file license/LICENSE.TXT for the license which includes terms and conditions on using this software.
 * 
 * @group ReportingChain
 *
 */
class EmployeeDaoReportingChainTest extends PHPUnit_Framework_TestCase {

    /**
     *
     * @var EmployeeDao 
     */
    protected $dao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->dao = new EmployeeDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/reporting-chain-test-data.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * @group ReportingChain 
     */
    public function testGetSubordinateList_ReportingChain_Simple2LevelHierarchy() {
        $chain = $this->dao->getSubordinateList(94, false, true);
        $this->assertTrue(is_array($chain));
        $this->assertEquals(2, count($chain));

        list($subordinate1, $subordinate2) = $chain;

        $this->assertTrue($subordinate1 instanceof Employee);
        $this->assertEquals(24, $subordinate1->getEmpNumber());

        $this->assertTrue($subordinate2 instanceof Employee);
        $this->assertEquals(53, $subordinate2->getEmpNumber());

        $chain = $this->dao->getSubordinateList(87, false, true);
        $this->assertTrue(is_array($chain));
        $this->assertEquals(7, count($chain));

        list($subordinate1, $subordinate2, $subordinate3, $subordinate4, $subordinate5, $subordinate6, $subordinate7) = $chain;

        $this->assertTrue($subordinate1 instanceof Employee);
        $this->assertEquals(6, $subordinate1->getEmpNumber());

        $this->assertTrue($subordinate7 instanceof Employee);
        $this->assertEquals(92, $subordinate7->getEmpNumber());
    }

    /**
     * @group ReportingChain 
     */
    public function testGetSubordinateList_ReportingChain_3LevelHierarchy() {
        $subordinates = $this->dao->getSubordinateList(61, false, true);
        $this->assertTrue(is_array($subordinates));
        $this->assertEquals(15, count($subordinates));

        $this->assertTrue($subordinates[0] instanceof Employee);
        $this->assertEquals(25, $subordinates[0]->getEmpNumber());
        $this->assertEquals(36, $subordinates[1]->getEmpNumber());
        $this->assertEquals(41, $subordinates[2]->getEmpNumber());
        $this->assertEquals(68, $subordinates[3]->getEmpNumber());
        $this->assertEquals(87, $subordinates[4]->getEmpNumber());
        $this->assertEquals(6, $subordinates[5]->getEmpNumber());
        $this->assertEquals(7, $subordinates[6]->getEmpNumber());
        $this->assertEquals(20, $subordinates[7]->getEmpNumber());
        $this->assertEquals(23, $subordinates[8]->getEmpNumber());
        $this->assertEquals(31, $subordinates[9]->getEmpNumber());
        $this->assertEquals(71, $subordinates[10]->getEmpNumber());
        $this->assertEquals(92, $subordinates[11]->getEmpNumber());
        $this->assertEquals(94, $subordinates[12]->getEmpNumber());
        $this->assertEquals(24, $subordinates[13]->getEmpNumber());
        $this->assertEquals(53, $subordinates[14]->getEmpNumber());
    }

    /**
     * @group ReportingChain 
     */
    public function testGetSubordinateList_ReportingChain_4LevelHierarchy() {
        $subordinates = $this->dao->getSubordinateList(97, false, true);
        $this->assertTrue(is_array($subordinates));
        $this->assertEquals(16, count($subordinates));

        $this->assertTrue($subordinates[0] instanceof Employee);
        $this->assertEquals(61, $subordinates[0]->getEmpNumber());
        $this->assertEquals(25, $subordinates[1]->getEmpNumber());
        $this->assertEquals(36, $subordinates[2]->getEmpNumber());
        $this->assertEquals(41, $subordinates[3]->getEmpNumber());
        $this->assertEquals(68, $subordinates[4]->getEmpNumber());
        $this->assertEquals(87, $subordinates[5]->getEmpNumber());
        $this->assertEquals(6, $subordinates[6]->getEmpNumber());
        $this->assertEquals(7, $subordinates[7]->getEmpNumber());
        $this->assertEquals(20, $subordinates[8]->getEmpNumber());
        $this->assertEquals(23, $subordinates[9]->getEmpNumber());
        $this->assertEquals(31, $subordinates[10]->getEmpNumber());
        $this->assertEquals(71, $subordinates[11]->getEmpNumber());
        $this->assertEquals(92, $subordinates[12]->getEmpNumber());
        $this->assertEquals(94, $subordinates[13]->getEmpNumber());
        $this->assertEquals(24, $subordinates[14]->getEmpNumber());
        $this->assertEquals(53, $subordinates[15]->getEmpNumber());
    }

}
