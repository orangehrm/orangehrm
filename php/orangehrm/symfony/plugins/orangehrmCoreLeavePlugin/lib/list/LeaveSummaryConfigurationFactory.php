<?php


class LeaveSummaryConfigurationFactory extends ohrmListConfigurationFactory {

    protected function init() {
        $header1 = new ListHeader();
        $header2 = new ListHeader();
        $header3 = new ListHeader();
        $header4 = new LeaveSummaryLinkHeader();
        $header5 = new LeaveSummaryLinkHeader();
        $header6 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Employee Name',
            'width' => '20%',
            'isSortable' => false,
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'linkable' => array('isEmployeeDetailsAccessible', array()),
                'labelGetter' => array('getEmployee', 'getFullName'),
                'placeholderGetters' => array('id' => 'getEmployeeId'),
                'urlPattern' => '../pim/viewPersonalDetails/empNumber/{id}',
                'hasHiddenField' => true,
                'hiddenFieldName' => 'hdnEmpId[]',
                'hiddenFieldValueGetter' => 'getEmployeeId',
            ),
        ));

        $header2->populateFromArray(array(
            'name' => 'Leave Type',
            'width' => '20%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'getter' => array('getLeaveType', 'getDescriptiveLeaveTypeName'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'hdnLeaveTypeId[]',
                'hiddenFieldValueGetter' => 'getLeaveTypeId',

            ),
        ));

        $header3->populateFromArray(array(
            'name' => 'Leave Entitled (Days)',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'textbox',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array(
                'getter' => 'getNoOfDaysAllotted',
                'name' => 'txtLeaveEntitled[]',
                'readOnly' => (!self::$permissions->canUpdate()),
            ),
        ));

        $header4->populateFromArray(array(
            'name' => 'Leave Scheduled (Days)',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'leaveSummaryLink',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array(
                'getter' => 'getLeaveScheduled',
                 /* FIXME: This hidden field is added because the old leave 
                  * summary contains one. Ideally, the value of leave period id 
                  * need to be taken from a single field
                  */
                'linkable' => array('isThereLeaveScheduled', array()),
                'labelGetter' => array('getLeaveScheduled'),
                'placeholderGetters' => array('id' => 'getEmployeeId', 'lty' => 'getLeaveTypeId', 'lpi' => 'getLeavePeriodId', 'empstat' => 'getEmployeeStatus'),
                'urlPattern' => 'viewLeaveList?txtEmpID={id}&leaveTypeId={lty}&status=2&leavePeriodId={lpi}&EmpStatus={empstat}',
                'hasHiddenField' => true,
                'hiddenFieldName' => 'hdnLeavePeriodId[]',
                'hiddenFieldValueGetter' => 'getLeavePeriodId',
                'altUrlPattern' => 'viewMyLeaveList?txtEmpID={id}&leaveTypeId={lty}&status=2&leavePeriodId={lpi}&EmpStatus={empstat}',
            ),
        ));

        $header5->populateFromArray(array(
            'name' => 'Leave Taken (Days)',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'leaveSummaryLink',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array(
                'getter' => 'getLeaveTaken',
                'linkable' => array('isThereLeaveTaken', array()),
                'labelGetter' => array('getLeaveTaken'),
                'placeholderGetters' => array('id' => 'getEmployeeId', 'lty' => 'getLeaveTypeId', 'lpi' => 'getLeavePeriodId'),
                'urlPattern' => 'viewLeaveList?txtEmpID={id}&leaveTypeId={lty}&status=3&leavePeriodId={lpi}',
                'altUrlPattern' => 'viewMyLeaveList?txtEmpID={id}&leaveTypeId={lty}&status=3&leavePeriodId={lpi}',
             ),
        ));

        $header6->populateFromArray(array(
            'name' => 'Leave Balance (Days)',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array('getter' => 'getLeaveBalance'),
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
