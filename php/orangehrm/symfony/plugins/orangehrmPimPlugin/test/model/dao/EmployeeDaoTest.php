<?php

require_once 'PHPUnit/Framework.php';
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

class EmployeeDaoTest extends PHPUnit_Framework_TestCase {

    private $testCase;
    private $employeeDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->employeeDao = new EmployeeDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmPimPlugin/test/fixtures/EmployeeDao.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * Testing getEmployeeListAsJson
     */
    public function testGetEmployeeListAsJson() {
        $result = $this->employeeDao->getEmployeeListAsJson();
        $this->assertTrue(!empty($result));
    }

    /**
     * Test saving EmployeePassport without sequence number
     */
    public function testSaveEmployeePassport1() {

        $empPassport = new EmpPassPort();
        $empPassport->setEmpNumber(1);
        $empPassport->country = 'LK';
        $result = $this->employeeDao->saveEmployeePassport($empPassport);
        $this->assertTrue($result);
        $this->assertEquals(1, $empPassport->seqno);
    }

    /**
     * Test saving EmployeePassport
     */
    public function testSaveEmployeePassport2() {

        $empPassport = TestDataService::fetchLastInsertedRecords('EmpPassport', 2);
        $empNumbers = array(1 => 1, 2 => 2);

        foreach ($empPassport as $passport) {

            $this->assertTrue($passport instanceof EmpPassPort);
            $this->assertEquals($empNumbers[$passport->getEmpNumber()], $passport->getEmpNumber());
            $comment = "I add more comments";
            $passport->comments = $comment;
            $result = $this->employeeDao->saveEmployeePassport($passport);
            $this->assertTrue($result);

            $savedPassport = $this->employeeDao->getEmployeePassport($passport->getEmpNumber(), $passport->getSeqno());
            $this->assertEquals($comment, $savedPassport->comments);
            $this->assertEquals($savedPassport, $passport);
        }
    }

    /**
     * Test saving getEmployeePassport returns object
     */
    public function testGetEmployeePassport1() {

        $empPassports = TestDataService::fetchLastInsertedRecords('EmpPassport', 2);

        foreach ($empPassports as $passport) {

            $empPassport = $this->employeeDao->getEmployeePassport($passport->getEmpNumber(), $passport->getSeqno());
            $this->assertEquals($passport, $empPassport);
        }
    }

    /**
     * Test saving getEmployeePassport returns Collection
     */
    public function testGetEmployeePassport2() {

        $empPassports = TestDataService::fetchLastInsertedRecords('EmpPassport', 2);

        foreach ($empPassports as $passport) {

            $collection = $this->employeeDao->getEmployeePassport($passport->getEmpNumber());
            $this->assertTrue($collection instanceof Doctrine_Collection);
        }
    }

    /**
     * Test for getEmployeeTaxExemptions returns Object
     */
    public function testGetEmployeeTaxExemptions() {
        $empTaxExemption = TestDataService::fetchObject('EmpUsTaxExemption', 1);
        $taxObject = $this->employeeDao->getEmployeeTaxExemptions($empTaxExemption->getEmpNumber());
        $this->assertTrue($taxObject instanceof EmpUsTaxExemption);
    }

    /**
     * Test saving Employee Tax Exemptions
     */
    public function testSaveEmployeeTaxExemptions() {

        $empUsTaxExemption = new EmpUsTaxExemption();
        $empUsTaxExemption->setEmpNumber(2);
        $empUsTaxExemption->stateExemptions = 4;
        $result = $this->employeeDao->saveEmployeeTaxExemptions($empUsTaxExemption);
        $this->assertTrue($result);
        $this->assertEquals(2, $empUsTaxExemption->getEmpNumber());
        $this->assertEquals(4, $empUsTaxExemption->stateExemptions);
    }

    /**
     * Test for getMembershipDetails returns collection
     */
    public function testGetMembershipDetails() {

        $employeeMemberDetail1 = TestDataService::loadObjectList('EmployeeMemberDetail', $this->fixture, 'EmployeeMemberDetail');
        $memberDetailArray = $this->employeeDao->getMembershipDetails(1);
        $this->assertTrue($memberDetailArray[0] instanceof EmployeeMemberDetail);
        $this->assertTrue($memberDetailArray[1] instanceof EmployeeMemberDetail);
    }

    /**
     * Test for saveReportMode returns ReportMode doctrine object
     */
    public function testSaveReportMode() {

        $reportMode = new ReportMode();
        $reportMode->reportModeName = "report name";

        $storedReportMode = $this->employeeDao->saveReportMode($reportMode);
        $this->assertTrue($storedReportMode instanceof ReportMode);
        $this->assertEquals($storedReportMode->reportModeName, "report name");
    }

    /**
     * Test for getReportMode returns ReportMode doctrine object
     */
    public function testGetReportMode() {

        $reportMode = $this->employeeDao->getReportMode(3);
        $this->assertTrue($reportMode instanceof ReportMode);
    }

    /**
     * Test for getReportMode returns ReportMode doctrine collection
     */
    public function testGetReportModeList() {

        $reportMode = $this->employeeDao->getReportModeList();
        $this->assertTrue($reportMode[0] instanceof ReportMode);
        $this->assertTrue($reportMode[1] instanceof ReportMode);
    }

    /**
     * Test for getSupervisorListForEmployee returns ReportTo doctrine collection
     */
    public function testGetSupervisorListForEmployee() {

        $supervisorReportToList = $this->employeeDao->getSupervisorListForEmployee(3);
        $this->assertTrue($supervisorReportToList[0] instanceof ReportTo);
        $this->assertEquals($supervisorReportToList[0]->supervisorId, 4);
        $this->assertTrue($supervisorReportToList[0]->getSupervisor() instanceof Employee);
    }

    /**
     * Test for getSubordinateListForEmployee returns ReportTo doctrine collection
     */
    public function testGetSubordinateListForEmployee() {

        $subordinateReportToList = $this->employeeDao->getSubordinateListForEmployee(3);
        $this->assertTrue($subordinateReportToList[0] instanceof ReportTo);
        $this->assertEquals($subordinateReportToList[0]->subordinateId, 1);
        $this->assertTrue($subordinateReportToList[0]->getSubordinate() instanceof Employee);
    }

    /**
     * Test for getReportToObject returns ReportTo doctrine Object
     */
    public function testGetReportToObject() {

        $subordinateReportTo = $this->employeeDao->getReportToObject(4, 3);
        $this->assertTrue($subordinateReportTo instanceof ReportTo);
    }

    /**
     * Test for deleteReportToObject returns boolean
     */
    public function testDeleteReportToObject() {

        $this->assertTrue($this->employeeDao->deleteReportToObject(3, 1));
    }


}
