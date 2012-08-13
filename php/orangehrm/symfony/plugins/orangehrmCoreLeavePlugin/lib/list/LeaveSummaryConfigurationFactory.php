<?php


class LeaveSummaryConfigurationFactory extends ohrmListConfigurationFactory {

    protected function init() {
        $header1 = new LeaveSummaryEmployeeNameHeader();
        $header2 = new LeaveSummaryLeaveTypeHeader();
        $header3 = new LeaveSummaryLeaveEntitlementHeader();
        $header4 = new LeaveSummaryValueHeader();
        $header5 = new LeaveSummaryValueHeader();
        $header6 = new LeaveSummaryValueHeader();

        $header1->populateFromArray(array(
            'name' => 'Employee Name',
            'width' => '20%',
            'isSortable' => false,
            'elementType' => 'leaveSummaryEmployeeName',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'linkable' => 'is_accessible',
                'loggedUserId' => 'logged_user_id',
                'terminatedEmployee' => 'termination_id',
                'labelGetter' => 'emp_fullname',
                'placeholderGetters' => array('id' => 'emp_number'),
                'urlPattern' => '../pim/viewPersonalDetails/empNumber/{id}',
                'hasHiddenField' => true,
                'hiddenFieldName' => 'hdnEmpId[]',
                'hiddenFieldValueGetter' => 'emp_number',
            ),
        ));

        $header2->populateFromArray(array(
            'name' => 'Leave Type',
            'width' => '20%',
            'isSortable' => false,
            'elementType' => 'leaveSummaryLeaveType',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'getter' => 'leave_type_name',
                'hasHiddenField' => true,
                'hiddenFieldName' => 'hdnLeaveTypeId[]',
                'hiddenFieldValueGetter' => 'leave_type_id',
                'leaveTypeStatus' => 'leave_type_status',
            ),
        ));

        $header3->populateFromArray(array(
            'name' => 'Leave Entitled (Days)',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'leaveSummaryLeaveEntitlement',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array(
                'getter' => 'no_of_days_allotted',
                'name' => 'txtLeaveEntitled[]',
                'readOnly' => (!self::$permissions->canUpdate()),
            ),
        ));

        $header4->populateFromArray(array(
            'name' => 'Leave Scheduled (Days)',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'leaveSummaryValue',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array(
                'getter' => 'leave_info',
                'elementKey' => 1,
                'linkable' => 'having_scheduled',
                'loggedUserId' => 'logged_user_id',
                'labelGetter' => 'leave_scheduled',
                'placeholderGetters' => array('id' => 'emp_number', 'lty' => 'leave_type_id', 'lpi' => 'leave_period_id'),
                'urlPattern' => 'viewLeaveList?txtEmpID={id}&leaveTypeId={lty}&status=2&leavePeriodId={lpi}',
                'altUrlPattern' => 'viewMyLeaveList?txtEmpID={id}&leaveTypeId={lty}&status=2&leavePeriodId={lpi}',
            ),
        ));
        
        

        $header5->populateFromArray(array(
            'name' => 'Leave Taken (Days)',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'leaveSummaryValue',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array(
                'getter' => 'leave_info',
                'loggedUserId' => 'logged_user_id',
                'elementKey' => 2,
                'linkable' => 'having_taken',
                'labelGetter' => 'leave_taken',
                'placeholderGetters' => array('id' => 'emp_number', 'lty' => 'leave_type_id', 'lpi' => 'leave_period_id'),
                'urlPattern' => 'viewLeaveList?txtEmpID={id}&leaveTypeId={lty}&status=3&leavePeriodId={lpi}',
                'altUrlPattern' => 'viewMyLeaveList?txtEmpID={id}&leaveTypeId={lty}&status=3&leavePeriodId={lpi}',
             ),
        ));

        $header6->populateFromArray(array(
            'name' => 'Leave Balance (Days)',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'leaveSummaryValue',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array(
            'getter' => 'leave_info',
            'elementKey' => 0,
        ),
        ));

        $this->headers = array($header1, $header2, $header3, $header4, $header5, $header6);
        
        if (!self::$permissions->canUpdate()) {
            $this->runtimeDefinitions = array(
                'buttons' => array(),
            );
        }
        
    }
    
    public function getClassName() {
        return 'EmployeeLeaveEntitlement';
    }

}
