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
/**
 * @group CoreLeave 
 */
class LeaveSummaryServiceTest extends PHPUnit_Framework_TestCase {

    protected $leaveSummaryService;

    public function setup() {

        $this->leaveSummaryService  = new LeaveSummaryService();

    }

    public function testSearchLeaveSummary() {

        $clues['cmbEmpId'] = 1;
        $clues['userType'] = 'ESS';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';
        $clues['cmbWithTerminated'] = '';

        $intendedResults[0] = array
        (
            'emp_fullname' => 'Ashley Abel',
            'leave_type_id' => 'LTY001',
            'available_flag' => 1,
            'leave_period_id' => null,
            'employee_id' => null,
            'emp_number' => 2,
            'termination_id' => null,
            'leave_type_name' => 'Casual',
            'no_of_days_allotted' => '0.00',
            'leave_brought_forward' => null,
            'leave_carried_forward' => null,
            'leave_info' => '0.00_0.00_0.00',
            'having_taken' => false,
            'leave_taken' => '0.00',
            'having_scheduled' => false,
            'leave_scheduled' => '0.00',
            'logged_user_id' => 1,
            'leave_type_status' => true
        );
        
        $intendedResults[1] = array
        (
            'emp_fullname' => 'Ashley Abel',
            'leave_type_id' => 'LTY002',
            'available_flag' => 1,
            'leave_period_id' => null,
            'employee_id' => null,
            'emp_number' => 2,
            'termination_id' => null,
            'leave_type_name' => 'Medical',
            'no_of_days_allotted' => '0.00',
            'leave_brought_forward' => null,
            'leave_carried_forward' => null,
            'leave_info' => '0.00_0.00_0.00',
            'having_taken' => false,
            'leave_taken' => '0.00',
            'having_scheduled' => false,
            'leave_scheduled' => '0.00',
            'logged_user_id' => 1,
            'leave_type_status' => true
        );
        
        $leaveSummaryDao = $this->getMock('LeaveSummaryDao', array('searchLeaveSummary'));
        $leaveSummaryDao->expects($this->once())
                        ->method('searchLeaveSummary')
                        ->with($clues, 0, 20)
                        ->will($this->returnValue($intendedResults));

        // TODO: 'BasicUserRoleManager' is used directly. Should be accessed via UserRoleManagerFactory::getUserRoleManager()
        $userRoleManagerMock = $this->getMock('BasicUserRoleManager', array('getAccessibleEntityIds', 'getUser', 'getDataGroupPermissions'));
        $userRoleManagerMock->expects($this->once())
                        ->method('getAccessibleEntityIds')
                        ->with('Employee')
                        ->will($this->returnValue(array(1, 2, 3, 4, 5)));
        $user = new SystemUser();
        $user->setEmpNumber(2);
        
        $userRoleManagerMock->expects($this->once())
                        ->method('getUser')
                        ->will($this->returnValue($user));
        
        $dataGroupPermission = new ResourcePermission(true, true, true, true);
        
        $userRoleManagerMock->expects($this->exactly(2))
                        ->method('getDataGroupPermissions')
                        ->with(array('leave_summary'), array(), array(), true)
                        ->will($this->returnValue($dataGroupPermission));
        

        $this->leaveSummaryService->setLeaveSummaryDao($leaveSummaryDao);
        $this->leaveSummaryService->setUserRoleManager($userRoleManagerMock);

        $result = $this->leaveSummaryService->searchLeaveSummary($clues, 0, 20, 1);

        $this->compareArrays($intendedResults, $result);

    }

    public function testSearchLeaveSummaryCount() {

        $clues['cmbEmpId'] = 1;
        $clues['userType'] = 'ESS';
        $clues['cmbLeaveType'] = '';
        $clues['cmbSubDivision'] = '';
        $clues['cmbJobTitle'] = '';
        $clues['cmbLocation'] = '';
        $clues['subordinates'] = '';
        $clues['cmbWithTerminated'] = '';
        
        $leaveSummaryDao = $this->getMock('LeaveSummaryDao');
        $leaveSummaryDao->expects($this->once())
                        ->method('searchLeaveSummaryCount')
                        ->with($clues)
                        ->will($this->returnValue(2));

        $this->leaveSummaryService->setLeaveSummaryDao($leaveSummaryDao);

        $result = $this->leaveSummaryService->searchLeaveSummaryCount($clues);

        $this->assertEquals(2, $result);

    }
    
    protected function compareArrays($expected, $actual) {
        $this->assertEquals(count($expected), count($actual));
        
        $diff = array_diff($expected, $actual);
        $this->assertEquals(0, count($diff), $diff);       
    }

}

class MySqlResource {

    public function fetch() {
        return false;
    }
}

?>
