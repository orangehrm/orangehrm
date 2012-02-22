<?php

class DetailedLeaveListConfigurationFactory extends ohrmListConfigurationFactory {
    
    protected static $listMode;

    public function init() {
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        
        $header1 = new ListHeader();
        $header3 = new ListHeader();
        $header4 = new ListHeader();
        $header5 = new ListHeader();
        $header6 = new ListHeader();
        $header7 = new ListHeader();
        $header8 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Date',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getFormattedLeaveDateToView'),
        ));

        $header3->populateFromArray(array(
            'name' => 'Leave Type',
            'width' => '15%',
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
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array('getter' => 'getLeaveBalance'),
        ));

        
        $header4->populateFromArray(array(
            'name' => 'Duration (Hours)',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array('getter' => 'getLeaveLengthHours', 'hideIfCallback' => 'isNonWorkingDay'),
        ));

        $header5->populateFromArray(array(
            'name' => 'Status',
            'width' => '10%',
            'isSortable' => false,
            'elementType' => 'label',
            'filters' => array('I18nCellFilter' => array()
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
                    'id' => 'getLeaveId',
                ),
            ),
        ));

        $header6->populateFromArray(array(
            'name' => 'Comments',
            'width' => '20%',
            'isSortable' => false,
            'elementType' => 'comment',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'getter' => 'getLeaveComments',
                'idPattern' => 'hdnLeaveComment-{id}',
                'namePattern' => 'leaveComments[{id}]',
                'placeholderGetters' => array('id' => 'getLeaveId'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'leave[{id}]',
                'hiddenFieldId' => 'hdnLeave_{id}',
                'hiddenFieldValueGetter' => 'getLeaveId',
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
                'options' => array($leaveRequestService, 'getLeaveActions', array(self::RECORD, self::$userId, self::$listMode)),
                'namePattern' => 'select_leave_action_{id}',
                'idPattern' => 'select_leave_action_{id}',
                'hasHiddenField' => true,
                'hiddenFieldName' => '{eimId}-{leaveTypeId}-{leavePeriodId}',
                'hiddenFieldId' => '{eimId}-{leaveTypeId}-{leavePeriodId}',
                'hiddenFieldValueGetter' => 'getDetailedLeaveListQuotaHolderValue',
                'hiddenFieldClass' => 'quotaHolder',
                'placeholderGetters' => array(
                    'id' => 'getLeaveId',
                    'eimId' => 'getEmployeeId',
                    'leaveTypeId' => 'getLeaveTypeId',
                    'leavePeriodId' => 'getLeavePeriodId',
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
}
