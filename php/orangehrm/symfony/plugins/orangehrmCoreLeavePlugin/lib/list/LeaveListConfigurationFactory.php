<?php

class LeaveListConfigurationFactory extends ohrmListConfigurationFactory {
    
    protected static $listMode;
    
    public function init() {
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        
        $header1 = new ListHeader();
        $header2 = new ListHeader();
        $header3 = new ListHeader();
        $header4 = new ListHeader();
        $header5 = new ListHeader();
        $header6 = new ListHeader();
        $header7 = new LeaveListActionHeader();
        $header8 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Date',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getLeaveDateRange'),
                'placeholderGetters' => array('id' => 'getLeaveRequestId'),
                'urlPattern' => public_path('index.php/leave/viewLeaveRequest/id/{id}'),
            ),
        ));

        $header2->populateFromArray(array(
            'name' => 'Employee Name',
            'width' => '12%',
            'isSortable' => false,
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getEmployee', 'getFullName'),
                'placeholderGetters' => array('id' => 'getEmployeeId'),
                'urlPattern' => public_path('index.php/pim/viewPersonalDetails/empNumber/{id}'),
            ),
        ));

        $header3->populateFromArray(array(
            'name' => 'Leave Type',
            'width' => '12%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => array('getLeaveType', 'getDescriptiveLeaveTypeName')),
        ));

        $header4->populateFromArray(array(
            'name' => 'Number of Days',
            'width' => '12%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array('getter' => 'getNumberOfDays'),
        ));

        $header8->populateFromArray(array(
            'name' => 'Leave Balance (Days)',
            'width' => '12%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array('getter' => 'getLeaveBalance'),
        ));

        $header5->populateFromArray(array(
            'name' => 'Status',
            'width' => '9%',
            'isSortable' => false,
            'elementType' => 'link',
            'filters' => array('I18nCellFilter' => array()
                              ),
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getStatus'),
                'placeholderGetters' => array('id' => 'getLeaveRequestId'),
                'urlPattern' => public_path('index.php/leave/viewLeaveRequest/id/{id}'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'leaveRequestStatus[{id}]',
                'hiddenFieldId' => 'hdnLeaveRequestStatus_{id}',
                'hiddenFieldValueGetter' => 'getLeaveStatusId',
            ),
        ));

        $header6->populateFromArray(array(
            'name' => 'Comments',
            'width' => '18%',
            'isSortable' => false,
            'elementType' => 'comment',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'getter' => 'getLeaveComments',
                'idPattern' => 'hdnLeaveComment-{id}',
                'namePattern' => 'leaveComments[{id}]',
                'placeholderGetters' => array('id' => 'getLeaveRequestId'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'leaveRequest[{id}]',
                'hiddenFieldId' => 'hdnLeaveRequest_{id}',
                'hiddenFieldValueGetter' => 'getLeaveRequestId',
            ),
        ));

        
        $leaveRequestService = new LeaveRequestService();
        $header7->populateFromArray(array(
            'name' => 'Actions',
            'width' => '10%',
            'isSortable' => false,
            'isExportable' => false,
            'elementType' => 'leaveListAction',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'classPattern' => 'select_action quotaSelect',
                'defaultOption' => array('label' => 'Select Action', 'value' => ''),
                'hideIfEmpty' => true,
                'options' => array($leaveRequestService, 'getLeaveRequestActions', array(self::RECORD, self::$userId, self::$listMode)),
                'namePattern' => 'select_leave_action_{id}',
                'idPattern' => 'select_leave_action_{id}',
                'hasHiddenField' => true,
                'hiddenFieldName' => '{eimId}-{leaveTypeId}-{leavePeriodId}',
                'hiddenFieldId' => '{eimId}-{leaveTypeId}-{leavePeriodId}',
                'hiddenFieldValueGetter' => 'getNumberOfDays',
                'hiddenFieldClass' => 'quotaHolder',
                'placeholderGetters' => array(
                    'id' => 'getLeaveRequestId',
                    'eimId' => 'getEmpNumber',
                    'leaveTypeId' => 'getLeaveTypeId',
                    'leavePeriodId' => 'getLeavePeriodId',
                ),
            ),
        ));

        $this->headers = array($header1, $header2, $header3, $header8, $header4, $header5, $header6, $header7);
    }
    
    public function getClassName() {
        return 'LeaveRequest';
    }
    
    public static function setListMode($listMode) {
        self::$listMode = $listMode;
    }
}
