<?php

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
class LeaveRequestDaoSearchTest extends PHPUnit_Framework_TestCase {

    /**
     * Set up method
     */
    protected function setUp() {

        TestDataService::truncateTables(array('Employee', 'LeaveType', 'LeavePeriod', 'Leave'));
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/LeaveRequestDaoSearch.yml');
    }

    /**
     * @group orangehrmLeaveListDataExtractorCsvPlugin
     */
    public function testGetLeaveRequestSearchResultAsArray1() {

        $searchParams = new ParameterObject(array(
                    'dateRange' => new DateRange("2010-09-01", "2010-09-07"),
                    'statuses' => null,
                    'employeeFilter' => null,
                    'leavePeriod' => null,
                    'leaveType' => null,
                    'noOfRecordsPerPage' => '',
                    'cmbWithTerminated' => 'yes',
                ));

        $dao = new LeaveRequestDao();
        $this->assertEquals(3, sizeof($dao->getLeaveRequestSearchResultAsArray($searchParams)));
    }

    /**
     * @group orangehrmLeaveListDataExtractorCsvPlugin
     */
    public function testGetDetailedLeaveRequestSearchResultAsArray2() {

        $searchParams = new ParameterObject(array(
                    'dateRange' => new DateRange("2010-09-01", "2010-09-07"),
                    'statuses' => null,
                    'employeeFilter' => null,
                    'leavePeriod' => null,
                    'leaveType' => null,
                    'noOfRecordsPerPage' => '',
                    'cmbWithTerminated' => 'yes',
                ));

        $dao = new LeaveRequestDao();
        $this->assertEquals(6, sizeof($dao->getDetailedLeaveRequestSearchResultAsArray($searchParams)));
    }

    /**
     * @group orangehrmLeaveListDataExtractorCsvPlugin
     */
    public function testGetLeaveRequestSearchResultAsArray3() {

        $searchParams = new ParameterObject(array(
                    'dateRange' => new DateRange("2010-09-01", "2010-09-07"),
                    'statuses' => null,
                    'employeeFilter' => array(3),
                    'leavePeriod' => null,
                    'leaveType' => null,
                    'noOfRecordsPerPage' => '',
                    'cmbWithTerminated' => 'yes',
                ));

        $dao = new LeaveRequestDao();
        $this->assertEquals(1, sizeof($dao->getLeaveRequestSearchResultAsArray($searchParams)));
    }

    /**
     * @group orangehrmLeaveListDataExtractorCsvPlugin
     */
    public function testGetDetailedLeaveRequestSearchResultAsArray4() {

        $searchParams = new ParameterObject(array(
                    'dateRange' => new DateRange("2010-09-01", "2010-09-07"),
                    'statuses' => null,
                    'employeeFilter' => array(3),
                    'leavePeriod' => null,
                    'leaveType' => null,
                    'noOfRecordsPerPage' => '',
                    'cmbWithTerminated' => 'yes',
                ));

        $dao = new LeaveRequestDao();
        $this->assertEquals(2, sizeof($dao->getDetailedLeaveRequestSearchResultAsArray($searchParams)));
    }

    /**
     * @group orangehrmLeaveListDataExtractorCsvPlugin
     */
    public function testGetLeaveRequestSearchResultAsArray5() {

        $searchParams = new ParameterObject(array(
                    'dateRange' => new DateRange("2010-09-01", "2010-09-07"),
                    'statuses' => null,
                    'employeeFilter' => null,
                    'leavePeriod' => null,
                    'leaveType' => 'LTY002',
                    'noOfRecordsPerPage' => '',
                    'cmbWithTerminated' => 'yes',
                ));

        $dao = new LeaveRequestDao();
        $this->assertEquals(1, sizeof($dao->getLeaveRequestSearchResultAsArray($searchParams)));
    }

    /**
     * @group orangehrmLeaveListDataExtractorCsvPlugin
     */
    public function testGetDetailedLeaveRequestSearchResultAsArray6() {

        $searchParams = new ParameterObject(array(
                    'dateRange' => new DateRange("2010-09-01", "2010-09-07"),
                    'statuses' => null,
                    'employeeFilter' => null,
                    'leavePeriod' => null,
                    'leaveType' => 'LTY002',
                    'noOfRecordsPerPage' => '',
                    'cmbWithTerminated' => 'yes',
                ));

        $dao = new LeaveRequestDao();
        $this->assertEquals(2, sizeof($dao->getDetailedLeaveRequestSearchResultAsArray($searchParams)));
    }

}

?>
