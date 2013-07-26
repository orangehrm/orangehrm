<?php

class LeaveListConfigurationFactory extends ohrmListConfigurationFactory {
    
    protected static $listMode;
    protected static $loggedInEmpNumber;
    
    public function init() {
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        
        $header1 = new LeaveListHeader();
        $header2 = new LeaveListHeader();
        $header3 = new LeaveListHeader();
        $header4 = new LeaveListHeader();
        $header5 = new LeaveListHeader();
        $header6 = new LeaveListHeader();
        $header7 = new LeaveListHeader();
        $header8 = new LeaveListHeader();

        $header1->populateFromArray(array(
            'name' => 'Date',
            'width' => '24%',
            'isSortable' => false,
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getLeaveDateRange'),
                'placeholderGetters' => array('id' => 'getId'),
                'urlPattern' => public_path('index.php/leave/viewLeaveRequest/id/{id}'),
            ),
        ));

        $header2->populateFromArray(array(
            'name' => 'Employee Name',
            'width' => '18%',
            'isSortable' => false,
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getEmployee', 'getFullName'),
                'placeholderGetters' => array('id' => 'getEmpNumber'),
                'urlPattern' => public_path('index.php/pim/viewEmployee/empNumber/{id}'),
            ),
        ));

        $header3->populateFromArray(array(
            'name' => 'Leave Type',
            'width' => '10%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => array('getLeaveType', 'getDescriptiveLeaveTypeName')),
        ));

        $header4->populateFromArray(array(
            'name' => 'Number of Days',
            'width' => '9%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'right',
            'elementProperty' => array('getter' => 'getNumberOfDays'),
        ));

        $header8->populateFromArray(array(
            'name' => 'Leave Balance (Days)',
            'width' => '12%',
            'isSortable' => false,
            'elementType' => 'leaveListBalance',
            'textAlignmentStyle' => 'right',
        ));

        $header5->populateFromArray(array(
            'name' => 'Status',
            'width' => '12%',
            'isSortable' => false,
            'elementType' => 'link',
            'filters' => array('I18nCellFilter' => array()
                              ),
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getLeaveBreakdown'),
                'placeholderGetters' => array('id' => 'getId'),
                'urlPattern' => public_path('index.php/leave/viewLeaveRequest/id/{id}'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'leaveRequestStatus[{id}]',
                'hiddenFieldId' => 'hdnLeaveRequestStatus_{id}',
                'hiddenFieldValueGetter' => 'getLeaveStatusId',
            ),
        ));

        $header6->populateFromArray(array(
            'name' => 'Comments',
            'width' => '17%',
            'isSortable' => false,
            'elementType' => 'leaveComment',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'getter' => 'getLeaveRequestComment',
                'idPattern' => 'hdnLeaveComment-{id}',
                'namePattern' => 'leaveComments[{id}]',
                'placeholderGetters' => array('id' => 'getId'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'leaveRequest[{id}]',
                'hiddenFieldId' => 'hdnLeaveRequest_{id}',
                'hiddenFieldValueGetter' => 'getId',
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
                'options' => array($leaveRequestService, 'getLeaveRequestActions', array(self::RECORD, self::$loggedInEmpNumber)),
                'namePattern' => 'select_leave_action_{id}',
                'idPattern' => 'select_leave_action_{id}',
                'hasHiddenField' => true,
                'hiddenFieldName' => '{eimId}-{leaveTypeId}',
                'hiddenFieldId' => '{eimId}-{leaveTypeId}',
                'hiddenFieldValueGetter' => 'getNumberOfDays',
                'hiddenFieldClass' => 'quotaHolder',
                'placeholderGetters' => array(
                    'id' => 'getId',
                    'eimId' => 'getEmpNumber',
                    'leaveTypeId' => 'getLeaveTypeId'
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
    
    public static function setLoggedInEmpNumber($empNumber) {
        self::$loggedInEmpNumber = $empNumber;
    }     
}
