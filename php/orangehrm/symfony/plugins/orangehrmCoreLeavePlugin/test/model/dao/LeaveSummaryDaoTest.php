<?php
/*
 *
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
 *
*/

require_once  sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 * @group CoreLeave 
 */
class LeaveSummaryDaoTest extends PHPUnit_Framework_TestCase {

    public $leaveSummaryDao ;

    protected function setUp() {

        $this->leaveSummaryDao = new LeaveSummaryDao();
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/LeaveSummaryDao.yml');

    }

    public function testSearchLeaveSummaryWithAllRecords() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';
        $clues['cmbWithTerminated'] = '';

        $result = $this->leaveSummaryDao->searchLeaveSummary($clues,0,20);

        /*Checking SearchLeaveSummaryCount function*/
        $count = $this->leaveSummaryDao->searchLeaveSummaryCount($clues);
        $this->assertEquals(8, $count);
        
        /* Checking records count */
        $this->assertEquals(8, count($result));
        /* Checking values and order */
        $this->assertEquals(2, $result[0]['emp_number']);
        $this->assertEquals('Ashley Abel', $result[0]['emp_fullname']);
        $this->assertEquals('LTY001', $result[0]['leave_type_id']);
        $this->assertEquals('Casual', $result[0]['leave_type_name']);

