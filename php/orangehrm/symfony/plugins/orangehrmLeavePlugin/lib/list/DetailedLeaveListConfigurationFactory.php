<?php

class DetailedLeaveListConfigurationFactory extends ohrmListConfigurationFactory {
    
    protected static $listMode;
    protected static $loggedInEmpNumber;

    public function init() {
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        
        $header1 = new LeaveListHeader();
        $header3 = new LeaveListHeader();
        $header4 = new LeaveListHeader();
        $header5 = new LeaveListHeader();
        $header6 = new LeaveListHeader();
        $header7 = new LeaveListHeader();
        $header8 = new LeaveListHeader();

        $header1->populateFromArray(array(
            'name' => 'Date',
            'width' => '20%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getFormattedLeaveDateToView'),
        ));

        $header3->populateFromArray(array(
            'name' => 'Leave Type',
            'width' => '10%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'getter' => array('getLeaveRequest', 'getLeaveType',  'getDescriptiveLeaveTypeName'),
                'hideIfCallback' => 'isNonWorkingDay',
             ),
        ));

        $header8->populateFromArray(array(
            'name' => 'Leave Balance (Days)',
            'width' => '10%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'right',
            'filters' => array('NumberFormatCellFilter' => array()
                              ),            
            'elementProperty' => array('getter' => 'getLeaveBalance'),
        ));

        
        $header4->populateFromArray(array(
            'name' => 'Duration (Hours)',
            'width' => '8%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'right',
            'elementProperty' => array('getter' => 'getLengthHours', 'hideIfCallback' => 'isNonWorkingDay'),
        ));

        $header5->populateFromArray(array(
            'name' => 'Status',
            'width' => '12%',
            'isSortable' => false,
            'elementType' => 'label',
            'filters' => array('CallbackCellFilter' => array('callback' => array('strtolower','ucwords')),
                               'I18nCellFilter' => array()
                              ),
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'getter' => array('getTextLeaveStatus'),
                'default' => __('Non Working Day'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'leave[{id}]',
                'hiddenFieldId' => 'leave-{id}',
                'hiddenFieldValueGetter' => 'getDetailedLeaveListRequestIdHolderValue',
                'hiddenFieldClass' => 'requestIdHolder',
                'placeholderGetters' => array(
                    'id' => 'getId',
                ),
            ),
        ));

        $header6->populateFromArray(array(
            'name' => 'Comments',
            'width' => '30%',
            'isSortable' => false,
            'elementType' => 'leaveComment',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'getter' => 'getLeaveComment',
                'idPattern' => 'hdnLeaveComment-{id}',
                'namePattern' => 'leaveComments[{id}]',
                'placeholderGetters' => array('id' => 'getId'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'leave[{id}]',
                'hiddenFieldId' => 'hdnLeave_{id}',
                'hiddenFieldValueGetter' => 'getId',
                'hideIfCallback' => 'isNonWorkingDay',
            ),
        ));

        $leaveRequestService = new LeaveRequestService();
        $header7->populateFromArray(array(
            'name' => 'Actions',
            'width' => '10%',
            'isSortable' => false,
            'isExportable' => false,
            'elementType' => 'selectSingle',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'classPattern' => 'select_action quotaSelect',
                'defaultOption' => array('label' => 'Select Action', 'value' => ''),
                'hideIfEmpty' => true,
                'hideIfCallback' => 'isNonWorkingDay',
                'options' => array($leaveRequestService, 'getLeaveActions', array(self::RECORD, self::$loggedInEmpNumber)),
                'namePattern' => 'select_leave_action_{id}',
                'idPattern' => 'select_leave_action_{id}',
                'hasHiddenField' => true,
                'hiddenFieldName' => '{eimId}-{leaveTypeId}',
                'hiddenFieldId' => '{eimId}-{leaveTypeId}',
                'hiddenFieldValueGetter' => 'getDetailedLeaveListQuotaHolderValue',
                'hiddenFieldClass' => 'quotaHolder',
                'placeholderGetters' => array(
                    'id' => 'getId',
                    'eimId' => 'getEmpNumber',
                    'leaveTypeId' => 'getLeaveTypeId',
                ),
            ),
        ));

        $this->headers = array($header1, $header3, $header8, $header4, $header5, $header6, $header7);
    }
    
    public function getClassName() {
        return 'Leave';
    }

    public static function setListMode($listMode) {
        self::$listMode = $listMode;
    }
    
    public static function setLoggedInEmpNumber($empNumber) {
        self::$loggedInEmpNumber = $empNumber;
    }    
}