        $this->assertEquals(2, $result[1]['emp_number']);
        $this->assertEquals('Ashley Abel', $result[1]['emp_fullname']);
        $this->assertEquals('LTY002', $result[1]['leave_type_id']);
        $this->assertEquals('Medical', $result[1]['leave_type_name']);

    }
    
    public function testSearchLeaveSummaryWithLeaveType() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = 'LTY001';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';
        $clues['cmbWithTerminated'] = '';

        $result = $this->leaveSummaryDao->searchLeaveSummary($clues,0,20);
        
        /*Checking SearchLeaveSummaryCount function*/
        $count = $this->leaveSummaryDao->searchLeaveSummaryCount($clues);
        $this->assertEquals(4, $count);
        
        /* Checking records count */
        $this->assertEquals(4, count($result));

        /* Checking values and order */

        $this->assertEquals(2, $result[0]['emp_number']);
        $this->assertEquals('Ashley Abel', $result[0]['emp_fullname']);
        $this->assertEquals('LTY001', $result[0]['leave_type_id']);
        $this->assertEquals('Casual', $result[0]['leave_type_name']);

        $this->assertEquals(5, $result[3]['emp_number']);
        $this->assertEquals('James Abrahamson', $result[3]['emp_fullname']);
        $this->assertEquals('LTY001', $result[3]['leave_type_id']);
        $this->assertEquals('Casual', $result[3]['leave_type_name']);

    }
    
    public function testSearchLeaveSummaryWithTerminatedEmployee() {

        $clues['cmbEmpId'] = 1;
        $clues['userType'] = 'ESS';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';
        $clues['cmbWithTerminated'] = '';

        $result = $this->leaveSummaryDao->searchLeaveSummary($clues,0,20);
        
        /*Checking SearchLeaveSummaryCount function*/
        $count = $this->leaveSummaryDao->searchLeaveSummaryCount($clues);
        $this->assertEquals(0, $count);
        
        /* Checking records count */
        $this->assertEquals(0, count($result));
        
        $clues['cmbEmpId'] = 1;
        $clues['userType'] = 'ESS';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';
        $clues['cmbWithTerminated'] = '1';

        $result = $this->leaveSummaryDao->searchLeaveSummary($clues,0,20);
        
        /*Checking SearchLeaveSummaryCount function*/
        $count = $this->leaveSummaryDao->searchLeaveSummaryCount($clues);
        $this->assertEquals(2, $count);
        
        /* Checking records count */
        $this->assertEquals(2, count($result));

        /* Checking values and order */

        $this->assertEquals(1, $result[0]['emp_number']);
        $this->assertEquals('Kayla Abbey', $result[0]['emp_fullname']);
        $this->assertEquals('LTY001', $result[0]['leave_type_id']);
        $this->assertEquals('Casual', $result[0]['leave_type_name']);

        $this->assertEquals(1, $result[1]['emp_number']);
        $this->assertEquals('Kayla Abbey', $result[1]['emp_fullname']);
        $this->assertEquals('LTY002', $result[1]['leave_type_id']);
        $this->assertEquals('Medical', $result[1]['leave_type_name']);

    }
    
    public function testSearchLeaveSummaryWithEmployeeId() {

        $clues['cmbEmpId'] = 2;
        $clues['userType'] = 'ESS';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';
        $clues['cmbWithTerminated'] = '';

        $result = $this->leaveSummaryDao->searchLeaveSummary($clues,0,20);
        
        /*Checking SearchLeaveSummaryCount function*/
        $count = $this->leaveSummaryDao->searchLeaveSummaryCount($clues);
        $this->assertEquals(2, $count);
        
        /* Checking records count */
        $this->assertEquals(2, count($result));
        
        $this->assertEquals(2, $result[0]['emp_number']);
        $this->assertEquals('Ashley Abel', $result[0]['emp_fullname']);
        $this->assertEquals('LTY001', $result[0]['leave_type_id']);
        $this->assertEquals('Casual', $result[0]['leave_type_name']);

        $this->assertEquals(2, $result[1]['emp_number']);
        $this->assertEquals('Ashley Abel', $result[1]['emp_fullname']);
        $this->assertEquals('LTY002', $result[1]['leave_type_id']);
        $this->assertEquals('Medical', $result[1]['leave_type_name']);

    }
    
    public function testSearchLeaveSummaryWithSubDivision() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = 2;
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';
        $clues['cmbWithTerminated'] = '';

        $result = $this->leaveSummaryDao->searchLeaveSummary($clues,0,20);

        /*Checking SearchLeaveSummaryCount function*/
        $count = $this->leaveSummaryDao->searchLeaveSummaryCount($clues);
        $this->assertEquals(2, $count);
        
        /* Checking records count */
        $this->assertEquals(2, count($result));

        $this->assertEquals(2, $result[0]['emp_number']);
        $this->assertEquals('Ashley Abel', $result[0]['emp_fullname']);
        $this->assertEquals('LTY001', $result[0]['leave_type_id']);
        $this->assertEquals('Casual', $result[0]['leave_type_name']);

        $this->assertEquals(2, $result[1]['emp_number']);
        $this->assertEquals('Ashley Abel', $result[1]['emp_fullname']);
        $this->assertEquals('LTY002', $result[1]['leave_type_id']);
        $this->assertEquals('Medical', $result[1]['leave_type_name']);
        
    }
    
    public function testSearchLeaveSummaryWithJobTitle() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = 1;
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';
        $clues['cmbWithTerminated'] = '';

        $result = $this->leaveSummaryDao->searchLeaveSummary($clues,0,20);

        /*Checking SearchLeaveSummaryCount function*/
        $count = $this->leaveSummaryDao->searchLeaveSummaryCount($clues);
        $this->assertEquals(2, $count);
        
        /* Checking records count */
        $this->assertEquals(2, count($result));

        $this->assertEquals(5, $result[0]['emp_number']);
        $this->assertEquals('James Abrahamson', $result[0]['emp_fullname']);
        $this->assertEquals('LTY001', $result[0]['leave_type_id']);
        $this->assertEquals('Casual', $result[0]['leave_type_name']);

        $this->assertEquals(5, $result[1]['emp_number']);
        $this->assertEquals('James Abrahamson', $result[1]['emp_fullname']);
        $this->assertEquals('LTY002', $result[1]['leave_type_id']);
        $this->assertEquals('Medical', $result[1]['leave_type_name']);

    }
    
    public function testSearchLeaveSummarysWithLocation() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = '';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = 1;
        $clues['subordinates'] = '';
        $clues['cmbWithTerminated'] = '';

        $result = $this->leaveSummaryDao->searchLeaveSummary($clues,0,20);

        /*Checking SearchLeaveSummaryCount function*/
        $count = $this->leaveSummaryDao->searchLeaveSummaryCount($clues);
        $this->assertEquals(2, $count);
        
        /* Checking records count */
        $this->assertEquals(2, count($result));

        $this->assertEquals(4, $result[0]['emp_number']);
        $this->assertEquals('Landon Abrahams', $result[0]['emp_fullname']);
        $this->assertEquals('LTY001', $result[0]['leave_type_id']);
        $this->assertEquals('Casual', $result[0]['leave_type_name']);

        $this->assertEquals(4, $result[1]['emp_number']);
        $this->assertEquals('Landon Abrahams', $result[1]['emp_fullname']);
        $this->assertEquals('LTY002', $result[1]['leave_type_id']);
        $this->assertEquals('Medical', $result[1]['leave_type_name']);

    }
    
    public function testSsearchLeaveSummaryWithSubordinates() {

        $clues['cmbEmpId'] = '';
        $clues['userType'] = 'Supervisor';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = array(2, 5);
        $clues['cmbWithTerminated'] = '';

        $result = $this->leaveSummaryDao->searchLeaveSummary($clues,0,20);

        /*Checking SearchLeaveSummaryCount function*/
        $count = $this->leaveSummaryDao->searchLeaveSummaryCount($clues);
        $this->assertEquals(4, $count);
        
        /* Checking records count */
        $this->assertEquals(4, count($result));

        $this->assertEquals(2, $result[0]['emp_number']);
        $this->assertEquals('Ashley Abel', $result[0]['emp_fullname']);
        $this->assertEquals('LTY001', $result[0]['leave_type_id']);
        $this->assertEquals('Casual', $result[0]['leave_type_name']);

        $this->assertEquals(5, $result[2]['emp_number']);
        $this->assertEquals('James Abrahamson', $result[2]['emp_fullname']);
        $this->assertEquals('LTY001', $result[2]['leave_type_id']);
        $this->assertEquals('Casual', $result[2]['leave_type_name']);

    }


}


?>